<x-layouts::app :title="$jobApplication->title">
    <div class="jt-page jt-detail">
        <a href="{{ route('job-applications.index') }}" class="underline">{{ __('Back to applications') }}</a>
        <h1 class="jt-title">{{ $jobApplication->title }}</h1>
        @if (session('status'))<p role="status" class="jt-notice">{{ session('status') }}</p>@endif
        <dl class="jt-details">
            <div><dt class="font-medium">{{ __('Company') }}</dt><dd>{{ $jobApplication->company->name }}</dd></div>
            @foreach (['status' => 'Status', 'job_url' => 'Job URL', 'location' => 'Location', 'description' => 'Description', 'salary_min' => 'Minimum salary', 'salary_max' => 'Maximum salary'] as $field => $label)
                <div><dt class="font-medium">{{ __($label) }}</dt><dd class="mt-1 whitespace-pre-line break-words">
                    @if ($field === 'status')
                        {{ $jobApplication->status->toString() }}
                    @else
                        {{ $jobApplication->{$field} ?? __('Not specified') }}
                    @endif
                </dd></div>
            @endforeach
            <div><dt class="font-medium">{{ __('Applied at') }}</dt><dd>{{ $jobApplication->applied_at?->format('M j, Y H:i') ?? __('Not recorded') }}</dd></div>
            <div><dt class="font-medium">{{ __('Follow-up at') }}</dt><dd>{{ $jobApplication->follow_up_at?->format('M j, Y H:i') ?? __('Not scheduled') }}</dd></div>
        </dl>
        <div class="flex items-center gap-4">
            <a href="{{ route('job-applications.edit', $jobApplication) }}" class="jt-primary">{{ __('Edit application') }}</a>
            <form method="POST" action="{{ route('job-applications.destroy', $jobApplication) }}" x-data @submit="if (! window.confirm('Delete this application?')) { $event.preventDefault() }">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-500 px-4 py-2 text-red-600 dark:text-red-400">{{ __('Delete application') }}</button>
            </form>
        </div>
    </div>
</x-layouts::app>
