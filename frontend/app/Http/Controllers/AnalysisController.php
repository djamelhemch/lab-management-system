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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'category_analyse_id' => 'nullable|integer',
            'unit_id' => 'nullable|integer',
            'sample_type_id' => 'nullable|integer',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'formula' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'tube_type' => 'nullable|string|max:50',
            'device_ids' => 'nullable|array',
            'device_ids.*' => 'integer',

            'normal_ranges' => 'array',
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle temporary entities
        $categoryId = $this->handleTempCategory($request);
        $sampleTypeId = $this->handleTempSampleType($request);
        $unitId = $this->handleTempUnit($request);
        $normalRangesInput = $request->input('normal_ranges', []);

        $normalRanges = collect($normalRangesInput)->map(function ($range) {
            // Helper: null-safe int
            $yMin = isset($range['age_min_years']) ? (int)$range['age_min_years'] : 0;
            $mMin = isset($range['age_min_months']) ? (int)$range['age_min_months'] : 0;
            $dMin = isset($range['age_min_days']) ? (int)$range['age_min_days'] : 0;

            $yMax = isset($range['age_max_years']) ? (int)$range['age_max_years'] : 0;
            $mMax = isset($range['age_max_months']) ? (int)$range['age_max_months'] : 0;
            $dMax = isset($range['age_max_days']) ? (int)$range['age_max_days'] : 0;

            // Simple conversion: 1 year = 365 days, 1 month = 30 days
            $ageMinDays = ($yMin * 365) + ($mMin * 30) + $dMin;
            $ageMaxDaysRaw = ($yMax * 365) + ($mMax * 30) + $dMax;

            // If all max parts are empty, treat as "no upper bound"
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
        // Prepare data
        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'formula' => $request->formula,
            'price' => $request->price,
            'category_analyse_id' => $categoryId ?: $request->category_analyse_id,
            'sample_type_id' => $sampleTypeId ?: $request->sample_type_id,
            'unit_id' => $unitId ?: $request->unit_id,
            'tube_type' => $request->tube_type,
            'device_ids' => $request->input('device_ids', []), // ✅ always array
            'normal_ranges' => $normalRanges,
            'is_active' => $request->is_active ?? 1,
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
        // ✅ Ensure FastAPI receives proper JSON
        $response = $this->api->post('analyses', $data, ['json' => true]);
            // ✅ Log the response
        Log::info('FastAPI Response Status: ' . $response->status());
        Log::info('FastAPI Response Body: ' . $response->body());
        if ($response->successful()) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Analysis created successfully.',
                'redirect' => route('analyses.index'),
                'clear_autosave' => true,
            ], 201);
        }

        return redirect()->route('analyses.index')
            ->with('success', 'Analysis created successfully.')
            ->with('clear_autosave', true);
        } else {
            if ($request->expectsJson()) {
                Log::error('FastAPI Error: ' . $response->status());
                Log::error('FastAPI Error Body: ' . $response->body());
                return response()->json([
                    'message' => 'Failed to create analysis.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to create analysis.')->withInput();
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

        // Default fallbacks
        $analysis['device_names'] = $analysis['device_names'] ?? [];
        $analysis['tube_type'] = $analysis['tube_type'] ?? 'Not specified';
        $analysis['formatAge'] = function($days) {
        if ($days === 0) return 'Nouveau-né';
            
            $years = floor($days / 365);
            $months = floor(($days % 365) / 30);
            $daysRemain = $days % 30;
            
            $parts = [];
            if ($years > 0) $parts[] = $years . ($years == 1 ? ' yr' : ' yrs');
            if ($months > 0) $parts[] = $months . ($months == 1 ? ' mo' : ' mos');
            if ($daysRemain > 0 || empty($parts)) $parts[] = $daysRemain . ($daysRemain == 1 ? ' day' : ' days');
            
            return implode(' ', $parts);
        };

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
        'sample_type_id' => 'nullable|integer',
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

    // Process normal ranges
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

    // ✅ FIX: Proper is_active handling
    $isActive = $request->has('is_active') ? 1 : 0;
    
    Log::info('is_active checkbox present: ' . ($request->has('is_active') ? 'YES' : 'NO'));
    Log::info('is_active value being set: ' . $isActive);

    // Prepare data
    $data = [
        'name' => $request->name,
        'code' => $request->code,
        'formula' => $request->formula,
        'price' => $request->price,
        'category_analyse_id' => $categoryId ?: $request->category_analyse_id,
        'sample_type_id' => $sampleTypeId ?: ($request->sample_type_id ?: null),
        'unit_id' => $unitId ?: $request->unit_id,
        'tube_type' => $request->tube_type,
        'device_ids' => $request->input('device_ids', []),
        'normal_ranges' => $normalRanges,
        'is_active' => $isActive, // ✅ Use the properly processed value
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
        Log::info('✅ Update successful, redirecting to edit page');
        
        // ✅ FIX: Redirect to EDIT page to show toast
        return redirect()->route('analyses.index')
        ->with('success', '✅ Analyse mis à jour avec succès!'); // Optional redirect after toast
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



    private function handleTempCategory($request)  
    {  
        if ($request->category_analyse_id < 0) { // Temporary category  
            $tempCategories = $request->temp_categories ?? [];  
            foreach ($tempCategories as $tempCategoryJson) {  
                $tempCategory = json_decode($tempCategoryJson, true);  
                if ($tempCategory['id'] == $request->category_analyse_id) {  
                    // Create the category via API  
                    $response = $this->api->post('analyses/category-analyse', ['name' => $tempCategory['name']]);  
                    if ($response->successful()) {  
                        $newCategory = $response->json();  
                        return $newCategory['id'];  
                    }  
                }  
            }  
        }  
        return null;  
    }  
    
    private function handleTempSampleType($request)  
    {  
        if ($request->sample_type_id < 0) { // Temporary sample type  
            $tempSampleTypes = $request->temp_sample_types ?? [];  
            foreach ($tempSampleTypes as $tempSampleTypeJson) {  
                $tempSampleType = json_decode($tempSampleTypeJson, true);  
                if ($tempSampleType['id'] == $request->sample_type_id) {  
                    // Create the sample type via API  
                    $response = $this->api->post('analyses/sample-types', ['name' => $tempSampleType['name']]);  
                    if ($response->successful()) {  
                        $newSampleType = $response->json();  
                        return $newSampleType['id'];  
                    }  
                }  
            }  
        }  
        return null;  
    }  
    
    private function handleTempUnit($request)  
    {  
        if ($request->unit_id < 0) { // Temporary unit  
            $tempUnits = $request->temp_units ?? [];  
            foreach ($tempUnits as $tempUnitJson) {  
                $tempUnit = json_decode($tempUnitJson, true);  
                if ($tempUnit['id'] == $request->unit_id) {  
                    // Create the unit via API  
                    $response = $this->api->post('analyses/units', ['name' => $tempUnit['name']]);  
                    if ($response->successful()) {  
                        $newUnit = $response->json();  
                        return $newUnit['id'];  
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