<?php

namespace Database\Factories;

use App\Models\DigitalSignature;
use App\Models\Letter;
use App\Models\User;
use App\Models\UserKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DigitalSignature>
 */
class DigitalSignatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $signedAt = fake()->dateTimeBetween('-1 month', 'now');
        
        return [
            'letter_id' => Letter::factory(),
            'signer_id' => User::factory()->state(['can_sign' => true]),
            'user_key_id' => UserKey::factory(),
            'signature_data' => base64_encode(fake()->text(256)), // Dummy signature
            'content_hash' => hash('sha256', fake()->text(1000)),
            'algorithm' => 'SHA256withRSA',
            'signed_at' => $signedAt,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}