@if ($errors->count() > 0)
    <div class="my-4 text-red">
    @foreach ($errors->all() as $error)
        <div class="">{{ $error }}</div>
    @endforeach
    </div>
@endif
