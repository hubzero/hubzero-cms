{{--
 * Single Solr search result
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $accessLevel = isset($result['access_level']) ? $result['access_level'] : 'public';
@endphp

<div class="search-result" id="{{ $result['id'] }}">
    {{-- Title --}}
    <h4 class="text-base font-semibold mb-1">
        <a href="{{ $result['url'] }}" class="link link-hover link-primary">
            {!! $result['title'] !!}
        </a>
    </h4>

    <div class="flex flex-wrap gap-2 text-sm text-base-content/60 mb-1">
        {{-- Category --}}
        <span class="badge badge-sm">{{ ucfirst($result['hubtype']) }}</span>

        @if(isset($result['date']))
            @php $date = new \Hubzero\Utility\Date($result['date']); @endphp
            <time datetime="{{ $result['date'] }}">
                {{ $date->toLocal('Y-m-d h:mA') }}
            </time>
        @endif

        @if(isset($result['author']))
            <span>{!! $result['authorString'] !!}</span>
        @endif

        @if(User::authorise('core.admin') && isset($result['access_level']))
            <span class="badge badge-sm badge-outline">Access: {{ $result['access_level'] }}</span>
        @endif
    </div>

    @if(isset($result['snippet']) && $result['snippet'] != '…')
        <p class="text-sm text-base-content/80 mb-2">
            {!! $result['snippet'] !!}
        </p>
    @endif

    {{-- Tags from child documents or tags array --}}
    @if(isset($result['_childDocuments_']) && $tagSearch)
        <div class="flex flex-wrap gap-1 mb-2">
            @php
                $baseTagUrl = Route::url(
                    'index.php?option=com_search&terms=' . $terms
                );
            @endphp
            @foreach($result['_childDocuments_'] as $tag)
                @if(!empty($tag['title'][0]))
                    @php
                        $description = !empty($tag['description'])
                            ? $tag['description']
                            : $tag['title'][0];
                        $tagHref = $baseTagUrl . '&tags=' . $description;
                    @endphp
                    <a class="badge badge-outline badge-sm"
                       href="{{ $tagHref }}"
                       data-tag="{{ $description }}">
                        {{ $tag['title'][0] }}
                    </a>
                @endif
            @endforeach
        </div>
    @elseif(isset($result['tags']))
        <div class="flex flex-wrap gap-1 mb-2">
            @foreach($result['tags'] as $tag)
                @php
                    $tagUrl = Route::url(
                        'index.php?option=com_search&terms=' . $tag
                    );
                @endphp
                <a class="badge badge-outline badge-sm" href="{{ $tagUrl }}">
                    {{ $tag }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Result URL --}}
    @if(isset($result['url']))
        <p class="text-xs text-base-content/40">
            <a href="{{ $result['url'] }}" class="link link-hover">
                {{ $result['url'] }}
            </a>
        </p>
    @endif
</div>
