<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Crypt;

class CryptographyService
{
    private const KEY_SIZE = 2048;
    private const SIGNATURE_ALGORITHM = 'SHA256';

    /**
     * Generate a new RSA key pair.
     *
     * @return array{private_key: string, public_key: string, fingerprint: string}
     */
    public function generateKeyPair(): array
    {
        $config = [
            'private_key_bits' => self::KEY_SIZE,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        
        $resource = openssl_pkey_new($config);
        if (!$resource) {
            throw new Exception('Failed to generate RSA key pair');
        }
        
        // Export private key
        openssl_pkey_export($resource, $privateKey);
        
        // Get public key
        $details = openssl_pkey_get_details($resource);
        if (!$details) {
            throw new Exception('Failed to get public key details');
        }
        
        $publicKey = $details['key'];
        $fingerprint = $this->generateFingerprint($publicKey);

        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey,
            'fingerprint' => $fingerprint,
        ];
    }

    /**
     * Encrypt a private key with a password.
     *
     * @param string $privateKey
     * @param string $password
     * @return string
     */
    public function encryptPrivateKey(string $privateKey, string $password): string
    {
        // First encrypt with the user's password
        $encrypted = openssl_pkey_get_private($privateKey);
        if (!$encrypted) {
            throw new Exception('Invalid private key format');
        }

        // Export with password protection
        $success = openssl_pkey_export($encrypted, $protectedKey, $password);
        if (!$success) {
            throw new Exception('Failed to encrypt private key');
        }

        // Then encrypt again with Laravel's encryption for storage
        return Crypt::encryptString($protectedKey);
    }

    /**
     * Decrypt a private key with a password.
     *
     * @param string $encryptedPrivateKey
     * @param string $password
     * @return string
     */
    public function decryptPrivateKey(string $encryptedPrivateKey, string $password): string
    {
        try {
            // First decrypt with Laravel's encryption
            $protectedKey = Crypt::decryptString($encryptedPrivateKey);
            
            // Then decrypt with the user's password
            $privateKey = openssl_pkey_get_private($protectedKey, $password);
            if (!$privateKey) {
                throw new Exception('Invalid password or corrupted key');
            }

            // Export as unencrypted PEM
            $success = openssl_pkey_export($privateKey, $unencryptedKey);
            if (!$success) {
                throw new Exception('Failed to export private key');
            }

            return $unencryptedKey;
        } catch (Exception $e) {
            throw new Exception('Failed to decrypt private key: ' . $e->getMessage());
        }
    }

    /**
     * Sign content with a private key.
     *
     * @param string $content
     * @param string $privateKeyPem
     * @return array{signature: string, hash: string}
     */
    public function signContent(string $content, string $privateKeyPem): array
    {
        $hash = hash(strtolower(self::SIGNATURE_ALGORITHM), $content);
        
        $privateKey = openssl_pkey_get_private($privateKeyPem);
        if (!$privateKey) {
            throw new Exception('Invalid private key for signing');
        }

        $signature = '';
        $success = openssl_sign($content, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$success) {
            throw new Exception('Failed to sign content');
        }

        return [
            'signature' => base64_encode($signature),
            'hash' => $hash,
        ];
    }

    /**
     * Verify a signature against content and public key.
     *
     * @param string $content
     * @param string $signature
     * @param string $publicKeyPem
     * @return bool
     */
    public function verifySignature(string $content, string $signature, string $publicKeyPem): bool
    {
        try {
            $publicKey = openssl_pkey_get_public($publicKeyPem);
            if (!$publicKey) {
                throw new Exception('Invalid public key for verification');
            }

            $signatureData = base64_decode($signature, true);
            if ($signatureData === false) {
                return false;
            }

            $result = openssl_verify($content, $signatureData, $publicKey, OPENSSL_ALGO_SHA256);
            return $result === 1;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate a fingerprint for a public key.
     *
     * @param string $publicKey
     * @return string
     */
    public function generateFingerprint(string $publicKey): string
    {
        return hash('sha256', $publicKey);
    }

    /**
     * Verify that a private key matches a public key.
     *
     * @param string $privateKeyPem
     * @param string $publicKeyPem
     * @return bool
     */
    public function verifyKeyPair(string $privateKeyPem, string $publicKeyPem): bool
    {
        try {
            $testContent = 'test_verification_' . time();
            $signResult = $this->signContent($testContent, $privateKeyPem);
            return $this->verifySignature($testContent, $signResult['signature'], $publicKeyPem);
        } catch (Exception $e) {
            return false;
        }
    }
}