    <flux:toast />
    <div x-data x-init="@if (session('success')) $flux.toast('{{ session('success') }}', { variant: 'success' }) @endif"></div>
