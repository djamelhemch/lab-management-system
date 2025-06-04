<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)  
    {  
        Log::info('Index method called', [  
            'all_request_data' => $request->all(),  
            'q' => $request->input('q'),  
            'category_analyse_id' => $request->input('category_analyse_id'),  
            'q_filled' => $request->filled('q'),  
            'category_filled' => $request->filled('category_analyse_id')  
        ]);  
  
        $params = [];  
        if ($request->filled('q')) {  
            $params['q'] = $request->input('q');  
        }  
        if ($request->filled('category_analyse_id')) {  
            $params['category_analyse_id'] = (int) $request->input('category_analyse_id');  
        }  
  
        Log::info('Fetching analyses from API', [  
            'endpoint' => 'analyses',  
            'params' => $params  
        ]);  
  
        $analysesResponse = $this->api->get('analyses', $params);  
  
        Log::info('API Response received', [  
            'status' => $analysesResponse->status(),  
            'successful' => $analysesResponse->successful(),  
            'headers' => $analysesResponse->headers(),  
        ]);  
  
        if (!$analysesResponse->successful()) {  
            Log::error('API analyses fetch failed', [  
                'status' => $analysesResponse->status(),  
                'body' => $analysesResponse->body(),  
                'json' => $analysesResponse->json()  
            ]);  
        }  
  
        $analyses = $analysesResponse->successful() ? $analysesResponse->json() : [];  
  
        // Fetch categories  
        Log::info('Fetching categories from API');  
        $categoriesResponse = $this->api->get('analyses/category-analyse');  
  
        if (!$categoriesResponse->successful()) {  
            Log::error('API categories fetch failed', [  
                'status' => $categoriesResponse->status(),  
                'body' => $categoriesResponse->body()  
            ]);  
        }  
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];  
  
        Log::info('Returning view', [  
            'analyses_count' => count($analyses),  
            'categories_count' => count($categories)  
        ]);  
  
        return view('analyses.index', compact('analyses', 'categories'));  
    }
    
   public function table(Request $request)  
    {  
        Log::info('Table method called', [  
            'all_request_data' => $request->all(),  
            'q' => $request->input('q'),  
            'category_analyse_id' => $request->input('category_analyse_id'),  
            'q_filled' => $request->filled('q'),  
            'category_filled' => $request->filled('category_analyse_id')  
        ]);  
  
        $params = [];  
        if ($request->filled('q')) {  
            $params['q'] = $request->input('q');  
        }  
        if ($request->filled('category_analyse_id')) {  
            $params['category_analyse_id'] = (int) $request->input('category_analyse_id');  
        }  
        
        $analysesResponse = $this->api->get('analyses/table', $params); 
  
        Log::info('API Response received (table)', [  
            'status' => $analysesResponse->status(),  
            'successful' => $analysesResponse->successful(),  
        ]);  
  
        if (!$analysesResponse->successful()) {  
            Log::error('API analyses fetch (table) failed', [  
                'status' => $analysesResponse->status(),  
                'body' => $analysesResponse->body(),  
                'json' => $analysesResponse->json()  
            ]);  
        }  
  
        $analyses = $analysesResponse->successful() ? $analysesResponse->json() : [];  
  
        Log::info('Returning table partial', [  
            'analyses_count' => count($analyses)  
        ]);  
  
        return view('analyses.partials.table', compact('analyses'))->render();  
    }

   
    public function create()  
    {  
        $categories = $this->api->get('analyses/category-analyse')->json();  
        $sampleTypes = $this->api->get('analyses/sample-types')->json();  
        $units = $this->api->get('analyses/units')->json();  
    
        return view('analyses.create', compact('categories', 'sampleTypes', 'units'));  
    }

    public function store(Request $request)    
    {    
        $validator = Validator::make($request->all(), [    
            'name' => 'required|string|max:100',    
            'code' => 'nullable|string|max:20',    
            'category_analyse_id' => 'nullable|integer',    
            'unit_id' => 'nullable|integer',    
            'sample_type_id' => 'nullable|integer',    
            'sex_applicable' => 'in:M,F,All',    
            'age_min' => 'nullable|integer',    
            'age_max' => 'nullable|integer',    
            'pregnant_applicable' => 'nullable|boolean',    
            'normal_min' => 'nullable|numeric',    
            'normal_max' => 'nullable|numeric',    
            'formula' => 'nullable|string',    
            'price' => 'required|numeric|min:0',    
        ]);    
    
        if ($validator->fails()) {    
            return redirect()->back()->withErrors($validator)->withInput();    
        }    
    
        // Handle temporary categories, sample types, and units  
        $categoryId = $this->handleTempCategory($request);  
        $sampleTypeId = $this->handleTempSampleType($request);  
        $unitId = $this->handleTempUnit($request);  
    
        $data = $request->only([    
            'name', 'code', 'sex_applicable', 'age_min', 'age_max', 'pregnant_applicable',    
            'normal_min', 'normal_max', 'formula', 'price'    
        ]);    
    
        // Use the resolved IDs  
        $data['category_analyse_id'] = $categoryId ?: $request->category_analyse_id;  
        $data['sample_type_id'] = $sampleTypeId ?: $request->sample_type_id;  
        $data['unit_id'] = $unitId ?: $request->unit_id;  
    
        $response = $this->api->post('analyses', $data);    
    
        if ($response->successful()) {    
            return redirect()->route('analyses.index')->with('success', 'Analysis created successfully.');    
        } else {    
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
            abort(404, 'Analysis not found');  
        }  
        return view('analyses.show', compact('analysis'));  
    }

    public function edit($id)  
    {  
        $analysis = $this->api->get("analyses/{$id}")->json();  
        $categories = $this->api->get('analyses/category-analyse')->json();  
        $sampleTypes = $this->api->get('analyses/sample-types')->json();  
        $units = $this->api->get('analyses/units')->json();  
        if (!$analysis) {
            abort(404); // Or handle the error as you see fit
        }
  
        return view('analyses.edit', compact('analysis', 'categories', 'sampleTypes', 'units'));  
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'category_analyse_id' => 'required|integer|exists:category_analyse,id',
            'unit_id' => 'required|integer|exists:units,id',
            'sex_applicable' => 'in:M,F,All',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
            'pregnant_applicable' => 'nullable|boolean',
            'sample_type_id' => 'required|integer|exists:sample_types,id',
            'normal_min' => 'nullable|numeric',
            'normal_max' => 'nullable|numeric',
            'formula' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'code', 'category_analyse_id', 'unit_id', 'sex_applicable',
            'age_min', 'age_max', 'pregnant_applicable', 'sample_type_id',
            'normal_min', 'normal_max', 'formula', 'price', 'is_active'
        ]);

        $response = $this->api->put("analyses/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('analyses.index')->with('success', 'Analysis updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update analysis.')->withInput();
        }
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