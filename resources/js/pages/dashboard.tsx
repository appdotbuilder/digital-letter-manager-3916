import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

interface Props {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            role: string;
            can_sign: boolean;
            can_review: boolean;
        };
    };
    [key: string]: unknown;
}

export default function Dashboard({ auth }: Props) {
    const { user } = auth;

    const getWelcomeMessage = () => {
        switch (user.role) {
            case 'admin':
                return '👑 Welcome, Admin!';
            case 'boss':
                return '🎯 Welcome, Boss!';
            case 'manager':
                return '📋 Welcome, Manager!';
            default:
                return '✨ Welcome to SecureSign!';
        }
    };

    const getRoleDescription = () => {
        switch (user.role) {
            case 'admin':
                return 'You have full system access. Manage users, monitor all letters, and configure system settings.';
            case 'boss':
                return 'You can sign approved letters and oversee the entire organization\'s document workflow.';
            case 'manager':
                return 'You can review and approve letters from your team before they go for final signing.';
            default:
                return 'You can create and edit draft letters, then submit them for approval.';
        }
    };

    const getQuickActions = () => {
        const actions = [
            {
                title: '📝 Create New Letter',
                description: 'Start drafting a new official letter',
                href: '/letters/create',
                variant: 'default' as const,
                show: true,
            },
            {
                title: '📄 My Letters',
                description: 'View and manage your created letters',
                href: '/letters?created_by=me',
                variant: 'outline' as const,
                show: true,
            },
            {
                title: '🔍 Review Queue',
                description: 'Letters waiting for your review',
                href: '/letters?status=submitted',
                variant: 'outline' as const,
                show: user.can_review || user.role === 'manager' || user.role === 'admin',
            },
            {
                title: '✍️ Signing Queue',
                description: 'Approved letters ready for signature',
                href: '/letters?status=approved',
                variant: 'outline' as const,
                show: user.can_sign || user.role === 'boss' || user.role === 'admin',
            },
            {
                title: '🔐 Manage Signing Keys',
                description: 'Generate or view your digital signature keys',
                href: '/user-keys',
                variant: 'outline' as const,
                show: user.can_sign || user.role === 'boss' || user.role === 'admin',
            },
            {
                title: '🔍 Verify Signature',
                description: 'Verify the authenticity of signed documents',
                href: '/verify',
                variant: 'ghost' as const,
                show: true,
            },
        ];

        return actions.filter(action => action.show);
    };

    const getRecentStats = () => {
        // These would be fetched from the backend in a real implementation
        return [
            { label: 'Letters Created', value: '12', color: 'text-blue-600' },
            { label: 'Pending Review', value: '3', color: 'text-orange-600' },
            { label: 'Awaiting Signature', value: '2', color: 'text-purple-600' },
            { label: 'Completed', value: '7', color: 'text-green-600' },
        ];
    };

    return (
        <AppShell>
            <div className="space-y-8">
                {/* Header */}
                <div className="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-2xl p-8 border border-blue-200">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">
                        {getWelcomeMessage()}
                    </h1>
                    <p className="text-lg text-gray-600 mb-4">
                        {getRoleDescription()}
                    </p>
                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                        <div className="flex items-center space-x-2">
                            <span className="w-2 h-2 bg-green-400 rounded-full"></span>
                            <span>Role: {user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                        </div>
                        {user.can_review && (
                            <div className="flex items-center space-x-2">
                                <span className="w-2 h-2 bg-blue-400 rounded-full"></span>
                                <span>Can Review</span>
                            </div>
                        )}
                        {user.can_sign && (
                            <div className="flex items-center space-x-2">
                                <span className="w-2 h-2 bg-purple-400 rounded-full"></span>
                                <span>Can Sign</span>
                            </div>
                        )}
                    </div>
                </div>

                {/* Quick Stats */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {getRecentStats().map((stat, index) => (
                        <div key={index} className="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600 mb-1">{stat.label}</p>
                                    <p className={`text-2xl font-bold ${stat.color}`}>{stat.value}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Quick Actions */}
                <div className="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">⚡ Quick Actions</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {getQuickActions().map((action, index) => (
                            <div key={index} className="group">
                                <Link href={action.href}>
                                    <div className="p-6 rounded-xl border-2 border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                                        <h3 className="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600">
                                            {action.title}
                                        </h3>
                                        <p className="text-gray-600 text-sm mb-4">
                                            {action.description}
                                        </p>
                                        <div className="flex justify-end">
                                            <Button variant={action.variant} size="sm">
                                                Go →
                                            </Button>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Recent Activity */}
                <div className="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">📈 Recent Activity</h2>
                    <div className="space-y-4">
                        <div className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span className="text-blue-600 font-semibold text-sm">📝</span>
                            </div>
                            <div className="flex-1">
                                <p className="text-sm font-medium text-gray-900">Letter "Meeting Request" was created</p>
                                <p className="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <span className="text-green-600 font-semibold text-sm">✅</span>
                            </div>
                            <div className="flex-1">
                                <p className="text-sm font-medium text-gray-900">Letter "Policy Update" was approved</p>
                                <p className="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                        <div className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div className="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <span className="text-purple-600 font-semibold text-sm">🔐</span>
                            </div>
                            <div className="flex-1">
                                <p className="text-sm font-medium text-gray-900">Letter "Contract Amendment" was signed</p>
                                <p className="text-xs text-gray-500">3 days ago</p>
                            </div>
                        </div>
                    </div>
                    <div className="mt-6 text-center">
                        <Link href="/letters">
                            <Button variant="ghost">
                                View All Letters →
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* System Status */}
                <div className="bg-gradient-to-r from-green-50 to-emerald-100 rounded-2xl p-8 border border-green-200">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">🛡️ System Status</h3>
                            <p className="text-gray-600">All systems operational. Your data is secure and protected.</p>
                        </div>
                        <div className="text-right">
                            <div className="w-4 h-4 bg-green-500 rounded-full inline-block mb-1"></div>
                            <p className="text-sm text-gray-600">Secure Connection</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppShell>
    );
}