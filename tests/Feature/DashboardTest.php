<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_dashboard_does_not_500()
    {
        $user = User::first();
        if (! $user) {
            $user = User::factory()->create();
        }
        $response = $this->actingAs($user)->get('/dashboard?month=9&year=2026');

        if ($response->status() == 500) {
            echo 'ERROR 500! '.$response->exception->getMessage()."\n";
            echo $response->exception->getFile().':'.$response->exception->getLine()."\n";
        }

        $response->assertStatus(200);
    }
}
