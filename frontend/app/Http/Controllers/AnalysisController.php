<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Session;
use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AnalysisController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    private function buildAnalysisParams(Request $request): array
    {
        $params = [];

        if ($request->filled('q')) {
            $params['q'] = $request->input('q');
        }

        if ($request->filled('category_analyse_id')) {
            $params['category_analyse_id'] = (int) $request->input('category_analyse_id');
        }

        $showAll = $request->input('show') === 'all';
        if (!$showAll) {
            // Only active when show!=all
            $params['is_active'] = 1;
        }
        if ($request->filled('sort')) {
            $params['sort'] = $request->input('sort');
        }
        if ($request->filled('direction')) {
            $params['direction'] = $request->input('direction');
        }
        return $params;
    }

    public function index(Request $request)
    {
        $params  = $this->buildAnalysisParams($request);
        $showAll = $request->input('show') === 'all';

        $analysesResponse = $this->api->get('analyses', $params);
        $analyses         = $analysesResponse->successful() ? $analysesResponse->json() : [];

        $categoriesResponse = $this->api->get('analyses/category-analyse');
        $categories         = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('analyses.index', compact('analyses', 'categories', 'showAll'));
    }

    public function table(Request $request)
    {
        $params  = $this->buildAnalysisParams($request);
        $showAll = $request->input('show') === 'all';

        $analysesResponse = $this->api->get('analyses/table', $params);
        $analyses         = $analysesResponse->successful() ? $analysesResponse->json() : [];

        return view('analyses.partials.table', compact('analyses', 'showAll'))->render();
    }

   public function create()
    {
        $categories = $this->api->get('analyses/category-analyse')->json();
        $sampleTypes = $this->api->get('analyses/sample-types')->json();
        $units = $this->api->get('analyses/units')->json();
        $devices = $this->api->get('/lab-devices')->json() ?? [];

        // ✅ Add this line:
        $analyses = $this->api->get('analyses')->json() ?? [];

        return view('analyses.create', compact('categories', 'sampleTypes', 'units', 'devices', 'analyses'));
    }

    public function store(Request $request)
    {
        // ✅ Enhanced logging
        Log::info('=== STORE REQUEST START ===', [
            'method' => $request->method(),
            'is_json' => $request->isJson(),
            'expects_json' => $request->expectsJson(),
            'content_type' => $request->header('Content-Type'),
        ]);
        
        Log::info('Raw Input Data:', $request->all());

        // ✅ Custom validation rule BEFORE validator
        Validator::extend('integer_or_temp_id', function ($attribute, $value, $parameters, $validator) {
            if (is_int($value) || (is_string($value) && ctype_digit($value))) return true;
            if (is_string($value) && preg_match('/^-?\d+$/', $value)) return true;
            if (is_string($value) && strpos($value, 'temp_') === 0) return true;
            return false;
        });

        // ✅ Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'category_analyse_id' => 'nullable|integer_or_temp_id',
            'unit_id' => 'nullable|integer_or_temp_id',
            'sample_type_ids' => 'nullable|array',
            'sample_type_ids.*' => 'required|integer_or_temp_id',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'formula' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'tube_type' => 'nullable|string|max:50',
            'device_ids' => 'nullable|array',
            'device_ids.*' => 'integer',
            'normal_ranges' => 'nullable|array',
            'normal_ranges.*.sex_applicable' => 'required|in:M,F,All',
            'normal_ranges.*.age_min_years' => 'nullable|integer|min:0',
            'normal_ranges.*.age_min_months' => 'nullable|integer|min:0|max:11',
            'normal_ranges.*.age_min_days' => 'nullable|integer|min:0|max:30',
            'normal_ranges.*.age_max_years' => 'nullable|integer|min:0',
            'normal_ranges.*.age_max_months' => 'nullable|integer|min:0|max:11',
            'normal_ranges.*.age_max_days' => 'nullable|integer|min:0|max:30',
            'normal_ranges.*.normal_min' => 'nullable|numeric',
            'normal_ranges.*.normal_max' => 'nullable|numeric',
            'normal_ranges.*.pregnant_applicable' => 'nullable|boolean',
            'temp_sample_types' => 'nullable|string',
            'temp_categories' => 'nullable|array',
            'temp_units' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            Log::error('❌ Validation Failed:', ['errors' => $validator->errors()->toArray()]);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Validation échouée',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '❌ Veuillez corriger les erreurs du formulaire');
        }

        Log::info('✅ Validation passed');

        try {
            // ✅ STEP 1: Process temp sample types FIRST
            $finalSampleTypeIds = $this->handleTempSampleTypes($request);
            Log::info('✅ Processed sample type IDs:', ['ids' => $finalSampleTypeIds]);

            // ✅ STEP 2: Handle category and unit
            $categoryId = $this->handleTempCategory($request);
            $unitId = $this->handleTempUnit($request);

            // ✅ STEP 3: Process normal ranges
            $normalRangesInput = $request->input('normal_ranges', []);
            $normalRanges = collect($normalRangesInput)->map(function ($range) {
                $yMin = isset($range['age_min_years']) ? (int)$range['age_min_years'] : 0;
                $mMin = isset($range['age_min_months']) ? (int)$range['age_min_months'] : 0;
                $dMin = isset($range['age_min_days']) ? (int)$range['age_min_days'] : 0;

                $yMax = isset($range['age_max_years']) ? (int)$range['age_max_years'] : 0;
                $mMax = isset($range['age_max_months']) ? (int)$range['age_max_months'] : 0;
                $dMax = isset($range['age_max_days']) ? (int)$range['age_max_days'] : 0;

                $ageMinDays = ($yMin * 365) + ($mMin * 30) + $dMin;
                $ageMaxDaysRaw = ($yMax * 365) + ($mMax * 30) + $dMax;

                $hasMax = $yMax || $mMax || $dMax;
                $ageMaxDays = $hasMax ? $ageMaxDaysRaw : null;

                return [
                    'sex_applicable' => $range['sex_applicable'] ?? 'All',
                    'age_min' => $ageMinDays > 0 ? $ageMinDays : null,
                    'age_max' => $ageMaxDays,
                    'normal_min' => isset($range['normal_min']) && $range['normal_min'] !== ''
                        ? (float)$range['normal_min']
                        : null,
                    'normal_max' => isset($range['normal_max']) && $range['normal_max'] !== ''
                        ? (float)$range['normal_max']
                        : null,
                    'pregnant_applicable' => !empty($range['pregnant_applicable']),
                ];
            })->toArray();

            // ✅ User session check
            $user = session('user');
            if (!$user || empty($user['id'])) {
                $errorMsg = 'Session expirée. Veuillez vous reconnecter.';
                Log::error('❌ User session missing');
                
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 401);
                }
                
                return back()->with('error', $errorMsg);
            }

            // ✅ STEP 4: Prepare final data with proper null handling
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'formula' => $request->formula,
                'price' => (float) $request->price,
                'category_analyse_id' => $categoryId ?: ($request->category_analyse_id ? (int)$request->category_analyse_id : null),
                'sample_type_ids' => $finalSampleTypeIds,
                'unit_id' => $unitId ?: ($request->unit_id ? (int)$request->unit_id : null),
                'tube_type' => $request->tube_type,
                'device_ids' => $request->input('device_ids', []),
                'normal_ranges' => $normalRanges,
                'is_active' => $request->is_active ?? 1,
                'user_id' => $user['id'],
            ];

            Log::info('=== PAYLOAD TO FASTAPI ===', $data);

            // ✅ Send to FastAPI
            $response = $this->api->post('analyses', $data, ['json' => true]);

            Log::info('FastAPI Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '✅ Analyse créée avec succès!',
                        'redirect' => route('analyses.index'),
                        'clear_autosave' => true,
                    ], 201);
                }

                return redirect()->route('analyses.index')
                    ->with('success', '✅ Analyse créée avec succès!')
                    ->with('clear_autosave', true);
            } else {
                // ✅ Handle API errors
                $statusCode = $response->status();
                $responseBody = $response->json();
                
                Log::error('❌ FastAPI Error:', [
                    'status' => $statusCode,
                    'body' => $responseBody
                ]);
                
                $errorMessage = '❌ Échec de la création de l\'analyse.';
                $errorDetail = null;
                
                if (isset($responseBody['detail'])) {
                    $errorDetail = is_string($responseBody['detail']) 
                        ? $responseBody['detail'] 
                        : json_encode($responseBody['detail'], JSON_PRETTY_PRINT);
                }
                
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'detail' => $errorDetail,
                        'status' => $statusCode
                    ], $statusCode);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage)
                    ->with('error_detail', $errorDetail);
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Exception in store:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Erreur serveur: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Erreur serveur: ' . $e->getMessage());
        }
    }

    public function show($id)  
    {
        $response = $this->api->get("analyses/{$id}");

        if (!$response->successful()) {
            abort(404, 'Analysis not found');
        }

        $analysis = $response->json();

        if (!is_array($analysis) || !isset($analysis['id'])) {
            abort(404, 'Invalid analysis data');
        }

        // ✅ Set default fallbacks
        $analysis['device_names'] = $analysis['device_names'] ?? [];
        $analysis['tube_type'] = $analysis['tube_type'] ?? null;
        $analysis['sample_types'] = $analysis['sample_types'] ?? [];
        
        if (!isset($analysis['sample_type_ids']) && !empty($analysis['sample_types'])) {
            $analysis['sample_type_ids'] = array_column($analysis['sample_types'], 'id');
        }

        return view('analyses.show', compact('analysis'));
    }

    public function edit($id)  
{  
    $analysis = $this->api->get("analyses/{$id}")->json();  
    $categories = $this->api->get('analyses/category-analyse')->json();  
    $sampleTypes = $this->api->get('analyses/sample-types')->json();  
    $units = $this->api->get('analyses/units')->json();  
    
    // ✅ Add analyses list for formula builder
    $analyses = $this->api->get('analyses')->json() ?? [];
    
    if (!$analysis) {
        abort(404);
    }

    return view('analyses.edit', compact('analysis', 'categories', 'sampleTypes', 'units', 'analyses'));  
}

public function update(Request $request, $id)
{
    Log::info('=== UPDATE REQUEST STARTED ===');
    Log::info('Analysis ID: ' . $id);
    Log::info('Request Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
    
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:100',
        'code' => 'nullable|string|max:20',
        'category_analyse_id' => 'nullable|integer',
        'unit_id' => 'nullable|integer',
        
        // ✅ UPDATED: Support both old and new
        'sample_type_id' => 'nullable|integer',
        'sample_type_ids' => 'nullable|array',
        'sample_type_ids.*' => 'integer',
        
        'formula' => 'nullable|string',
        'formula_name' => 'nullable|string|max:255',
        'price' => 'required|numeric|min:0',
        'tube_type' => 'nullable|string|max:50',
        'is_active' => 'nullable|boolean',
        'device_ids' => 'nullable|array',
        'device_ids.*' => 'integer',
        'normal_ranges' => 'array',
        'normal_ranges.*.sex_applicable' => 'required|in:M,F,All',
        'normal_ranges.*.age_min_years' => 'nullable|integer|min:0',
        'normal_ranges.*.age_min_months' => 'nullable|integer|min:0|max:11',
        'normal_ranges.*.age_min_days' => 'nullable|integer|min:0',
        'normal_ranges.*.age_max_years' => 'nullable|integer|min:0',
        'normal_ranges.*.age_max_months' => 'nullable|integer|min:0|max:11',
        'normal_ranges.*.age_max_days' => 'nullable|integer|min:0',
        'normal_ranges.*.normal_min' => 'nullable|numeric',
        'normal_ranges.*.normal_max' => 'nullable|numeric',
        'normal_ranges.*.pregnant_applicable' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation failed: ' . json_encode($validator->errors()));
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Handle temporary entities
    $categoryId = $this->handleTempCategory($request);
    $sampleTypeId = $this->handleTempSampleType($request);
    $unitId = $this->handleTempUnit($request);

    // ✅ Handle sample_type_ids (prioritize array over single)
    $sampleTypeIds = $request->input('sample_type_ids', []);
    
    // Fallback: If using old single select, convert to array
    if (empty($sampleTypeIds) && $request->sample_type_id) {
        $sampleTypeIds = [$request->sample_type_id];
    }
    
    // If created a new temp sample type, add it
    if ($sampleTypeId && !in_array($sampleTypeId, $sampleTypeIds)) {
        $sampleTypeIds[] = $sampleTypeId;
    }

    Log::info('Sample Type IDs being sent: ' . json_encode($sampleTypeIds));

    // Process normal ranges (unchanged)
    $normalRangesInput = $request->input('normal_ranges', []);
    
    $normalRanges = collect($normalRangesInput)->map(function ($range) {
        $yMin = isset($range['age_min_years']) ? (int)$range['age_min_years'] : 0;
        $mMin = isset($range['age_min_months']) ? (int)$range['age_min_months'] : 0;
        $dMin = isset($range['age_min_days']) ? (int)$range['age_min_days'] : 0;

        $yMax = isset($range['age_max_years']) ? (int)$range['age_max_years'] : 0;
        $mMax = isset($range['age_max_months']) ? (int)$range['age_max_months'] : 0;
        $dMax = isset($range['age_max_days']) ? (int)$range['age_max_days'] : 0;

        $ageMinDays = ($yMin * 365) + ($mMin * 30) + $dMin;
        $ageMaxDaysRaw = ($yMax * 365) + ($mMax * 30) + $dMax;

        $hasMax = $yMax || $mMax || $dMax;
        $ageMaxDays = $hasMax ? $ageMaxDaysRaw : null;

        return [
            'sex_applicable' => $range['sex_applicable'] ?? 'All',
            'age_min' => $ageMinDays > 0 ? $ageMinDays : null,
            'age_max' => $ageMaxDays,
            'normal_min' => isset($range['normal_min']) && $range['normal_min'] !== ''
                ? (float)$range['normal_min']
                : null,
            'normal_max' => isset($range['normal_max']) && $range['normal_max'] !== ''
                ? (float)$range['normal_max']
                : null,
            'pregnant_applicable' => !empty($range['pregnant_applicable']),
        ];
    })->toArray();

    // ✅ Proper is_active handling
    $isActive = $request->has('is_active') ? 1 : 0;
    
    Log::info('is_active checkbox present: ' . ($request->has('is_active') ? 'YES' : 'NO'));
    Log::info('is_active value being set: ' . $isActive);

    // ✅ Prepare data with sample_type_ids
    $data = [
        'name' => $request->name,
        'code' => $request->code,
        'formula' => $request->formula,
        'price' => $request->price,
        'category_analyse_id' => $categoryId ?: $request->category_analyse_id,
        'sample_type_ids' => $sampleTypeIds, // ✅ Send array
        'unit_id' => $unitId ?: $request->unit_id,
        'tube_type' => $request->tube_type,
        'device_ids' => $request->input('device_ids', []),
        'normal_ranges' => $normalRanges,
        'is_active' => $isActive,
    ];

    // User
    $user = session('user');
    if (!$user || empty($user['id'])) {
        return back()->with('error', 'User session expired, please log in again.');
    }
    $data['user_id'] = $user['id'];

    Log::info('=== SENDING TO FASTAPI ===');
    Log::info('Data: ' . json_encode($data, JSON_PRETTY_PRINT));
    Log::info('=== END PAYLOAD ===');

    // Send to FastAPI
    $response = $this->api->put("analyses/{$id}", $data, ['json' => true]);

    Log::info('FastAPI Response Status: ' . $response->status());
    Log::info('FastAPI Response Body: ' . $response->body());

    if ($response->successful()) {
        Log::info('✅ Update successful, redirecting to index');
        
        return redirect()->route('analyses.index')
            ->with('success', '✅ Analyse mis à jour avec succès!');
    } else {
        $errorMessage = '❌ Échec de la mise à jour de l\'analyse.';
        
        if ($response->status() === 422) {
            $errors = $response->json();
            if (isset($errors['detail'])) {
                $errorDetails = is_array($errors['detail']) 
                    ? json_encode($errors['detail']) 
                    : $errors['detail'];
                
                Log::error('Validation error from FastAPI: ' . $errorDetails);
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->with('warning', $errorDetails)
                    ->withInput();
            }
        }
        
        Log::error('FastAPI Error: ' . $response->status());
        Log::error('FastAPI Error Body: ' . $response->body());
        
        return redirect()->back()
            ->with('error', $errorMessage . ' Veuillez vérifier les logs.')
            ->withInput();
    }
}

    public function labFormulas()
    {   
        $response = $this->api->get('lab-formulas'); // Calls FastAPI GET /lab-formulas
        
        if ($response->successful()) {
            return response()->json($response->json());
        }
        
        return response()->json([
            'error' => 'Failed to fetch formulas'
        ], $response->status());
    }
    public function createFormula(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'formula' => 'required|string',
        ]);

        // Pass the authenticated Laravel user's token to FastAPI
        $response = $this->api->post('lab-formulas', [
            'name' => $validated['name'],
            'formula' => $validated['formula'],
        ]);
        Log::info('createFormula Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        if ($response->successful()) {
            return response()->json($response->json(), 201);
        }
        
        return response()->json([
            'error' => 'Failed to create formula',
            'message' => $response->json()['detail'] ?? 'Unknown error'
        ], $response->status());
    }
    public function deleteFormula($id)
    {
        $response = $this->api->delete("lab-formulas/{$id}");
        
        if ($response->successful()) {
            return response()->json(['message' => 'Formula deleted successfully']);
        }
        
        return response()->json([
            'error' => 'Failed to delete formula'
        ], $response->status());
    }



        private function handleTempSampleTypes($request) 
    { 
        $sampleTypeIds = $request->input('sample_type_ids', []);
        
        Log::info('🔍 handleTempSampleTypes - Raw IDs:', ['ids' => $sampleTypeIds]);
        
        // Parse temp sample types JSON
        $tempJson = $request->input('temp_sample_types', '');
        Log::info('🔍 Raw temp_sample_types JSON:', ['json' => $tempJson]);
        
        $tempSampleTypes = [];
        if (!empty($tempJson) && $tempJson !== '[]' && $tempJson !== 'null') {
            $decoded = json_decode($tempJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tempSampleTypes = $decoded;
                Log::info('✅ Decoded temp sample types:', ['data' => $tempSampleTypes]);
            } else {
                Log::error('❌ Invalid temp_sample_types JSON:', [
                    'json' => $tempJson,
                    'error' => json_last_error_msg()
                ]);
            }
        }
        
        $processedIds = [];
        foreach ($sampleTypeIds as $sampleTypeId) {
            if (is_string($sampleTypeId) && strpos($sampleTypeId, 'temp_') === 0) {
                Log::info('🔄 Processing temp ID:', ['id' => $sampleTypeId]);
                
                $found = false;
                foreach ($tempSampleTypes as $tempSampleType) {
                    if (isset($tempSampleType['id']) && $tempSampleType['id'] === $sampleTypeId) {
                        Log::info('🎯 Found matching temp sample type:', ['name' => $tempSampleType['name']]);
                        
                        try {
                            $response = $this->api->post('analyses/sample-types', [
                                'name' => $tempSampleType['name']
                            ]);
                            
                            if ($response->successful()) {
                                $newSampleType = $response->json();
                                $newId = $newSampleType['id'];
                                $processedIds[] = $newId;
                                Log::info('✅ Created sample type:', ['temp_id' => $sampleTypeId, 'real_id' => $newId]);
                                $found = true;
                            } else {
                                Log::error('❌ API failed:', [
                                    'status' => $response->status(),
                                    'body' => $response->body()
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('❌ Exception creating sample type:', [
                                'message' => $e->getMessage()
                            ]);
                        }
                        break;
                    }
                }
                
                if (!$found) {
                    Log::warning('⚠️ Temp ID not found, skipping:', ['id' => $sampleTypeId]);
                }
            } else {
                // Regular ID
                $processedIds[] = (int)$sampleTypeId;
                Log::info('✅ Keeping regular ID:', ['id' => $sampleTypeId]);
            }
        }
        
        Log::info('✅ Final processed sample type IDs:', ['ids' => $processedIds]);
        return $processedIds;
    }

    // ✅ FIXED: Handle temp category
    private function handleTempCategory($request)
    {
        $categoryId = $request->input('category_analyse_id');
        
        // Check if it's a temp ID (string starting with "temp_" or negative number)
        if (is_string($categoryId) && strpos($categoryId, 'temp_') === 0) {
            Log::info('🔄 Processing temp category ID:', ['id' => $categoryId]);
            
            $tempCategories = $request->input('temp_categories', []);
            
            foreach ($tempCategories as $tempCategoryJson) {
                $tempCategory = is_string($tempCategoryJson) 
                    ? json_decode($tempCategoryJson, true) 
                    : $tempCategoryJson;
                    
                if (isset($tempCategory['id']) && $tempCategory['id'] == $categoryId) {
                    try {
                        $response = $this->api->post('analyses/category-analyse', [
                            'name' => $tempCategory['name']
                        ]);
                        
                        if ($response->successful()) {
                            $newCategory = $response->json();
                            Log::info('✅ Created category:', ['temp_id' => $categoryId, 'real_id' => $newCategory['id']]);
                            return $newCategory['id'];
                        }
                    } catch (\Exception $e) {
                        Log::error('❌ Failed to create category:', ['error' => $e->getMessage()]);
                    }
                }
            }
        }
        
        return null;
    }

    // ✅ FIXED: Handle temp unit
    private function handleTempUnit($request)
    {
        $unitId = $request->input('unit_id');
        
        // Check if it's a temp ID
        if (is_string($unitId) && strpos($unitId, 'temp_') === 0) {
            Log::info('🔄 Processing temp unit ID:', ['id' => $unitId]);
            
            $tempUnits = $request->input('temp_units', []);
            
            foreach ($tempUnits as $tempUnitJson) {
                $tempUnit = is_string($tempUnitJson) 
                    ? json_decode($tempUnitJson, true) 
                    : $tempUnitJson;
                    
                if (isset($tempUnit['id']) && $tempUnit['id'] == $unitId) {
                    try {
                        $response = $this->api->post('analyses/units', [
                            'name' => $tempUnit['name']
                        ]);
                        
                        if ($response->successful()) {
                            $newUnit = $response->json();
                            Log::info('✅ Created unit:', ['temp_id' => $unitId, 'real_id' => $newUnit['id']]);
                            return $newUnit['id'];
                        }
                    } catch (\Exception $e) {
                        Log::error('❌ Failed to create unit:', ['error' => $e->getMessage()]);
                    }
                }
            }
        }
        
        return null;
    }
    public function destroy($id)
    {
        $response = $this->api->delete("analyses/{$id}");

        if ($response->successful()) {
            return redirect()->route('analyses.index')->with('success', 'Analysis deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete analysis.');
        }
    }
}