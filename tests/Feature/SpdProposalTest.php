<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpdProposalTest extends TestCase
{
    use RefreshDatabase;

    public function test_spd_crud_and_stats_work(): void
    {
        $user = User::factory()->create([
            'is_active' => 1,
        ]);
        $this->actingAs($user, 'sanctum');

        $createResponse = $this->postJson('/api/spd', [
            'orderer_name' => 'Kepala OPD',
            'orderer_nip' => '198001012010011001',
            'orderer_position' => 'Kepala Bidang',
            'employee_name' => 'Budi Santoso',
            'employee_nip' => '199001012015011001',
            'employee_rank' => 'Penata Muda',
            'employee_position' => 'Staff',
            'purpose' => 'Kunjungan kerja',
            'transportation' => 'Mobil Dinas',
            'departure_place' => 'Bandung',
            'destination' => 'Bogor',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-02',
            'budget_estimate' => 1500000,
            'followers' => [
                ['name' => 'Dina', 'nip' => '199202022018021001', 'position' => 'Analisis'],
            ],
            'status' => 'draft',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.status', 'draft');

        $id = $createResponse->json('data.id');

        $statsResponse = $this->getJson('/api/spd/stats');
        $statsResponse->assertStatus(200)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.by_status.draft', 1);

        $showResponse = $this->getJson("/api/spd/{$id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.id', $id);

        $updateResponse = $this->putJson("/api/spd/{$id}", [
            'status' => 'submitted',
            'purpose' => 'Rapat koordinasi',
        ]);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'submitted');

        $deleteResponse = $this->deleteJson("/api/spd/{$id}");
        $deleteResponse->assertStatus(200)
            ->assertJsonPath('message', 'Data SPD berhasil dihapus.');
    }
}
