@extends('layouts.app')

@section('top-id', 'vue-app')

@section('title', 'API Details')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex mb-12 items-center justify-between">
        @include('partials.errors')

        <h1 class="text-gray-800 text-3xl font-bold mb-4">API Details</h1>
    </div>

    <div class="bg-white p-8 mb-16 rounded-lg leading-loose text-gray-800 shadow sm:p-12">
        <h3 class="mb-2 text-gray-800 text-lg font-bold">Current public (accessible without API keys) API routes:</h3>

        <ul class="mb-4 leading-normal list-disc pl-10">
            <li>/api/search?q=Ticker</li>
            <li>/api/recent</li>
            <li>/api/popular</li>
            <li>/api/stats</li>
        </ul>

        <h3 class="mb-2 text-gray-800 text-lg font-bold">Current private (accessible only with API keys) API routes:</h3>

        <ul class="mb-8 leading-normal list-disc pl-10">
            <li>/api/packages - paginated, 10 per page</li>
        </ul>

        <strong class="text-gray-800 mb-4">More documentation is coming.</strong>

        <!-- https://laravel-news.com/the-mystical-laravel-dragon - https://twitter.com/robboclancy/status/1139610128825667584/photo/1 -->
        <code class="leading-none"><pre>
                                                  .~))>>
                                                 .~)>>
                                               .~))))>>>
                                             .~))>>             ___
                                           .~))>>)))>>      .-~))>>
                                         .~)))))>>       .-~))>>)>
                                       .~)))>>))))>>  .-~)>>)>
                   )                 .~))>>))))>>  .-~)))))>>)>
                ( )@@*)             //)>))))))  .-~))))>>)>
              ).@(@@               //))>>))) .-~))>>)))))>>)>
            (( @.@).              //))))) .-~)>>)))))>>)>
          ))  )@@*.@@ )          //)>))) //))))))>>))))>>)>
       ((  ((@@@.@@             |/))))) //)))))>>)))>>)>
      )) @@*. )@@ )   (\_(\-\b  |))>)) //)))>>)))))))>>)>
    (( @@@(.@(@ .    _/`-`  ~|b |>))) //)>>)))))))>>)>
     )* @@@ )@*     (@)  (@) /\b|))) //))))))>>))))>>
   (( @. )@( @ .   _/  /    /  \b)) //))>>)))))>>>_._
    )@@ (@@*)@@.  (6///6)- / ^  \b)//))))))>>)))>>   ~~-.
 ( @jgs@@. @@@.*@_ VvvvvV//  ^  \b/)>>))))>>      _.     `bb
  ((@@ @@@*.(@@ . - | o |' \ (  ^   \b)))>>        .'       b`,
   ((@@).*@@ )@ )   \^^^/  ((   ^  ~)_        \  /           b `,
     (@@. (@@ ).     `-'   (((   ^    `\ \ \ \ \|             b  `.
       (*.@*              / ((((        \| | |  \       .       b `.
                         / / (((((  \    \ /  _.-~\     Y,      b  ;
                        / / / (((((( \    \.-~   _.`" _.-~`,    b  ;
                       /   /   `(((((()    )    (((((~      `,  b  ;
                     _/  _/      `"""/   /'                  ; b   ;
                 _.-~_.-~           /  /'                _.'~bb _.'
               ((((~~              / /'              _.'~bb.--~
                                  ((((          __.-~bb.-~
                                              .'  b .~~
                                              :bb ,'
                                              ~~~~
        </pre></code>
    </div>

    <passport-clients></passport-clients>
    <passport-authorized-clients></passport-authorized-clients>

    <br class="mb-4">

    <passport-personal-access-tokens></passport-personal-access-tokens>
</div>
@endsection
