<x-layouts::app :title="__('Create application')">
    <div class="jt-page">
        <a href="{{ route('job-applications.index') }}" class="jt-back">← {{ __('Back to job applications') }}</a>
        <h1 class="jt-title">{{ __('Create application') }}</h1>
        <p class="-mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Keep the details in one place. You can update them at any time.') }}</p>
        <form class="jt-form-card" method="POST" action="{{ route('job-applications.store') }}">
            
            @include('job-applications._form')
        </form>
    </div>
</x-layouts::app>
