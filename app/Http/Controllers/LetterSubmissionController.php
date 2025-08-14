<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Services\AuditService;
use Illuminate\Http\Request;

class LetterSubmissionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Submit a letter for review.
     */
    public function store(Letter $letter, Request $request)
    {
        if (!$letter->canBeSubmitted() || $letter->created_by !== $request->user()->id) {
            abort(403, 'You cannot submit this letter.');
        }

        $letter->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->auditService->logLetterSubmitted($letter, $request->user(), $request);

        return redirect()->route('letters.show', $letter)
            ->with('success', 'Letter submitted for review.');
    }
}