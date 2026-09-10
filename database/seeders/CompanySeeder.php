<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(User $user): void
    {

        Company::factory()->hasJobApplications(2)->create();

        $company = Company::factory()->create();

        JobApplication::factory(2)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);
    }
}
