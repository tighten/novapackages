@if (session('status'))
    <div {{ $attributes->merge(['class' => 'bg-green-100 border border-green-300 text-green-600 text-sm px-4 py-3 rounded']) }}>
        {{ session('status') }}
    </div>
@endif
