<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoverTest extends TestCase
{
    public function test_rover_can_move_forward()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'f'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 0,
                    'y' => 1
                ]);
    }

    public function test_rover_can_turn_left()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'lf'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 199, // Wrapping around the 200x200 world
                    'y' => 0
                ]);
    }

    public function test_rover_can_turn_right()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'rf'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 1,
                    'y' => 0
                ]);
    }

    public function test_rover_stops_at_obstacle()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'ff',
            'obstacles' => [[0, 2]]
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'obstacle',
                    'x' => 0,
                    'y' => 1
                ]);
    }

    public function test_rover_wraps_around_world_boundaries()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 199,
            'y' => 199,
            'direction' => 'E',
            'commands' => 'f'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 0,
                    'y' => 199
                ]);
    }

    public function test_complex_command_sequence()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'FFRFFFRL'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 3,
                    'y' => 2
                ]);
    }

    public function test_validation_requires_x_coordinate()
    {
        $response = $this->postJson('/api/rover/execute', [
            'y' => 0,
            'direction' => 'N',
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_requires_y_coordinate()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'direction' => 'N',
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_requires_direction()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_requires_commands()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_x_coordinate_range()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 200,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_y_coordinate_range()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 200,
            'direction' => 'N',
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_validation_direction_values()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'X',
            'commands' => 'f'
        ]);

        $response->assertStatus(422);
    }

    public function test_case_insensitive_commands()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'FFRFFFRL'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'ok',
                    'x' => 3,
                    'y' => 2
                ]);
    }
} 