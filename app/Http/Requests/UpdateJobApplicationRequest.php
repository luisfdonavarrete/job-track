<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateJobApplicationRequest extends StoreJobApplicationRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        unset($rules['resume'], $rules['cover_letter']);

        return $rules;
    }

    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->route('jobApplication')->user_id === $this->user()->id;
    }
}
