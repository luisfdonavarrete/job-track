<x-layouts::app :title="$company->name">
    <div class="jt-page jt-detail">
        <a href="{{ route('companies.index') }}" class="underline">{{ __('Back to companies') }}</a>
        <h1 class="jt-title">{{ $company->name }}</h1>
        @if (session('status'))<p role="status" class="jt-notice">{{ session('status') }}</p>@endif
        @error('company')<p role="alert" class="text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        <dl class="jt-details">
            <div><dt class="font-medium">{{ __('Website') }}</dt><dd class="mt-1 break-words">{{ $company->website ?: __('Not specified') }}</dd></div>
            <div><dt class="font-medium">{{ __('Location') }}</dt><dd class="mt-1">{{ $company->location ?: __('Not specified') }}</dd></div>
            <div><dt class="font-medium">{{ __('Description') }}</dt><dd class="mt-1 whitespace-pre-line">{{ $company->description ?: __('No description provided') }}</dd></div>
        </dl>
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('companies.edit', $company) }}" class="jt-primary">{{ __('Edit company') }}</a>
            <form method="POST" action="{{ route('companies.destroy', $company) }}" x-data @submit="if (! window.confirm('Delete this company?')) { $event.preventDefault() }">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-500 px-4 py-2 text-red-600 dark:text-red-400">{{ __('Delete company') }}</button>
            </form>
        </div>
    </div>
</x-layouts::app>
