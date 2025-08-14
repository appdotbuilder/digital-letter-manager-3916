<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Services\AuditService;
use Illuminate\Http\Request;

class LetterApprovalController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Approve a letter.
     */
    public function store(Letter $letter, Request $request)
    {
        if (!$letter->canBeApprovedBy($request->user())) {
            abort(403, 'You cannot approve this letter.');
        }

        $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $letter->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        $this->auditService->logLetterReviewed(
            $letter, 
            $request->user(), 
            'approved', 
            $request->review_notes, 
            $request
        );

        return redirect()->route('letters.show', $letter)
            ->with('success', 'Letter approved successfully.');
    }
}