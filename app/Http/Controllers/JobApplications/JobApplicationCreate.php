<?php

namespace App\Http\Controllers\JobApplications;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Contracts\View\View;

class JobApplicationCreate extends Controller
{
    public function __invoke(): View
    {
        return view('job-applications.create', ['companies' => Company::query()->orderBy('name')->get()]);
    }
}
