import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface Letter {
    id: number;
    title: string;
    status: string;
    created_at: string;
    creator: {
        name: string;
    };
    reference_number?: string;
}

interface Props {
    letters: {
        data: Letter[];
        links: unknown;
    };
    filters: {
        status?: string;
    };
    [key: string]: unknown;
}

export default function LettersIndex({ letters, filters }: Props) {
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
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
                <span className="mr-1">{config.emoji}</span>
                {status.replace('_', ' ').toUpperCase()}
            </span>
        );
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <AppShell>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">📄 Letters</h1>
                        <p className="text-gray-600 mt-1">Manage your official letters and documents</p>
                    </div>
                    <Link href="/letters/create">
                        <Button>
                            <span className="mr-2">📝</span>
                            Create Letter
                        </Button>
                    </Link>
                </div>

                {/* Filters */}
                <div className="bg-white rounded-lg border border-gray-200 p-4">
                    <div className="flex flex-wrap gap-2">
                        <Link 
                            href="/letters" 
                            className={`px-3 py-1 rounded-full text-sm font-medium border transition-colors ${
                                !filters.status 
                                    ? 'bg-blue-100 text-blue-800 border-blue-200' 
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            }`}
                        >
                            All
                        </Link>
                        {['draft', 'submitted', 'under_review', 'approved', 'signed', 'rejected'].map((status) => (
                            <Link
                                key={status}
                                href={`/letters?status=${status}`}
                                className={`px-3 py-1 rounded-full text-sm font-medium border transition-colors ${
                                    filters.status === status
                                        ? 'bg-blue-100 text-blue-800 border-blue-200'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                }`}
                            >
                                {status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1)}
                            </Link>
                        ))}
                    </div>
                </div>

                {/* Letters List */}
                <div className="bg-white shadow rounded-lg border border-gray-200">
                    {letters.data.length === 0 ? (
                        <div className="text-center py-12">
                            <div className="text-6xl mb-4">📭</div>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">No letters found</h3>
                            <p className="text-gray-500 mb-6">Get started by creating your first official letter.</p>
                            <Link href="/letters/create">
                                <Button>
                                    <span className="mr-2">📝</span>
                                    Create Your First Letter
                                </Button>
                            </Link>
                        </div>
                    ) : (
                        <div className="overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Letter
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Reference
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {letters.data.map((letter) => (
                                        <tr key={letter.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {letter.title}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        by {letter.creator.name}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {letter.reference_number || '—'}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {getStatusBadge(letter.status)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {formatDate(letter.created_at)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div className="flex space-x-2">
                                                    <Link href={`/letters/${letter.id}`}>
                                                        <Button variant="ghost" size="sm">
                                                            View
                                                        </Button>
                                                    </Link>
                                                    {letter.status === 'draft' && (
                                                        <Link href={`/letters/${letter.id}/edit`}>
                                                            <Button variant="ghost" size="sm">
                                                                Edit
                                                            </Button>
                                                        </Link>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppShell>
    );
}