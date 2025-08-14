<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserKey;
use App\Services\AuditService;
use App\Services\CryptographyService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserKeyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private CryptographyService $cryptographyService,
        private AuditService $auditService
    ) {}

    /**
     * Display a listing of the user's keys.
     */
    public function index()
    {
        $keys = request()->user()->keys()->orderBy('generated_at', 'desc')->get();

        return Inertia::render('user-keys/index', [
            'keys' => $keys,
        ]);
    }

    /**
     * Show the form for creating a new key pair.
     */
    public function create()
    {
        return Inertia::render('user-keys/create');
    }

    /**
     * Generate and store a new key pair.
     */
    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        try {
            // Generate new key pair
            $keyPair = $this->cryptographyService->generateKeyPair();

            // Encrypt the private key with user's password
            $encryptedPrivateKey = $this->cryptographyService->encryptPrivateKey(
                $keyPair['private_key'],
                $request->password
            );

            // Deactivate existing keys
            $user->keys()->update(['is_active' => false]);

            // Create new key record
            $userKey = UserKey::create([
                'user_id' => $user->id,
                'public_key' => $keyPair['public_key'],
                'encrypted_private_key' => $encryptedPrivateKey,
                'key_fingerprint' => $keyPair['fingerprint'],
                'generated_at' => now(),
                'is_active' => true,
            ]);

            // Log the key generation
            $this->auditService->logKeyGenerated($user, $keyPair['fingerprint'], $request);

            return redirect()->route('user-keys.index')
                ->with('success', 'New signing key generated successfully. Keep your password safe - it cannot be recovered!');

        } catch (Exception $e) {
            return back()->withErrors(['password' => 'Failed to generate key pair: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified key.
     */
    public function show(UserKey $userKey)
    {
        // Ensure user can only view their own keys
        if ($userKey->user_id !== request()->user()->id && !request()->user()->isAdmin()) {
            abort(403);
        }

        return Inertia::render('user-keys/show', [
            'userKey' => $userKey,
        ]);
    }

    /**
     * Deactivate a key pair.
     */
    public function destroy(UserKey $userKey)
    {
        $user = request()->user();

        // Ensure user can only deactivate their own keys
        if ($userKey->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $userKey->update(['is_active' => false]);

        return redirect()->route('user-keys.index')
            ->with('success', 'Key pair deactivated successfully.');
    }

    /**
     * Update key pair status (test functionality).
     */
    public function update(UserKey $userKey, Request $request)
    {
        if ($userKey->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        try {
            // Test decryption and signing
            $privateKey = $this->cryptographyService->decryptPrivateKey(
                $userKey->encrypted_private_key,
                $request->password
            );

            $testContent = 'Key test at ' . now()->toISOString();
            $signResult = $this->cryptographyService->signContent($testContent, $privateKey);

            $verified = $this->cryptographyService->verifySignature(
                $testContent,
                $signResult['signature'],
                $userKey->public_key
            );

            if ($verified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Key pair is working correctly.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Key pair verification failed.',
                ], 422);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Key test failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}