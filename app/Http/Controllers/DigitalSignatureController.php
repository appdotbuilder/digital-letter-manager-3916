<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DigitalSignature;
use App\Models\Letter;
use App\Models\UserKey;
use App\Services\AuditService;
use App\Services\CryptographyService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DigitalSignatureController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private CryptographyService $cryptographyService,
        private AuditService $auditService
    ) {}

    /**
     * Show the signing form for a letter.
     */
    public function create(Letter $letter)
    {
        $user = request()->user();
        
        if (!$letter->canBeSignedBy($user)) {
            abort(403, 'You cannot sign this letter.');
        }

        $userKey = $user->activeKey;
        if (!$userKey) {
            return redirect()->route('user-keys.create')
                ->with('error', 'You need to generate a signing key before you can sign letters.');
        }

        return Inertia::render('signatures/create', [
            'letter' => $letter->load(['creator', 'reviewer']),
            'userKey' => $userKey->only(['id', 'key_fingerprint', 'generated_at']),
        ]);
    }

    /**
     * Sign a letter with the user's private key.
     */
    public function store(Request $request, Letter $letter)
    {
        $user = $request->user();
        
        if (!$letter->canBeSignedBy($user)) {
            abort(403, 'You cannot sign this letter.');
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        $userKey = $user->activeKey;
        if (!$userKey) {
            return back()->withErrors(['password' => 'No active signing key found.']);
        }

        try {
            // Decrypt the private key with the user's password
            $privateKey = $this->cryptographyService->decryptPrivateKey(
                $userKey->encrypted_private_key,
                $request->password
            );

            // Sign the letter content
            $signResult = $this->cryptographyService->signContent(
                $letter->content,
                $privateKey
            );

            // Create the digital signature record
            $signature = DigitalSignature::create([
                'letter_id' => $letter->id,
                'signer_id' => $user->id,
                'user_key_id' => $userKey->id,
                'signature_data' => $signResult['signature'],
                'content_hash' => $signResult['hash'],
                'algorithm' => 'SHA256withRSA',
                'signed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Update the letter status
            $letter->update([
                'status' => 'signed',
                'signed_at' => now(),
            ]);

            // Log the signing event
            $this->auditService->logLetterSigned($letter, $user, (string) $signature->id, $request);

            return redirect()->route('letters.show', $letter)
                ->with('success', 'Letter signed successfully.');

        } catch (Exception $e) {
            return back()->withErrors(['password' => 'Invalid password or signing failed.']);
        }
    }



    /**
     * Display the specified signature details.
     */
    public function show(DigitalSignature $signature)
    {
        $signature->load(['letter', 'signer', 'userKey']);

        return Inertia::render('signatures/show', [
            'signature' => $signature,
        ]);
    }
}