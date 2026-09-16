<?php

namespace Database\Factories;

use App\Enums\JobApplications\Status;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
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
        $salaryMin = fake()->numberBetween(50000, 100000);

        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'title' => fake()->jobTitle(),
            'job_url' => fake()->url(),
            'location' => fake()->city(),
            'description' => fake()->paragraph(),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMin + fake()->numberBetween(10000, 30000),
            'status' => fake()->randomElement(array_map(fn ($status) => $status->value, Status::cases())),
            'applied_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
