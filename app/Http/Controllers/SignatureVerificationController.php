<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Services\AuditService;
use App\Services\CryptographyService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SignatureVerificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private CryptographyService $cryptographyService,
        private AuditService $auditService
    ) {}

    /**
     * Show the signature verification page.
     */
    public function index()
    {
        return Inertia::render('signatures/verify');
    }

    /**
     * Verify a digital signature.
     */
    public function store(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'content' => 'required|string',
        ]);

        $letter = Letter::with(['digitalSignature.userKey', 'digitalSignature.signer'])->findOrFail($request->letter_id);
        
        if (!$letter->digitalSignature) {
            return response()->json([
                'verified' => false,
                'message' => 'No digital signature found for this letter.',
            ]);
        }

        $signature = $letter->digitalSignature;
        
        try {
            $isValid = $this->cryptographyService->verifySignature(
                $request->content,
                $signature->signature_data,
                $signature->userKey->public_key
            );

            // Additional integrity checks
            $contentHash = hash('sha256', $request->content);
            $hashMatches = $contentHash === $signature->content_hash;

            $verified = $isValid && $hashMatches;

            // Log the verification attempt
            $this->auditService->logSignatureVerification(
                (string) $letter->id,
                $verified,
                $request->user(),
                $request
            );

            return response()->json([
                'verified' => $verified,
                'signature_info' => [
                    'signer_name' => $signature->signer->name,
                    'signed_at' => $signature->signed_at->format('Y-m-d H:i:s T'),
                    'algorithm' => $signature->algorithm,
                    'key_fingerprint' => $signature->userKey->key_fingerprint,
                ],
                'checks' => [
                    'signature_valid' => $isValid,
                    'content_unchanged' => $hashMatches,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'verified' => false,
                'message' => 'Signature verification failed: ' . $e->getMessage(),
            ]);
        }
    }
}