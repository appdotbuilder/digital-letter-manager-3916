<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LetterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Letter::with(['creator', 'reviewer', 'signer', 'template'])
            ->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user role
        $user = $request->user();
        if (!$user->isAdmin()) {
            if ($user->role === 'staff') {
                // Staff can only see their own letters
                $query->where('created_by', $user->id);
            } elseif ($user->hasReviewPrivileges()) {
                // Managers can see letters assigned to them or created by their team
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_reviewer', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            } elseif ($user->hasSigningPrivileges()) {
                // Signers can see letters assigned to them for signing
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_signer', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }
        }

        $letters = $query->paginate(15);

        return Inertia::render('letters/index', [
            'letters' => $letters,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = LetterTemplate::active()->get();
        $reviewers = User::active()->canReview()->get(['id', 'name', 'role']);
        $signers = User::active()->canSign()->get(['id', 'name', 'role']);

        return Inertia::render('letters/create', [
            'templates' => $templates,
            'reviewers' => $reviewers,
            'signers' => $signers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLetterRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;
        
        // Generate reference number after creation
        $letter = Letter::create($validated);
        $letter->update([
            'reference_number' => $letter->generateReferenceNumber()
        ]);

        // Log the creation
        $this->auditService->logLetterCreated($letter, $request->user(), $request);

        return redirect()->route('letters.show', $letter)
            ->with('success', 'Letter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Letter $letter)
    {
        $letter->load(['creator', 'reviewer', 'signer', 'template', 'digitalSignature']);

        // Check permissions
        $user = request()->user();
        $canView = $letter->created_by === $user->id ||
                   $letter->assigned_reviewer === $user->id ||
                   $letter->assigned_signer === $user->id ||
                   $user->isAdmin();

        if (!$canView) {
            abort(403, 'You do not have permission to view this letter.');
        }

        return Inertia::render('letters/show', [
            'letter' => $letter,
            'canEdit' => $letter->canBeEditedBy($user),
            'canSubmit' => $letter->canBeSubmitted(),
            'canApprove' => $letter->canBeApprovedBy($user),
            'canSign' => $letter->canBeSignedBy($user),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Letter $letter)
    {
        $user = request()->user();
        
        if (!$letter->canBeEditedBy($user)) {
            abort(403, 'You cannot edit this letter.');
        }

        $templates = LetterTemplate::active()->get();
        $reviewers = User::active()->canReview()->get(['id', 'name', 'role']);
        $signers = User::active()->canSign()->get(['id', 'name', 'role']);

        return Inertia::render('letters/edit', [
            'letter' => $letter,
            'templates' => $templates,
            'reviewers' => $reviewers,
            'signers' => $signers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLetterRequest $request, Letter $letter)
    {
        if (!$letter->canBeEditedBy($request->user())) {
            abort(403, 'You cannot edit this letter.');
        }

        $letter->update($request->validated());

        return redirect()->route('letters.show', $letter)
            ->with('success', 'Letter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Letter $letter)
    {
        $user = request()->user();
        
        if (!$letter->canBeEditedBy($user)) {
            abort(403, 'You cannot delete this letter.');
        }

        $letter->delete();

        return redirect()->route('letters.index')
            ->with('success', 'Letter deleted successfully.');
    }


}