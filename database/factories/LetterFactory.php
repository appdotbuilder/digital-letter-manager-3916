<?php

namespace Database\Factories;

use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'submitted', 'under_review', 'approved', 'signed', 'rejected']);
        
        return [
            'title' => fake()->sentence(4),
            'content' => $this->generateLetterContent(),
            'status' => $status,
            'recipient_name' => fake()->name(),
            'recipient_address' => fake()->address(),
            'reference_number' => null, // Will be generated after creation
            'template_id' => LetterTemplate::factory(),
            'created_by' => User::factory(),
            'assigned_reviewer' => $status !== 'draft' ? User::factory()->state(['can_review' => true]) : null,
            'assigned_signer' => in_array($status, ['approved', 'signed']) ? User::factory()->state(['can_sign' => true]) : null,
            'submitted_at' => $status !== 'draft' ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'reviewed_at' => in_array($status, ['approved', 'signed', 'rejected']) ? fake()->dateTimeBetween('-2 weeks', 'now') : null,
            'signed_at' => $status === 'signed' ? fake()->dateTimeBetween('-1 week', 'now') : null,
            'rejection_reason' => $status === 'rejected' ? fake()->paragraph() : null,
            'review_notes' => in_array($status, ['approved', 'signed']) ? fake()->sentence() : null,
        ];
    }

    /**
     * Generate realistic letter content.
     */
    protected function generateLetterContent(): string
    {
        $date = fake()->date('F j, Y');
        $content = "<div style=\"margin-bottom: 20px;\">
            <p><strong>{$date}</strong></p>
        </div>

        <div style=\"margin-bottom: 20px;\">
            <p>" . fake()->name() . "<br>
            " . fake()->jobTitle() . "<br>
            " . fake()->company() . "<br>
            " . fake()->address() . "</p>
        </div>

        <div style=\"margin-bottom: 20px;\">
            <p><strong>Subject: " . fake()->sentence(6) . "</strong></p>
        </div>

        <div style=\"margin-bottom: 20px;\">
            <p>Dear " . fake()->firstName() . ",</p>
        </div>

        <div style=\"margin-bottom: 20px; line-height: 1.6;\">
            <p>" . fake()->paragraph(4) . "</p>
            <p>" . fake()->paragraph(3) . "</p>
            <p>" . fake()->paragraph(2) . "</p>
        </div>

        <div style=\"margin-bottom: 20px;\">
            <p>Thank you for your attention to this matter.</p>
        </div>

        <div>
            <p>Sincerely,<br><br>
            " . fake()->name() . "<br>
            " . fake()->jobTitle() . "<br>
            " . fake()->company() . "</p>
        </div>";

        return $content;
    }

    /**
     * Indicate that the letter is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
            'reviewed_at' => null,
            'signed_at' => null,
            'assigned_reviewer' => null,
            'assigned_signer' => null,
        ]);
    }

    /**
     * Indicate that the letter is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'submitted_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'reviewed_at' => null,
            'signed_at' => null,
        ]);
    }

    /**
     * Indicate that the letter is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submitted_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'signed_at' => null,
            'review_notes' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the letter is signed.
     */
    public function signed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'signed',
            'submitted_at' => fake()->dateTimeBetween('-3 weeks', '-2 weeks'),
            'reviewed_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'signed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'review_notes' => fake()->sentence(),
        ]);
    }
}