@props([
    'tags' => [],
])

@if(count($tags))
    <ol class="tags">
        @foreach($tags as $tag)
            <li>
                <a class="tag"
                   href="{{ \Hubzero\Facades\Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'), false) }}"
                   rel="tag">
                    {{ $tag->get('raw_tag') }}
                </a>
            </li>
        @endforeach
    </ol>
@endif
