@csrf
<div class="jt-form-grid">
    @foreach (['name' => __('Name'), 'website' => __('Website'), 'location' => __('Location')] as $field => $label)
        <div>
            <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'website' ? 'url' : 'text' }}" value="{{ old($field, isset($company) ? $company->{$field} : '') }}" maxlength="255" @required($field === 'name') class="mt-2 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 dark:border-zinc-600">
            @error($field)<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    @endforeach
    <div class="sm:col-span-2">
        <label for="description" class="block text-sm font-medium">{{ __('Description') }}</label>
        <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 dark:border-zinc-600">{{ old('description', $company->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>
    <div class="jt-form-actions">
        <button type="submit" class="jt-primary">{{ __('Save company') }}</button>
        <a href="{{ route('companies.index') }}" class="underline">{{ __('Cancel') }}</a>
    </div>
</div>
