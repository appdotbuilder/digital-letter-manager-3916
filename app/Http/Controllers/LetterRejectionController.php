<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Services\AuditService;
use Illuminate\Http\Request;

class LetterRejectionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Reject a letter.
     */
    public function store(Letter $letter, Request $request)
    {
        if (!$letter->canBeApprovedBy($request->user())) {
            abort(403, 'You cannot reject this letter.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $letter->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->auditService->logLetterReviewed(
            $letter, 
            $request->user(), 
            'rejected', 
            $request->rejection_reason, 
            $request
        );

        return redirect()->route('letters.show', $letter)
            ->with('success', 'Letter rejected.');
    }
}