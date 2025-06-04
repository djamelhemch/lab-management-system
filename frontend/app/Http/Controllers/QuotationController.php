<?php  
// app/Http/Controllers/QuotationController.php  
  
namespace App\Http\Controllers;  
  
use Illuminate\Http\Request;  
use App\Services\ApiService;  
use Illuminate\Support\Facades\Validator;  
  
class QuotationController extends Controller  
{  
    protected $api;  
  
    public function __construct(ApiService $api)  
    {  
        $this->api = $api;  
    }  
  
    public function index()  
    {  
        $response = $this->api->get('quotations');  
        $quotations = $response->successful() ? $response->json() : [];  
  
        return view('quotations.index', compact('quotations'));  
    }  
  
    public function create()  
    {  
        // Get patients and analyses for the form  
        $patientsResponse = $this->api->get('patients');  
        $analysesResponse = $this->api->get('analyses');  
          
        $patients = $patientsResponse->successful() ? $patientsResponse->json() : [];  
        $analyses = $analysesResponse->successful() ? $analysesResponse->json() : [];  
  
        return view('quotations.create', compact('patients', 'analyses'));  
    }  
  
    public function store(Request $request)  
    {  
        $validator = Validator::make($request->all(), [  
            'patient_id' => 'required|integer',  
            'analyses' => 'required|array|min:1',  
            'analyses.*.analysis_id' => 'required|integer',  
            'analyses.*.price' => 'required|numeric|min:0',  
            'total' => 'required|numeric|min:0',  
        ]);  
  
        if ($validator->fails()) {  
            return redirect()  
                ->back()  
                ->withErrors($validator)  
                ->withInput();  
        }  
  
        $data = [  
            'patient_id' => $request->patient_id,  
            'status' => 'draft',  
            'total' => $request->total,  
            'analyses' => $request->analyses  
        ];  
  
        $response = $this->api->post('quotations', $data);  
  
        if ($response->successful()) {  
            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');  
        } else {  
            return redirect()->back()->with('error', 'Failed to create quotation.')->withInput();  
        }  
    }  
  
    public function show($id)  
    {  
        $quotationResponse = $this->api->get("quotations/{$id}");  
          
        if (!$quotationResponse->successful()) {  
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');  
        }  
          
        $quotation = $quotationResponse->json();  
          
        return view('quotations.show', compact('quotation'));  
    }  
  
    public function edit($id)  
    {  
        $quotationResponse = $this->api->get("quotations/{$id}");  
          
        if (!$quotationResponse->successful()) {  
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');  
        }  
          
        $quotation = $quotationResponse->json();  
          
        // Get patients and analyses for the form  
        $patientsResponse = $this->api->get('patients');  
        $analysesResponse = $this->api->get('analyses');  
          
        $patients = $patientsResponse->successful() ? $patientsResponse->json() : [];  
        $analyses = $analysesResponse->successful() ? $analysesResponse->json() : [];  
  
        return view('quotations.edit', compact('quotation', 'patients', 'analyses'));  
    }  
  
    public function update(Request $request, $id)  
    {  
        $validator = Validator::make($request->all(), [  
            'patient_id' => 'required|integer',  
            'analyses' => 'required|array|min:1',  
            'analyses.*.analysis_id' => 'required|integer',  
            'analyses.*.price' => 'required|numeric|min:0',  
            'total' => 'required|numeric|min:0',  
            'status' => 'required|in:draft,confirmed,converted',  
        ]);  
  
        if ($validator->fails()) {  
            return redirect()  
                ->back()  
                ->withErrors($validator)  
                ->withInput();  
        }  
  
        $data = [  
            'patient_id' => $request->patient_id,  
            'status' => $request->status,  
            'total' => $request->total,  
            'analyses' => $request->analyses  
        ];  
  
        $response = $this->api->put("quotations/{$id}", $data);  
  
        if ($response->successful()) {  
            return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');  
        } else {  
            return redirect()->back()->with('error', 'Failed to update quotation.')->withInput();  
        }  
    }  
  
    public function destroy($id)  
    {  
        $response = $this->api->delete("quotations/{$id}");  
  
        if ($response->successful()) {  
            return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');  
        } else {  
            return redirect()->back()->with('error', 'Failed to delete quotation.');  
        }  
    }  
  
    // AJAX endpoints for autocomplete  
    public function searchPatients(Request $request)  
    {  
        $query = $request->get('q', '');  
        $response = $this->api->get('patients/search', ['q' => $query]);  
          
        return $response->successful() ? $response->json() : [];  
    }  
  
    public function searchAnalyses(Request $request)  
    {  
        $query = $request->get('q', '');  
        $response = $this->api->get('analyses/search', ['q' => $query]);  
          
        return $response->successful() ? $response->json() : [];  
    }  
  
    public function quotationsTable(Request $request)  
    {  
        $params = [];  
        if ($request->filled('q')) {  
            $params['q'] = $request->input('q');  
        }  
        if ($request->filled('status')) {  
            $params['status'] = $request->input('status');  
        }  
  
        $quotationsResponse = $this->api->get('quotations/table', $params);  
  
        if (!$quotationsResponse->successful()) {  
            return response('Error fetching quotations', 500);  
        }  
  
        $quotations = $quotationsResponse->json();  
  
        return view('quotations.partials.quotations_table', compact('quotations'));  
    }  
}