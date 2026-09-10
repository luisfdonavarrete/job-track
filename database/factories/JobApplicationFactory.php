<?php

namespace Database\Factories;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->jobTitle(),
            'job_url' => fake()->url(),
            'location' => fake()->city(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['applied', 'interviewing', 'offered', 'rejected']),
            'applied_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
