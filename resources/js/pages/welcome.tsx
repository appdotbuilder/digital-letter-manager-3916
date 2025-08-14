import React from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

interface Props {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        } | null;
    };
    [key: string]: unknown;
}

export default function Welcome({ auth }: Props) {
    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
            {/* Navigation */}
            <nav className="bg-white/80 backdrop-blur-sm border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-16">
                        <div className="flex items-center space-x-3">
                            <div className="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold">📝</span>
                            </div>
                            <h1 className="text-xl font-bold text-gray-900">SecureSign</h1>
                        </div>
                        <div className="flex items-center space-x-4">
                            {auth.user ? (
                                <Link href="/dashboard">
                                    <Button>Dashboard</Button>
                                </Link>
                            ) : (
                                <>
                                    <Link href="/login">
                                        <Button variant="ghost">Sign In</Button>
                                    </Link>
                                    <Link href="/register">
                                        <Button>Get Started</Button>
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </nav>

            {/* Hero Section */}
            <div className="relative overflow-hidden">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
                    <div className="text-center">
                        <h1 className="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6">
                            🔐 Secure Digital 
                            <span className="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                {' '}Letter Management
                            </span>
                        </h1>
                        <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                            Transform your organization's letter approval process with enterprise-grade digital signatures, 
                            streamlined workflows, and bulletproof security. From draft to signature in minutes, not days.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            {auth.user ? (
                                <Link href="/dashboard">
                                    <Button size="lg" className="w-full sm:w-auto">
                                        Go to Dashboard 🚀
                                    </Button>
                                </Link>
                            ) : (
                                <>
                                    <Link href="/register">
                                        <Button size="lg" className="w-full sm:w-auto">
                                            Start Free Trial 🎯
                                        </Button>
                                    </Link>
                                    <Link href="/login">
                                        <Button variant="outline" size="lg" className="w-full sm:w-auto">
                                            Sign In 📝
                                        </Button>
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </div>

                {/* Floating Elements */}
                <div className="absolute top-20 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 animate-bounce" style={{animationDelay: '0s'}}></div>
                <div className="absolute top-40 right-10 w-16 h-16 bg-purple-200 rounded-full opacity-20 animate-bounce" style={{animationDelay: '1s'}}></div>
                <div className="absolute bottom-20 left-20 w-12 h-12 bg-indigo-200 rounded-full opacity-20 animate-bounce" style={{animationDelay: '2s'}}></div>
            </div>

            {/* Features Section */}
            <div className="bg-white py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-bold text-gray-900 mb-4">
                            ⚡ Everything You Need for Secure Letter Management
                        </h2>
                        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                            Built with enterprise security standards and designed for modern workflows.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Feature 1 */}
                        <div className="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-2xl border border-blue-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">🔒</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">RSA Digital Signatures</h3>
                            <p className="text-gray-600 mb-4">
                                Military-grade RSA-2048 encryption with SHA-256 hashing. Each signature is cryptographically 
                                secure and legally binding.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Password-protected private keys</li>
                                <li>✓ Tamper-proof verification</li>
                                <li>✓ Non-repudiation guarantee</li>
                            </ul>
                        </div>

                        {/* Feature 2 */}
                        <div className="bg-gradient-to-br from-purple-50 to-pink-100 p-8 rounded-2xl border border-purple-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">⚡</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Smart Workflow Engine</h3>
                            <p className="text-gray-600 mb-4">
                                Automated routing from Staff → Manager → Boss with role-based permissions and 
                                intelligent assignment.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Multi-stage approval process</li>
                                <li>✓ Automated notifications</li>
                                <li>✓ Real-time status tracking</li>
                            </ul>
                        </div>

                        {/* Feature 3 */}
                        <div className="bg-gradient-to-br from-green-50 to-emerald-100 p-8 rounded-2xl border border-green-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">📋</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Rich Text Templates</h3>
                            <p className="text-gray-600 mb-4">
                                Professional letter templates with WYSIWYG editing, dynamic placeholders, 
                                and consistent branding.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Drag & drop editor</li>
                                <li>✓ Variable substitution</li>
                                <li>✓ Brand consistency</li>
                            </ul>
                        </div>

                        {/* Feature 4 */}
                        <div className="bg-gradient-to-br from-orange-50 to-red-100 p-8 rounded-2xl border border-orange-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">📄</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">PDF Export & Printing</h3>
                            <p className="text-gray-600 mb-4">
                                Generate professional PDFs with visible signature seals, timestamps, 
                                and verification codes.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Official letterhead design</li>
                                <li>✓ Signature verification QR</li>
                                <li>✓ Print-ready formatting</li>
                            </ul>
                        </div>

                        {/* Feature 5 */}
                        <div className="bg-gradient-to-br from-cyan-50 to-blue-100 p-8 rounded-2xl border border-cyan-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-cyan-600 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">🔍</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Signature Verification</h3>
                            <p className="text-gray-600 mb-4">
                                Public verification portal allows anyone to authenticate signed documents 
                                using just the document content.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Zero-knowledge verification</li>
                                <li>✓ Public key cryptography</li>
                                <li>✓ Instant authenticity check</li>
                            </ul>
                        </div>

                        {/* Feature 6 */}
                        <div className="bg-gradient-to-br from-gray-50 to-slate-100 p-8 rounded-2xl border border-gray-200 hover:shadow-lg transition-shadow">
                            <div className="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center mb-6">
                                <span className="text-2xl">📊</span>
                            </div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-4">Complete Audit Trail</h3>
                            <p className="text-gray-600 mb-4">
                                Comprehensive logging of every action with timestamps, IP addresses, 
                                and user details for compliance.
                            </p>
                            <ul className="text-sm text-gray-500 space-y-1">
                                <li>✓ Immutable audit logs</li>
                                <li>✓ Compliance reporting</li>
                                <li>✓ Security monitoring</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {/* Security Section */}
            <div className="bg-gray-900 py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-bold text-white mb-4">
                            🛡️ Enterprise-Grade Security
                        </h2>
                        <p className="text-lg text-gray-300 max-w-2xl mx-auto">
                            Your sensitive documents deserve the highest level of protection.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div className="text-center">
                            <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span className="text-2xl">🔐</span>
                            </div>
                            <h3 className="text-white font-semibold mb-2">Encrypted Storage</h3>
                            <p className="text-gray-400 text-sm">Private keys encrypted with AES-256</p>
                        </div>
                        <div className="text-center">
                            <div className="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span className="text-2xl">🚫</span>
                            </div>
                            <h3 className="text-white font-semibold mb-2">Zero Trust</h3>
                            <p className="text-gray-400 text-sm">Role-based access control</p>
                        </div>
                        <div className="text-center">
                            <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span className="text-2xl">📝</span>
                            </div>
                            <h3 className="text-white font-semibold mb-2">CSRF Protection</h3>
                            <p className="text-gray-400 text-sm">Secure form submissions</p>
                        </div>
                        <div className="text-center">
                            <div className="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span className="text-2xl">📊</span>
                            </div>
                            <h3 className="text-white font-semibold mb-2">Audit Logging</h3>
                            <p className="text-gray-400 text-sm">Complete action tracking</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* CTA Section */}
            <div className="bg-gradient-to-r from-blue-600 to-purple-600 py-16">
                <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-bold text-white mb-4">
                        Ready to Transform Your Letter Management?
                    </h2>
                    <p className="text-xl text-blue-100 mb-8">
                        Join organizations worldwide who trust SecureSign for their critical document workflows.
                    </p>
                    {!auth.user && (
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link href="/register">
                                <Button size="lg" variant="secondary" className="w-full sm:w-auto">
                                    Start Your Free Trial 🚀
                                </Button>
                            </Link>
                            <Link href="/login">
                                <Button size="lg" variant="outline" className="w-full sm:w-auto border-white text-white hover:bg-white hover:text-blue-600">
                                    Sign In Now 📝
                                </Button>
                            </Link>
                        </div>
                    )}
                </div>
            </div>

            {/* Footer */}
            <footer className="bg-gray-900 py-8 border-t border-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center text-gray-400">
                        <p>&copy; 2024 SecureSign. Built with Laravel & React. Enterprise security guaranteed.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}