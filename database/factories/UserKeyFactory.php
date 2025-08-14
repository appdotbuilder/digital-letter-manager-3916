<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserKey>
 */
class UserKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a dummy key fingerprint
        $fingerprint = hash('sha256', fake()->uuid() . fake()->unixTime());
        
        return [
            'user_id' => User::factory(),
            'public_key' => $this->generateDummyPublicKey(),
            'encrypted_private_key' => fake()->text(500), // Dummy encrypted key
            'key_fingerprint' => $fingerprint,
            'generated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    /**
     * Generate a dummy public key for testing.
     */
    protected function generateDummyPublicKey(): string
    {
        return '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA' . base64_encode(fake()->text(200)) . '
-----END PUBLIC KEY-----';
    }

    /**
     * Indicate that the key is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}