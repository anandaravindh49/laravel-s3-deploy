<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicRequest;
use App\Services\PublicRequestService;
use Illuminate\Http\Request;

class PublicRequestController extends Controller
{
    public function __construct(
        private PublicRequestService $publicRequestService
    ) {}

    /**
     * Get all pending requests (Admin)
     */
    public function index()
    {
        return response()->json([
            'requests' => $this->publicRequestService->getPendingRequests()
                ->through(fn ($req) => [
                    'id' => $req->id,
                    'visitor_name' => $req->visitor_name,
                    'visitor_phone' => $req->visitor_phone,
                    'purpose_of_visit' => $req->purpose_of_visit,
                    'visit_date' => $req->visit_date,
                    'visit_time' => $req->visit_time,
                    'host_department' => $req->host_department,
                    'status' => $req->status,
                    'created_at' => $req->created_at->format('Y-m-d H:i:s'),
                ]),
        ]);
    }

    /**
     * Submit public request (Public Form)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email',
            'visitor_phone' => 'required|string|max:20',
            'id_proof_type' => 'nullable|string',
            'id_proof_number' => 'nullable|string',
            'id_proof_image' => 'nullable|file|mimes:jpg,png,pdf',
            'purpose_of_visit' => 'required|string|max:500',
            'visit_date' => 'required|date|after:today',
            'visit_time' => 'nullable|date_format:H:i',
            'host_department' => 'required|string',
            'host_person_name' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('id_proof_image')) {
            $path = $request->file('id_proof_image')->store('id_proofs', 'public');
            $validated['id_proof_image'] = $path;
        }

        $publicRequest = $this->publicRequestService->createRequest($validated);

        return response()->json([
            'message' => 'Request submitted successfully',
            'request_id' => $publicRequest->id,
            'status' => 'pending',
            'submitted_at' => $publicRequest->created_at,
        ], 201);
    }

    /**
     * Get request details
     */
    public function show(PublicRequest $request)
    {
        return response()->json([
            'request' => [
                'id' => $request->id,
                'visitor_name' => $request->visitor_name,
                'visitor_email' => $request->visitor_email,
                'visitor_phone' => $request->visitor_phone,
                'id_proof_type' => $request->id_proof_type,
                'id_proof_number' => $request->id_proof_number,
                'purpose_of_visit' => $request->purpose_of_visit,
                'visit_date' => $request->visit_date,
                'visit_time' => $request->visit_time,
                'host_department' => $request->host_department,
                'host_person_name' => $request->host_person_name,
                'status' => $request->status,
                'approved_at' => $request->approved_at,
                'rejection_reason' => $request->rejection_reason,
                'created_at' => $request->created_at,
            ],
        ]);
    }
}
