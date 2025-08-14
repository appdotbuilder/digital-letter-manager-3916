import React, { useState } from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/react';
import { InputError } from '@/components/input-error';

interface Template {
    id: number;
    name: string;
    description?: string;
    content: string;
}

interface User {
    id: number;
    name: string;
    role: string;
}

interface Props {
    templates: Template[];
    reviewers: User[];
    signers: User[];
    [key: string]: unknown;
}

export default function CreateLetter({ templates, reviewers, signers }: Props) {
    const [selectedTemplate, setSelectedTemplate] = useState<Template | null>(null);

    const { data, setData, post, processing, errors } = useForm({
        title: '',
        content: '',
        recipient_name: '',
        recipient_address: '',
        template_id: '',
        assigned_reviewer: '',
        assigned_signer: '',
    });

    const handleTemplateSelect = (template: Template) => {
        setSelectedTemplate(template);
        setData({
            ...data,
            template_id: template.id.toString(),
            content: template.content,
            title: data.title || `Letter using ${template.name}`,
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('letters.store'));
    };

    return (
        <AppShell>
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900">📝 Create New Letter</h1>
                    <p className="text-gray-600 mt-1">Draft an official letter for approval and signing</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Template Selection */}
                    {templates.length > 0 && (
                        <div className="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">📋 Choose a Template (Optional)</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {templates.map((template) => (
                                    <div
                                        key={template.id}
                                        onClick={() => handleTemplateSelect(template)}
                                        className={`p-4 rounded-lg border-2 cursor-pointer transition-all ${
                                            selectedTemplate?.id === template.id
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                        }`}
                                    >
                                        <h4 className="font-medium text-gray-900">{template.name}</h4>
                                        {template.description && (
                                            <p className="text-sm text-gray-500 mt-1">{template.description}</p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Basic Information */}
                    <div className="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
                        <h3 className="text-lg font-semibold text-gray-900">📄 Letter Details</h3>
                        
                        <div>
                            <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-2">
                                Letter Title *
                            </label>
                            <input
                                type="text"
                                id="title"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter a descriptive title for this letter"
                                required
                            />
                            <InputError message={errors.title} className="mt-2" />
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label htmlFor="recipient_name" className="block text-sm font-medium text-gray-700 mb-2">
                                    Recipient Name
                                </label>
                                <input
                                    type="text"
                                    id="recipient_name"
                                    value={data.recipient_name}
                                    onChange={(e) => setData('recipient_name', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Who is this letter addressed to?"
                                />
                                <InputError message={errors.recipient_name} className="mt-2" />
                            </div>

                            <div>
                                <label htmlFor="recipient_address" className="block text-sm font-medium text-gray-700 mb-2">
                                    Recipient Address
                                </label>
                                <textarea
                                    id="recipient_address"
                                    value={data.recipient_address}
                                    onChange={(e) => setData('recipient_address', e.target.value)}
                                    rows={3}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Full address of the recipient"
                                />
                                <InputError message={errors.recipient_address} className="mt-2" />
                            </div>
                        </div>
                    </div>

                    {/* Letter Content */}
                    <div className="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">✍️ Letter Content</h3>
                        <div>
                            <label htmlFor="content" className="block text-sm font-medium text-gray-700 mb-2">
                                Content *
                            </label>
                            <textarea
                                id="content"
                                value={data.content}
                                onChange={(e) => setData('content', e.target.value)}
                                rows={15}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                placeholder="Write your letter content here. You can use HTML tags for formatting."
                                required
                            />
                            <InputError message={errors.content} className="mt-2" />
                            <p className="text-xs text-gray-500 mt-2">
                                💡 Tip: You can use HTML tags like &lt;b&gt;, &lt;i&gt;, &lt;u&gt; for basic formatting.
                            </p>
                        </div>
                    </div>

                    {/* Workflow Assignment */}
                    <div className="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
                        <h3 className="text-lg font-semibold text-gray-900">🔄 Workflow Assignment</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {reviewers.length > 0 && (
                                <div>
                                    <label htmlFor="assigned_reviewer" className="block text-sm font-medium text-gray-700 mb-2">
                                        Assign Reviewer
                                    </label>
                                    <select
                                        id="assigned_reviewer"
                                        value={data.assigned_reviewer}
                                        onChange={(e) => setData('assigned_reviewer', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select a reviewer (optional)</option>
                                        {reviewers.map((reviewer) => (
                                            <option key={reviewer.id} value={reviewer.id}>
                                                {reviewer.name} ({reviewer.role})
                                            </option>
                                        ))}
                                    </select>
                                    <InputError message={errors.assigned_reviewer} className="mt-2" />
                                </div>
                            )}

                            {signers.length > 0 && (
                                <div>
                                    <label htmlFor="assigned_signer" className="block text-sm font-medium text-gray-700 mb-2">
                                        Assign Signer
                                    </label>
                                    <select
                                        id="assigned_signer"
                                        value={data.assigned_signer}
                                        onChange={(e) => setData('assigned_signer', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select a signer (optional)</option>
                                        {signers.map((signer) => (
                                            <option key={signer.id} value={signer.id}>
                                                {signer.name} ({signer.role})
                                            </option>
                                        ))}
                                    </select>
                                    <InputError message={errors.assigned_signer} className="mt-2" />
                                </div>
                            )}
                        </div>

                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div className="flex">
                                <span className="text-blue-500 mr-2">💡</span>
                                <div className="text-sm">
                                    <p className="font-medium text-blue-900 mb-1">Workflow Process:</p>
                                    <p className="text-blue-800">
                                        1. Draft → 2. Submitted for Review → 3. Approved → 4. Digitally Signed
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-between">
                        <Button type="button" variant="ghost" onClick={() => window.history.back()}>
                            ← Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Creating...' : '📝 Create Letter'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppShell>
    );
}