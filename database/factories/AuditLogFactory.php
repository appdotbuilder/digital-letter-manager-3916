<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventTypes = [
            'letter.created',
            'letter.submitted',
            'letter.reviewed',
            'letter.signed',
            'auth.login',
            'auth.login_failed',
            'key.generated',
        ];

        $eventType = fake()->randomElement($eventTypes);
        $performedAt = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'event_type' => $eventType,
            'description' => $this->generateDescription($eventType),
            'event_data' => $this->generateEventData($eventType),
            'user_id' => fake()->boolean(80) ? User::factory() : null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'auditable_type' => null,
            'auditable_id' => null,
            'performed_at' => $performedAt,
        ];
    }

    /**
     * Generate description based on event type.
     */
    protected function generateDescription(string $eventType): string
    {
        return match ($eventType) {
            'letter.created' => 'Letter "' . fake()->sentence(3) . '" was created',
            'letter.submitted' => 'Letter "' . fake()->sentence(3) . '" was submitted for review',
            'letter.reviewed' => 'Letter "' . fake()->sentence(3) . '" was reviewed and approved',
            'letter.signed' => 'Letter "' . fake()->sentence(3) . '" was digitally signed',
            'auth.login' => 'User ' . fake()->name() . ' logged in',
            'auth.login_failed' => 'Failed login attempt for email: ' . fake()->email(),
            'key.generated' => 'New signing key generated for ' . fake()->name(),
            default => 'System event occurred',
        };
    }

    /**
     * Generate event data based on event type.
     */
    protected function generateEventData(string $eventType): array
    {
        return match ($eventType) {
            'letter.created' => [
                'letter_id' => fake()->numberBetween(1, 100),
                'title' => fake()->sentence(3),
                'status' => 'draft',
            ],
            'letter.submitted' => [
                'letter_id' => fake()->numberBetween(1, 100),
                'title' => fake()->sentence(3),
                'previous_status' => 'draft',
                'new_status' => 'submitted',
            ],
            'letter.reviewed' => [
                'letter_id' => fake()->numberBetween(1, 100),
                'title' => fake()->sentence(3),
                'action' => fake()->randomElement(['approved', 'rejected']),
                'new_status' => fake()->randomElement(['approved', 'rejected']),
            ],
            'letter.signed' => [
                'letter_id' => fake()->numberBetween(1, 100),
                'title' => fake()->sentence(3),
                'signature_id' => fake()->numberBetween(1, 50),
                'signed_at' => fake()->dateTimeBetween('-1 month', 'now')->format('c'),
            ],
            'auth.login' => [
                'user_id' => fake()->numberBetween(1, 10),
                'email' => fake()->email(),
                'role' => fake()->randomElement(['staff', 'manager', 'boss', 'admin']),
            ],
            'auth.login_failed' => [
                'email' => fake()->email(),
                'attempt_time' => fake()->dateTimeBetween('-1 week', 'now')->format('c'),
            ],
            'key.generated' => [
                'user_id' => fake()->numberBetween(1, 10),
                'key_fingerprint' => hash('sha256', fake()->uuid()),
                'key_size' => '2048',
            ],
            default => [],
        };
    }
}