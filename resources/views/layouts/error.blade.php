@extends ('layouts.app')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                You've reached an error page!
            </div>
            <div class="bg-white p-3 rounded-b">
                @yield('body')
            </div>
        </div>
    </div>
</div>
@endsection
