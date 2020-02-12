@extends('layouts.app')

@section('content')
<div class="flex items-center mx-4 pb-8">
    <div class="w-full md:w-1/2 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Enter your email
            </div>
            <div class="bg-white p-3 rounded-b">
                <p class="mb-4 text-lg font-bold">Whoops!</p>
                <p class="mb-4">Your GitHub OAuth handshake didn't give us an email address. Probably privacy settings. But we need it to operate. Could you hook us up?</p>

                @include('partials.errors')

                <form method="post">
                    @csrf

                    <label for="email" class="block italic font-bold mb-1">Email address</label>
                    <input name="email" id="email" type="email" placeholder="you@yours.com" class="border border-gray-600 p-2 block mb-2" required>
                    <input type="submit" value="Save" class="border border-gray-600 p-1 px-8">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
