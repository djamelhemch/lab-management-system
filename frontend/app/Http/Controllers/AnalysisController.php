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
        $params = $request->only(['category_analyse_id', 'q', 'limit']);
        $response = $this->api->get('analyses', $params);
        $analyses = $response->successful() ? $response->json() : [];

        $categoriesResponse = $this->api->get('analyses/categories');
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('analyses.index', compact('analyses', 'categories'));
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
    
        $data = $request->only([  
            'name', 'code', 'category_analyse_id', 'unit_id', 'sample_type_id',  
            'sex_applicable', 'age_min', 'age_max', 'pregnant_applicable',  
            'normal_min', 'normal_max', 'formula', 'price'  
        ]);  
    
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
        $analysis = $response->successful() ? $response->json() : null;

        if (!$analysis) {
            abort(404); // Or handle the error as you see fit
        }

        return view('analyses.show', compact('analysis'));
    }

    public function edit($id)  
    {  
        $analysis = $this->api->get("analyses/{$id}")->json();  
        $categories = $this->api->get('analyses/category-analyse')->json();  
        $sampleTypes = $this->api->get('analyses/sample-types')->json();  
        $units = $this->api->get('analyses/units')->json();  
    
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
    public function storeCategory(Request $request)  
{  
    try {  
        $validator = Validator::make($request->all(), [  
            'name' => 'required|string|max:50|unique:category_analyse,name',  
        ]);  
  
        if ($validator->fails()) {  
            return response()->json(['errors' => $validator->errors()], 422);  
        }  
  
        $category = $this->api->post('analyses/category-analyse', ['name' => $request->input('name')]);  
  
        if ($category->successful()) {  
            return response()->json($category->json(), 201);  
        } else {  
            Log::error('Failed to create category: ' . $category->body());  
            return response()->json(['message' => 'Failed to create category'], $category->status());  
        }  
    } catch (\Exception $e) {  
        Log::error('Exception creating category: ' . $e->getMessage());  
        return response()->json(['message' => 'Error creating category'], 500);  
    }  
}  
  
public function storeSampleType(Request $request)  
{  
    try {  
        $validator = Validator::make($request->all(), [  
            'name' => 'required|string|max:50|unique:sample_types,name',  
        ]);  
  
        if ($validator->fails()) {  
            return response()->json(['errors' => $validator->errors()], 422);  
        }  
  
        $sampleType = $this->api->post('analyses/sample-types', ['name' => $request->input('name')]);  
  
        if ($sampleType->successful()) {  
            return response()->json($sampleType->json(), 201);  
        } else {  
            Log::error('Failed to create sample type: ' . $sampleType->body());  
            return response()->json(['message' => 'Failed to create sample type'], $sampleType->status());  
        }  
    } catch (\Exception $e) {  
        Log::error('Exception creating sample type: ' . $e->getMessage());  
        return response()->json(['message' => 'Error creating sample type'], 500);  
    }  
}  
  
public function storeUnit(Request $request)  
{  
    try {  
        $validator = Validator::make($request->all(), [  
            'name' => 'required|string|max:20|unique:units,name',  
        ]);  
  
        if ($validator->fails()) {  
            return response()->json(['errors' => $validator->errors()], 422);  
        }  
  
        $unit = $this->api->post('analyses/units', ['name' => $request->input('name')]);  
  
        if ($unit->successful()) {  
            return response()->json($unit->json(), 201);  
        } else {  
            Log::error('Failed to create unit: ' . $unit->body());  
            return response()->json(['message' => 'Failed to create unit'], $unit->status());  
        }  
    } catch (\Exception $e) {  
        Log::error('Exception creating unit: ' . $e->getMessage());  
        return response()->json(['message' => 'Error creating unit'], 500);  
    }  
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