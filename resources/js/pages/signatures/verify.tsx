import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { InputError } from '@/components/input-error';

interface VerificationResult {
    verified: boolean;
    message?: string;
    signature_info?: {
        signer_name: string;
        signed_at: string;
        algorithm: string;
        key_fingerprint: string;
    };
    checks?: {
        signature_valid: boolean;
        content_unchanged: boolean;
    };
}



export default function VerifySignature() {
    const [letterId, setLetterId] = useState('');
    const [content, setContent] = useState('');
    const [verificationResult, setVerificationResult] = useState<VerificationResult | null>(null);
    const [isVerifying, setIsVerifying] = useState(false);
    const [errors, setErrors] = useState<{ [key: string]: string }>({});

    const handleVerify = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsVerifying(true);
        setErrors({});
        setVerificationResult(null);

        try {
            const response = await fetch('/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    letter_id: letterId,
                    content: content,
                }),
            });

            if (response.ok) {
                const result = await response.json();
                setVerificationResult(result);
            } else {
                const errorData = await response.json();
                if (errorData.errors) {
                    setErrors(errorData.errors);
                } else {
                    setErrors({ general: 'Verification failed. Please try again.' });
                }
            }
        } catch {
            setErrors({ general: 'Network error. Please try again.' });
        } finally {
            setIsVerifying(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 py-8">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="text-center mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">
                        🔍 Signature Verification
                    </h1>
                    <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                        Verify the authenticity and integrity of digitally signed letters. 
                        Enter the letter ID and content to check if the signature is valid.
                    </p>
                </div>

                {/* Verification Form */}
                <div className="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
                    <form onSubmit={handleVerify} className="space-y-6">
                        <div>
                            <label htmlFor="letter_id" className="block text-sm font-medium text-gray-700 mb-2">
                                Letter ID *
                            </label>
                            <input
                                type="text"
                                id="letter_id"
                                value={letterId}
                                onChange={(e) => setLetterId(e.target.value)}
                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter the letter ID (e.g., 123)"
                                required
                            />
                            <InputError message={errors.letter_id} className="mt-2" />
                        </div>

                        <div>
                            <label htmlFor="content" className="block text-sm font-medium text-gray-700 mb-2">
                                Letter Content *
                            </label>
                            <textarea
                                id="content"
                                value={content}
                                onChange={(e) => setContent(e.target.value)}
                                rows={15}
                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                placeholder="Paste the exact content of the letter here, including HTML formatting..."
                                required
                            />
                            <InputError message={errors.content} className="mt-2" />
                            <p className="text-xs text-gray-500 mt-2">
                                💡 The content must match exactly as it was when signed, including all HTML tags and formatting.
                            </p>
                        </div>

                        {errors.general && (
                            <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p className="text-red-800">{errors.general}</p>
                            </div>
                        )}

                        <Button 
                            type="submit" 
                            disabled={isVerifying}
                            className="w-full py-3 text-lg"
                        >
                            {isVerifying ? (
                                <>
                                    <span className="mr-2">⏳</span>
                                    Verifying Signature...
                                </>
                            ) : (
                                <>
                                    <span className="mr-2">🔍</span>
                                    Verify Signature
                                </>
                            )}
                        </Button>
                    </form>
                </div>

                {/* Verification Results */}
                {verificationResult && (
                    <div className={`rounded-2xl border-2 p-8 ${
                        verificationResult.verified 
                            ? 'bg-green-50 border-green-200' 
                            : 'bg-red-50 border-red-200'
                    }`}>
                        <div className="flex items-center mb-6">
                            <div className={`w-16 h-16 rounded-full flex items-center justify-center text-2xl mr-4 ${
                                verificationResult.verified 
                                    ? 'bg-green-100 text-green-600' 
                                    : 'bg-red-100 text-red-600'
                            }`}>
                                {verificationResult.verified ? '✅' : '❌'}
                            </div>
                            <div>
                                <h2 className={`text-2xl font-bold ${
                                    verificationResult.verified 
                                        ? 'text-green-900' 
                                        : 'text-red-900'
                                }`}>
                                    {verificationResult.verified 
                                        ? 'Signature Verified ✓' 
                                        : 'Signature Invalid ✗'
                                    }
                                </h2>
                                <p className={`text-lg ${
                                    verificationResult.verified 
                                        ? 'text-green-700' 
                                        : 'text-red-700'
                                }`}>
                                    {verificationResult.verified 
                                        ? 'This document is authentic and has not been tampered with.'
                                        : (verificationResult.message || 'The signature could not be verified.')
                                    }
                                </p>
                            </div>
                        </div>

                        {verificationResult.verified && verificationResult.signature_info && (
                            <div className="bg-white rounded-xl border border-green-200 p-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">📋 Signature Details</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span className="font-medium text-gray-700">Signed by:</span>
                                        <p className="text-gray-900">{verificationResult.signature_info.signer_name}</p>
                                    </div>
                                    <div>
                                        <span className="font-medium text-gray-700">Signed on:</span>
                                        <p className="text-gray-900">{verificationResult.signature_info.signed_at}</p>
                                    </div>
                                    <div>
                                        <span className="font-medium text-gray-700">Algorithm:</span>
                                        <p className="text-gray-900">{verificationResult.signature_info.algorithm}</p>
                                    </div>
                                    <div>
                                        <span className="font-medium text-gray-700">Key fingerprint:</span>
                                        <p className="text-gray-900 font-mono text-xs break-all">
                                            {verificationResult.signature_info.key_fingerprint}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {verificationResult.checks && (
                            <div className="mt-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">🔍 Verification Checks</h3>
                                <div className="space-y-3">
                                    <div className="flex items-center">
                                        <span className={`text-lg mr-3 ${
                                            verificationResult.checks.signature_valid ? 'text-green-600' : 'text-red-600'
                                        }`}>
                                            {verificationResult.checks.signature_valid ? '✅' : '❌'}
                                        </span>
                                        <span className="text-gray-800">
                                            Cryptographic signature validation
                                        </span>
                                    </div>
                                    <div className="flex items-center">
                                        <span className={`text-lg mr-3 ${
                                            verificationResult.checks.content_unchanged ? 'text-green-600' : 'text-red-600'
                                        }`}>
                                            {verificationResult.checks.content_unchanged ? '✅' : '❌'}
                                        </span>
                                        <span className="text-gray-800">
                                            Document integrity check (content unchanged)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {/* Information Section */}
                <div className="bg-blue-50 border border-blue-200 rounded-2xl p-8 mt-8">
                    <h3 className="text-xl font-semibold text-blue-900 mb-4">
                        🛡️ How Signature Verification Works
                    </h3>
                    <div className="space-y-4 text-blue-800">
                        <p>
                            <strong>1. Cryptographic Validation:</strong> We verify the digital signature using the signer's public key 
                            to ensure it was created with the corresponding private key.
                        </p>
                        <p>
                            <strong>2. Content Integrity:</strong> We generate a SHA-256 hash of the provided content and compare it 
                            with the hash that was signed to ensure the document hasn't been altered.
                        </p>
                        <p>
                            <strong>3. Timestamp Verification:</strong> We check when the document was signed and provide this 
                            information for audit purposes.
                        </p>
                        <p className="text-sm bg-blue-100 p-3 rounded-lg">
                            <strong>Security Note:</strong> This verification is performed entirely server-side using 
                            RSA-2048 with SHA-256, providing the same level of security as SSL certificates.
                        </p>
                    </div>
                </div>

                {/* Back Link */}
                <div className="text-center mt-8">
                    <a 
                        href="/" 
                        className="inline-flex items-center text-blue-600 hover:text-blue-500 transition-colors"
                    >
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    );
}