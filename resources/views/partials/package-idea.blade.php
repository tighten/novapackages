<a href="{{ $idea['html_url'] }}" class="block mb-4 no-underline text-gray-900 border border-gray-300 px-4 py-3 hover:border-gray-400 hover:bg-gray-100">
    <span class="font-bold text-lg">{{ $idea['title'] }}</span>
    @foreach ($idea['labels'] as $label)
        <div class="text-xs inline-block font-bold rounded-sm px-1 ml-1 shadow-slim" style="background-color: #{{ $label['color'] }};">{{ $label['name'] }}</div>
    @endforeach

    <div class="text-gray-600 text-xs">Suggested by {{ $idea['user']['login'] }}</div>
    <div class="mt-1 text-sm max-w-md">{!! abstractify(markdown($idea['body'])) !!}</div>

    @if ($idea['reactions']['total_count'] > 0)
    <div class="reactions mt-1 text-lg">
        @foreach ($idea['reactions'] as $key => $count)
            @if (! in_array($key, ['url', 'total_count']) && $count > 0)
            <div class="inline-block mr-4">
                {{ translate_github_emoji($key) }}
                <span class="text-gray-500 text-xs">{{ $count }}</span>
            </div>
            @endif
        @endforeach
    </div>
    @endif
</a>
