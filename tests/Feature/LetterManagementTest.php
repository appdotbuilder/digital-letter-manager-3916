<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_view_letters_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/letters');

        $response->assertStatus(200);
    }

    public function test_users_can_create_letters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/letters/create');

        $response->assertStatus(200);
    }

    public function test_users_can_store_letters(): void
    {
        $user = User::factory()->create();
        $template = LetterTemplate::factory()->create();

        $letterData = [
            'title' => 'Test Letter',
            'content' => 'This is a test letter content.',
            'recipient_name' => 'John Doe',
            'recipient_address' => '123 Test St, Test City',
            'template_id' => $template->id,
        ];

        $response = $this->actingAs($user)->post('/letters', $letterData);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'title' => 'Test Letter',
            'created_by' => $user->id,
        ]);
    }

    public function test_users_can_view_their_letters(): void
    {
        $user = User::factory()->create();
        $letter = Letter::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get("/letters/{$letter->id}");

        $response->assertStatus(200);
    }

    public function test_users_cannot_view_others_letters_without_permission(): void
    {
        $user1 = User::factory()->create(['role' => 'staff']);
        $user2 = User::factory()->create(['role' => 'staff']);
        $letter = Letter::factory()->create(['created_by' => $user2->id]);

        $response = $this->actingAs($user1)->get("/letters/{$letter->id}");

        $response->assertStatus(403);
    }

    public function test_admins_can_view_all_letters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'staff']);
        $letter = Letter::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($admin)->get("/letters/{$letter->id}");

        $response->assertStatus(200);
    }

    public function test_users_can_edit_draft_letters(): void
    {
        $user = User::factory()->create();
        $letter = Letter::factory()->draft()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get("/letters/{$letter->id}/edit");

        $response->assertStatus(200);
    }

    public function test_users_cannot_edit_submitted_letters(): void
    {
        $user = User::factory()->create();
        $letter = Letter::factory()->submitted()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get("/letters/{$letter->id}/edit");

        $response->assertStatus(403);
    }

    public function test_users_can_submit_draft_letters(): void
    {
        $user = User::factory()->create();
        $letter = Letter::factory()->draft()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post("/letters/{$letter->id}/submit");

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status' => 'submitted',
        ]);
    }

    public function test_managers_can_approve_letters(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'can_review' => true]);
        $letter = Letter::factory()->submitted()->create(['assigned_reviewer' => $manager->id]);

        $response = $this->actingAs($manager)->post("/letters/{$letter->id}/approve", [
            'review_notes' => 'Approved for signing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status' => 'approved',
        ]);
    }

    public function test_managers_can_reject_letters(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'can_review' => true]);
        $letter = Letter::factory()->submitted()->create(['assigned_reviewer' => $manager->id]);

        $response = $this->actingAs($manager)->post("/letters/{$letter->id}/reject", [
            'rejection_reason' => 'Needs more details',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status' => 'rejected',
        ]);
    }
}