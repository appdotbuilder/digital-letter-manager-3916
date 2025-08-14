import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface Letter {
    id: number;
    title: string;
    content: string;
    status: string;
    recipient_name?: string;
    recipient_address?: string;
    reference_number?: string;
    created_at: string;
    creator: {
        name: string;
    };
    reviewer?: {
        name: string;
    };
    signer?: {
        name: string;
    };
    digital_signature?: {
        id: number;
        signed_at: string;
        signer: {
            name: string;
        };
    };
}

interface Props {
    letter: Letter;
    canEdit: boolean;
    canSubmit: boolean;
    canApprove: boolean;
    canSign: boolean;
    [key: string]: unknown;
}

export default function ShowLetter({ letter, canEdit, canSubmit, canApprove, canSign }: Props) {
    const getStatusBadge = (status: string) => {
        const statusConfig = {
            draft: { color: 'bg-gray-100 text-gray-800', emoji: '📝' },
            submitted: { color: 'bg-blue-100 text-blue-800', emoji: '📤' },
            under_review: { color: 'bg-yellow-100 text-yellow-800', emoji: '👀' },
            approved: { color: 'bg-green-100 text-green-800', emoji: '✅' },
            signed: { color: 'bg-purple-100 text-purple-800', emoji: '🔐' },
            rejected: { color: 'bg-red-100 text-red-800', emoji: '❌' },
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.draft;
        
        return (
            <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${config.color}`}>
                <span className="mr-2">{config.emoji}</span>
                {status.replace('_', ' ').toUpperCase()}
            </span>
        );
    };

    return (
        <AppShell>
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex justify-between items-start">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">{letter.title}</h1>
                        <div className="flex items-center space-x-4 text-sm text-gray-600">
                            <span>Created by {letter.creator.name}</span>
                            <span>•</span>
                            <span>{new Date(letter.created_at).toLocaleDateString()}</span>
                            {letter.reference_number && (
                                <>
                                    <span>•</span>
                                    <span className="font-mono">{letter.reference_number}</span>
                                </>
                            )}
                        </div>
                    </div>
                    <div className="flex flex-col items-end space-y-2">
                        {getStatusBadge(letter.status)}
                        {letter.digital_signature && (
                            <div className="text-xs text-gray-500">
                                Signed by {letter.digital_signature.signer.name}
                            </div>
                        )}
                    </div>
                </div>

                {/* Actions */}
                <div className="flex flex-wrap gap-2">
                    <Link href="/letters">
                        <Button variant="ghost">← Back to Letters</Button>
                    </Link>
                    
                    {canEdit && (
                        <Link href={`/letters/${letter.id}/edit`}>
                            <Button variant="outline">✏️ Edit</Button>
                        </Link>
                    )}
                    
                    {canSubmit && (
                        <form method="POST" action={`/letters/${letter.id}/submit`} className="inline">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />
                            <Button type="submit">📤 Submit for Review</Button>
                        </form>
                    )}
                    
                    {canApprove && (
                        <Link href={`/letters/${letter.id}/approve`}>
                            <Button variant="outline">✅ Approve</Button>
                        </Link>
                    )}
                    
                    {canSign && (
                        <Link href={`/letters/${letter.id}/sign`}>
                            <Button>🔐 Sign Letter</Button>
                        </Link>
                    )}
                </div>

                {/* Letter Details */}
                {(letter.recipient_name || letter.recipient_address) && (
                    <div className="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">📮 Recipient Information</h3>
                        <div className="space-y-2">
                            {letter.recipient_name && (
                                <div>
                                    <span className="font-medium text-gray-700">Name:</span>
                                    <span className="ml-2 text-gray-900">{letter.recipient_name}</span>
                                </div>
                            )}
                            {letter.recipient_address && (
                                <div>
                                    <span className="font-medium text-gray-700">Address:</span>
                                    <div className="ml-2 text-gray-900 whitespace-pre-line">{letter.recipient_address}</div>
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {/* Letter Content */}
                <div className="bg-white rounded-lg border border-gray-200 p-8">
                    <h3 className="text-lg font-semibold text-gray-900 mb-6">📄 Letter Content</h3>
                    <div 
                        className="prose max-w-none"
                        dangerouslySetInnerHTML={{ __html: letter.content }}
                    />
                </div>

                {/* Digital Signature Info */}
                {letter.digital_signature && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div className="flex items-center mb-4">
                            <span className="text-2xl mr-3">🔐</span>
                            <div>
                                <h3 className="text-lg font-semibold text-green-900">Digitally Signed</h3>
                                <p className="text-green-700">This letter has been digitally signed and is legally binding.</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span className="font-medium text-green-800">Signed by:</span>
                                <p className="text-green-700">{letter.digital_signature.signer.name}</p>
                            </div>
                            <div>
                                <span className="font-medium text-green-800">Signed on:</span>
                                <p className="text-green-700">
                                    {new Date(letter.digital_signature.signed_at).toLocaleString()}
                                </p>
                            </div>
                        </div>
                        <div className="mt-4">
                            <Link href={`/signatures/${letter.digital_signature.id}`}>
                                <Button variant="outline" size="sm">
                                    View Signature Details
                                </Button>
                            </Link>
                        </div>
                    </div>
                )}

                {/* Workflow Status */}
                <div className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">🔄 Workflow Status</h3>
                    <div className="space-y-3">
                        {letter.reviewer && (
                            <div>
                                <span className="font-medium text-gray-700">Assigned Reviewer:</span>
                                <span className="ml-2 text-gray-900">{letter.reviewer.name}</span>
                            </div>
                        )}
                        {letter.signer && (
                            <div>
                                <span className="font-medium text-gray-700">Assigned Signer:</span>
                                <span className="ml-2 text-gray-900">{letter.signer.name}</span>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppShell>
    );
}