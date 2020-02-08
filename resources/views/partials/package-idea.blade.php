<a href="{{ $idea['html_url'] }}" class="block mb-4 no-underline text-near-black border px-4 py-3 hover:border-grey hover:bg-grey-lightest">
    <span class="font-bold text-lg">{{ $idea['title'] }}</span>
    @foreach ($idea['labels'] as $label)
        <div class="text-xs inline-block font-bold rounded-sm px-1 ml-1 shadow-slim" style="background-color: #{{ $label['color'] }};">{{ $label['name'] }}</div>
    @endforeach

    <div class="text-grey-dark text-xs">Suggested by {{ $idea['user']['login'] }}</div>
    <div class="mt-1 text-sm max-w-md">{!! abstractify(markdown($idea['body'])) !!}</div>

    @if ($idea['reactions']['total_count'] > 0)
    <div class="reactions mt-1 text-lg">
        @foreach ($idea['reactions'] as $key => $count)
            @if (! in_array($key, ['url', 'total_count']) && $count > 0)
            <div class="inline-block mr-4">
                {{ translate_github_emoji($key) }}
                <span class="text-grey-dark text-xs">{{ $count }}</span>
            </div>
            @endif
        @endforeach
    </div>
    @endif
</a>
