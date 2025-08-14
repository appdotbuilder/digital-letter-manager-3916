import React, { useState } from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/react';
import { InputError } from '@/components/input-error';

interface Letter {
    id: number;
    title: string;
    content: string;
    recipient_name?: string;
    recipient_address?: string;
    template_id?: number;
    assigned_reviewer?: number;
    assigned_signer?: number;
}

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
    letter: Letter;
    templates: Template[];
    reviewers: User[];
    signers: User[];
    [key: string]: unknown;
}

export default function EditLetter({ letter, templates, reviewers, signers }: Props) {
    const [selectedTemplate, setSelectedTemplate] = useState<Template | null>(null);

    const { data, setData, put, processing, errors } = useForm({
        title: letter.title,
        content: letter.content,
        recipient_name: letter.recipient_name || '',
        recipient_address: letter.recipient_address || '',
        template_id: letter.template_id?.toString() || '',
        assigned_reviewer: letter.assigned_reviewer?.toString() || '',
        assigned_signer: letter.assigned_signer?.toString() || '',
    });

    const handleTemplateSelect = (template: Template) => {
        setSelectedTemplate(template);
        setData({
            ...data,
            template_id: template.id.toString(),
            content: template.content,
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('letters.update', letter.id));
    };

    return (
        <AppShell>
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900">✏️ Edit Letter</h1>
                    <p className="text-gray-600 mt-1">Modify your draft letter before submission</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Template Selection */}
                    {templates.length > 0 && (
                        <div className="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">📋 Switch Template (Optional)</h3>
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
                            {selectedTemplate && (
                                <div className="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p className="text-sm text-blue-800">
                                        ⚠️ Selecting a template will replace your current content.
                                    </p>
                                </div>
                            )}
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
                                required
                            />
                            <InputError message={errors.content} className="mt-2" />
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
                    </div>

                    {/* Actions */}
                    <div className="flex justify-between">
                        <Button 
                            type="button" 
                            variant="ghost" 
                            onClick={() => window.history.back()}
                        >
                            ← Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving...' : '💾 Save Changes'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppShell>
    );
}