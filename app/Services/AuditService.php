<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Log an audit event.
     *
     * @param string $eventType
     * @param string $description
     * @param Model|null $auditable
     * @param User|null $user
     * @param array $eventData
     * @param Request|null $request
     * @return AuditLog
     */
    public function log(
        string $eventType,
        string $description,
        ?Model $auditable = null,
        ?User $user = null,
        array $eventData = [],
        ?Request $request = null
    ): AuditLog {
        $request = $request ?: request();
        
        return AuditLog::create([
            'event_type' => $eventType,
            'description' => $description,
            'event_data' => $eventData,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'performed_at' => now(),
        ]);
    }

    /**
     * Log a letter creation event.
     */
    public function logLetterCreated(Model $letter, User $user, Request $request = null): AuditLog
    {
        /** @var \App\Models\Letter $letter */
        return $this->log(
            'letter.created',
            "Letter '{$letter->title}' was created",
            $letter,
            $user,
            [
                'letter_id' => $letter->id,
                'title' => $letter->title,
                'status' => $letter->status,
            ],
            $request
        );
    }

    /**
     * Log a letter submission event.
     */
    public function logLetterSubmitted(Model $letter, User $user, Request $request = null): AuditLog
    {
        /** @var \App\Models\Letter $letter */
        return $this->log(
            'letter.submitted',
            "Letter '{$letter->title}' was submitted for review",
            $letter,
            $user,
            [
                'letter_id' => $letter->id,
                'title' => $letter->title,
                'previous_status' => 'draft',
                'new_status' => $letter->status,
            ],
            $request
        );
    }

    /**
     * Log a letter review event.
     */
    public function logLetterReviewed(Model $letter, User $user, string $action, ?string $notes = null, Request $request = null): AuditLog
    {
        /** @var \App\Models\Letter $letter */
        return $this->log(
            'letter.reviewed',
            "Letter '{$letter->title}' was {$action} by {$user->name}",
            $letter,
            $user,
            [
                'letter_id' => $letter->id,
                'title' => $letter->title,
                'action' => $action,
                'notes' => $notes,
                'new_status' => $letter->status,
            ],
            $request
        );
    }

    /**
     * Log a letter signing event.
     */
    public function logLetterSigned(Model $letter, User $user, string $signatureId, Request $request = null): AuditLog
    {
        /** @var \App\Models\Letter $letter */
        return $this->log(
            'letter.signed',
            "Letter '{$letter->title}' was digitally signed by {$user->name}",
            $letter,
            $user,
            [
                'letter_id' => $letter->id,
                'title' => $letter->title,
                'signature_id' => $signatureId,
                'signed_at' => now()->toISOString(),
            ],
            $request
        );
    }

    /**
     * Log a user login event.
     */
    public function logUserLogin(User $user, Request $request = null): AuditLog
    {
        return $this->log(
            'auth.login',
            "User {$user->name} logged in",
            $user,
            $user,
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            $request
        );
    }

    /**
     * Log a failed login attempt.
     */
    public function logLoginFailed(string $email, Request $request = null): AuditLog
    {
        return $this->log(
            'auth.login_failed',
            "Failed login attempt for email: {$email}",
            null,
            null,
            [
                'email' => $email,
                'attempt_time' => now()->toISOString(),
            ],
            $request
        );
    }

    /**
     * Log a key generation event.
     */
    public function logKeyGenerated(User $user, string $fingerprint, Request $request = null): AuditLog
    {
        return $this->log(
            'key.generated',
            "New signing key generated for {$user->name}",
            null,
            $user,
            [
                'user_id' => $user->id,
                'key_fingerprint' => $fingerprint,
                'key_size' => '2048',
            ],
            $request
        );
    }

    /**
     * Log signature verification attempts.
     */
    public function logSignatureVerification(string $letterId, bool $verified, ?User $user = null, Request $request = null): AuditLog
    {
        $status = $verified ? 'successful' : 'failed';
        
        return $this->log(
            'signature.verification',
            "Signature verification {$status} for letter ID: {$letterId}",
            null,
            $user,
            [
                'letter_id' => $letterId,
                'verification_result' => $verified,
                'verification_time' => now()->toISOString(),
            ],
            $request
        );
    }

    /**
     * Log PDF export events.
     */
    public function logPdfExported(Model $letter, User $user, Request $request = null): AuditLog
    {
        /** @var \App\Models\Letter $letter */
        return $this->log(
            'letter.pdf_exported',
            "PDF exported for letter '{$letter->title}'",
            $letter,
            $user,
            [
                'letter_id' => $letter->id,
                'title' => $letter->title,
                'export_time' => now()->toISOString(),
            ],
            $request
        );
    }
}