<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true).' Recipe',
            'user_id' => User::factory(),
            'preset_id' => (string) fake()->numberBetween(100, 200),
            'location' => fake()->randomElement(['NL', 'US', 'FI', 'DE', 'IS', 'TR', 'UK', 'ES', 'IT', 'PL', 'CH', 'FR']),
            'os_id' => fake()->numberBetween(180, 200),
            'software_id' => fake()->optional()->numberBetween(1, 50),
            'traffic_plan_id' => fake()->numberBetween(20, 40),
            'deploy_period' => fake()->randomElement(['monthly', 'quarterly', 'semi-annually', 'annually']),
            'ssh_key' => fake()->optional()->passthrough('ssh-rsa AAAAB3...'),
            'post_install_script' => fake()->optional()->text(200),
            'post_install_callback' => fake()->optional()->url(),
        ];
    }
}
