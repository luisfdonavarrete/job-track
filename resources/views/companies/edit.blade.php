<x-layouts::app :title="__('Edit company')">
    <div class="jt-page">
        <a href="{{ route('companies.index') }}" class="jt-back">← {{ __('Back to companies') }}</a>
        <h1 class="jt-title">{{ __('Edit company') }}</h1>
        <p class="-mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Keep the details in one place. You can update them at any time.') }}</p>
        <form class="jt-form-card" method="POST" action="{{ route('companies.update', $company) }}">
            @method('PUT')
            @include('companies._form')
        </form>
    </div>
</x-layouts::app>
