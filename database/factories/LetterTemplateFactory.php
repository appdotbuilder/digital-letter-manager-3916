<?php

namespace Database\Factories;

use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LetterTemplate>
 */
class LetterTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Template',
            'description' => fake()->sentence(),
            'content' => $this->generateTemplateContent(),
            'placeholders' => [
                'DATE' => 'Current date',
                'RECIPIENT_NAME' => 'Name of the recipient',
                'SENDER_NAME' => 'Name of the sender',
                'SUBJECT' => 'Letter subject',
                'CONTENT' => 'Main letter content',
            ],
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Generate template content with placeholders.
     */
    protected function generateTemplateContent(): string
    {
        return '<div style="margin-bottom: 20px;">
            <p><strong>{{DATE}}</strong></p>
        </div>

        <div style="margin-bottom: 20px;">
            <p>{{RECIPIENT_NAME}}<br>
            {{RECIPIENT_ADDRESS}}</p>
        </div>

        <div style="margin-bottom: 20px;">
            <p><strong>Subject: {{SUBJECT}}</strong></p>
        </div>

        <div style="margin-bottom: 20px;">
            <p>Dear {{RECIPIENT_NAME}},</p>
        </div>

        <div style="margin-bottom: 20px; line-height: 1.6;">
            <p>{{CONTENT}}</p>
        </div>

        <div>
            <p>Sincerely,<br><br>
            {{SENDER_NAME}}<br>
            {{SENDER_TITLE}}</p>
        </div>';
    }
}