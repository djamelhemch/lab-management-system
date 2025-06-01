<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;

class AnalysisController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $params = $request->only(['category', 'q', 'limit']);
        $response = $this->api->get('analyses', $params);
        $analyses = $response->successful() ? $response->json() : [];

        $categoriesResponse = $this->api->get('analyses/categories');
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('analyses.index', compact('analyses', 'categories'));
    }

    public function create()
    {
        $categoriesResponse = $this->api->get('analyses/categories');
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('analyses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:analyses', // Laravel validation, but API should also validate
            'category' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'sex_applicable' => 'in:M,F,All',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
            'pregnant_applicable' => 'nullable|boolean',
            'sample_type' => 'nullable|string|max:50',
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
            'name', 'code', 'category', 'unit', 'sex_applicable',
            'age_min', 'age_max', 'pregnant_applicable', 'sample_type',
            'normal_min', 'normal_max', 'formula', 'price', 'is_active'
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
        $response = $this->api->get("analyses/{$id}");
        $analysis = $response->successful() ? $response->json() : null;

        if (!$analysis) {
            abort(404);
        }

        $categoriesResponse = $this->api->get('analyses/categories');
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('analyses.edit', compact('analysis', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'sex_applicable' => 'in:M,F,All',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
            'pregnant_applicable' => 'nullable|boolean',
            'sample_type' => 'nullable|string|max:50',
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
            'name', 'code', 'category', 'unit', 'sex_applicable',
            'age_min', 'age_max', 'pregnant_applicable', 'sample_type',
            'normal_min', 'normal_max', 'formula', 'price', 'is_active'
        ]);

        $response = $this->api->put("analyses/{$id}", $data);

        if ($response->successful()) {
            return redirect()->route('analyses.index')->with('success', 'Analysis updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update analysis.')->withInput();
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