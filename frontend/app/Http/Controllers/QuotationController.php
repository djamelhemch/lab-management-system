<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\AnalysisController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;

class QuotationController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $params = [
            'q'      => $request->get('q'),
            'status' => $request->get('status'),
        ];

        $response = $this->api->get('/quotations', array_filter($params));
        $quotations = $response->ok() ? $response->json() : [];

        // full page
        return view('quotations.index', compact('quotations'));
    }

    public function table(Request $request)
    {
        $params = [
            'q'      => $request->get('q'),
            'status' => $request->get('status'),
        ];

        $response = $this->api->get('/quotations', array_filter($params));
        $quotations = $response->ok() ? $response->json() : [];

        // only table partial, NO layout
        return view('quotations.partials.table', compact('quotations'));
    }

    public function create()
    {
        $patients   = $this->api->get('patients')->json();
        $analyses   = $this->api->get('analyses')->json();
        $agreements = $this->api->get('agreements', ['status' => 'active'])->json();
        $doctors    = $this->api->get('doctors')->json() ?? [];

        return view('quotations.create', compact('patients', 'analyses', 'agreements', 'doctors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'              => 'required|integer',
            'items'                   => 'required|array|min:1',
            'items.*.analysis_id'     => 'required|integer',
            'items.*.price'           => 'required|numeric|min:0',
            'payment.amount'          => 'nullable|numeric|min:0',
            'payment.method'          => 'nullable|string|max:50',
            'payment.notes'           => 'nullable|string|max:255',
            'payment.amount_received' => 'nullable|numeric|min:0',
            'payment.change_given'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Session::get('user');
        if (!$user || !isset($user['id'])) {
            return back()->with('error', 'User session expired, please log in again.');
        }

        // --- Compute totals ---
        $total = collect($request->items)->sum(fn ($item) => (float) $item['price']);
        $discount = $request->input('discount_applied', 0.0);
        $netTotal = $total - $discount;
        $outstanding = $request->input('payment.amount_received')
            ? $netTotal - (float) $request->input('payment.amount_received')
            : $netTotal;

        // --- Build quotation payload ---
        $quotationData = [
            'patient_id'       => (int) $request->patient_id,
            'status'           => 'draft',
            'agreement_id'     => $request->agreement_id ? (int) $request->agreement_id : null,
            'items'            => collect($request->items)->map(fn ($item) => [
                'analysis_id' => (int) $item['analysis_id'],
                'price'       => (float) $item['price'],
            ])->values()->all(),
            'total'            => $total,
            'discount_applied' => (float) $discount,
            'net_total'        => $netTotal,
            'outstanding'      => $outstanding,
        ];

        if ($request->filled('payment.method')) {
            $quotationData['payment'] = [
                'amount'          => (float) $request->input('payment.amount'),
                'method'          => $request->input('payment.method'),
                'notes'           => $request->input('payment.notes'),
                'amount_received' => $request->input('payment.amount_received'),
                'change_given'    => $request->input('payment.change_given'),
                'outstanding'     => $outstanding, // sync with quotation outstanding
                'user_id'         => $user['id'],
            ];
        }

        \Log::info('📤 Sending quotation payload to FastAPI', $quotationData);

        $quotationResponse = $this->api->post('quotations', $quotationData);

        if (!$quotationResponse->successful()) {
            return back()->with('error', 'Failed to create quotation.')->withInput();
        }

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function storePatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'dob'               => 'required|date',
            'gender'            => 'required|string|in:H,F',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'address'           => 'nullable|string|max:255',
            'blood_type'        => 'required|string|max:3',
            'weight'            => 'nullable|numeric|min:0',
            'allergies'         => 'nullable|string',
            'medical_history'   => 'nullable|string',
            'chronic_conditions'=> 'nullable|string',
            'doctor_id'         => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Forward to FastAPI
        $payload = $validator->validated();
        $response = $this->api->post('patients', $payload);

        if (!$response->successful()) {
            return response()->json([
                'error'   => 'Failed to create patient',
                'details' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json(), 201);
    }

    public function show($id)
    {
        $response = $this->api->get("quotations/{$id}");

        if (!$response->successful()) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }

        $quotation = $response->json();
        // ✅ Outstanding now comes directly from FastAPI/DB
        return view('quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $response = $this->api->get("quotations/{$id}");
        if (!$response->successful()) {
            return redirect()->route('quotations.index')
                ->with('error', 'Quotation not found.');
        }

        $quotation  = $response->json();
        $patients   = $this->api->get('patients')->json();
        $analyses   = $this->api->get('analyses')->json();
        $agreements = $this->api->get('agreements', ['status' => 'active'])->json();

        return view('quotations.edit', compact('quotation', 'patients', 'analyses', 'agreements'));
    }

    public function update(Request $request, $id)
    {
        $payload = [
            'status'      => $request->input('status'),
            'analyses'    => $request->input('analyses', []),
            'new_payment' => $request->input('new_payment', null),
        ];

        $response = $this->api->put("quotations/{$id}", $payload);

        if ($response->successful()) {
            return redirect()->route('quotations.show', $id)
                ->with('success', 'Quotation updated successfully.');
        } else {
            return back()->with('error', 'Failed to update quotation.')->withInput();
        }
    }
}
