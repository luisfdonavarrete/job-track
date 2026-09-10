<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demoUser = User::firstOrCreate(['email' => 'test@example.com'], User::factory()->raw([
            'name' => 'Test User',
        ]));

        $users = User::factory()->count(2)->create()->prepend($demoUser);

        $this->call(CompanySeeder::class);

        foreach (Company::all() as $company) {
            foreach ($users as $user) {
                JobApplication::factory()
                    ->for($user)
                    ->for($company)
                    ->count(4)
                    ->sequence(
                        ['status' => 'applied', 'follow_up_at' => now()->addWeek()],
                        ['status' => 'interviewing', 'follow_up_at' => now()->addDays(2)],
                        ['status' => 'offered', 'follow_up_at' => null],
                        ['status' => 'rejected', 'follow_up_at' => null],
                    )
                    ->create();
            }
        }
    }
}
