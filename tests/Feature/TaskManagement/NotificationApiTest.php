<?php

namespace Tests\Feature\TaskManagement;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_their_notifications(): void
    {
        $user = User::factory()->create(['is_active' => 1]);
        Notification::create([
            'user_id' => $user->id,
            'type' => 'TASK_ASSIGNED',
            'title' => 'New Task Assigned',
            'message' => 'You were assigned a task.',
            'is_read' => false,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/task-management/notifications');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'New Task Assigned']);
    }
}
