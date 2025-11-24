<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class LeaveRequestController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $leaveRequests = $this->api->get('/leave-requests')->json();
        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:255',
        ]);

        $this->api->post('/leave-requests', $request->only('start_date', 'end_date', 'reason'));

        return back()->with('success', 'Leave request submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $this->api->put("/leave-requests/{$id}", ['status' => $request->status]);

        return back()->with('success', 'Leave request updated successfully!');
    }

    public function destroy($id)
    {
        $this->api->delete("/leave-requests/{$id}");
        return back()->with('success', 'Leave request deleted successfully!');
    }
}
