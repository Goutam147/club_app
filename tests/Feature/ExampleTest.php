<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        \App\Models\ClubMaster::create([
            'name' => 'Bhimchak Sunrise Club',
            'logo' => 'uploads/logo/bsc_logo.jpeg',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
