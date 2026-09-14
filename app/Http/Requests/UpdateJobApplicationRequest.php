<?php

namespace App\Http\Requests;

class UpdateJobApplicationRequest extends StoreJobApplicationRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->route('job_application')->user_id === $this->user()->id;
    }
}
