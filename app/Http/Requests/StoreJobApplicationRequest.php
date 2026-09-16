<?php

namespace App\Http\Requests;

use App\Enums\JobApplications\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'job_url' => ['nullable', 'url:http,https', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_map(fn ($status) => $status->value, Status::cases()))],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', Rule::when($this->filled('salary_min'), 'gte:salary_min')],
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
            'applied_at' => ['nullable', 'date'],
            'follow_up_at' => ['nullable', 'date'],
        ];
    }
}
