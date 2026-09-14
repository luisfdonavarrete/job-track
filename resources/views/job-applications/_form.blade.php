@csrf
<div class="jt-form-grid">
    @if ($companies->isEmpty())
        <p>{{ __('Add a company before creating an application.') }} <a class="underline" href="{{ route('companies.create') }}">{{ __('Add company') }}</a></p>
    @endif
    <div>
        <label for="company_id" class="block text-sm font-medium">{{ __('Company') }}</label>
        <select id="company_id" name="company_id" required class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-600 dark:bg-zinc-900">
            <option value="">{{ __('Select a company') }}</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $jobApplication->company_id ?? '') === $company->id)>{{ $company->name }}</option>
            @endforeach
        </select>
        @error('company_id')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    @foreach (['title' => 'Title', 'job_url' => 'Job URL', 'location' => 'Location', 'salary_min' => 'Minimum salary', 'salary_max' => 'Maximum salary', 'applied_at' => 'Applied at', 'follow_up_at' => 'Follow-up at'] as $field => $label)
        @php
            $isDate = in_array($field, ['applied_at', 'follow_up_at']);
            $isSalary = in_array($field, ['salary_min', 'salary_max']);
            $value = isset($jobApplication) ? $jobApplication->{$field} : null;
        @endphp
        <div>
            <label for="{{ $field }}" class="block text-sm font-medium">{{ __($label) }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="{{ $isDate ? 'datetime-local' : ($isSalary ? 'number' : ($field === 'job_url' ? 'url' : 'text')) }}" value="{{ old($field, $isDate ? $value?->format('Y-m-d\TH:i') : $value) }}" @required($field === 'title') @if ($isSalary) min="0" max="99999999.99" step="0.01" @elseif (! $isDate) maxlength="255" @endif class="mt-2 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 dark:border-zinc-600">
            @error($field)<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    @endforeach
    <div>
        <label for="status" class="block text-sm font-medium">{{ __('Status') }}</label>
        <select id="status" name="status" required class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-600 dark:bg-zinc-900">
            @foreach (['applied', 'interviewing', 'offered', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(old('status', $jobApplication->status ?? 'applied') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label for="description" class="block text-sm font-medium">{{ __('Description') }}</label>
        <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 dark:border-zinc-600">{{ old('description', $jobApplication->description ?? '') }}</textarea>
        @error('description')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    <div class="jt-form-actions">
        <button type="submit" @disabled($companies->isEmpty()) class="jt-primary disabled:opacity-50">{{ __('Save application') }}</button>
        <a href="{{ route('job-applications.index') }}" class="underline">{{ __('Cancel') }}</a>
    </div>
</div>
