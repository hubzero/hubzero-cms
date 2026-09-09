{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Cache;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$viewUrl = Route::url('index.php?option=' . $option . '&task=view');
$browseUrl = Route::url('index.php?option=' . $option . '&task=browse');

// Recent tags cloud
$recentFilters = [
    'limit'    => 50,
    'admin'    => 0,
    'sort'     => 'taggedon',
    'sort_Dir' => 'DESC',
];

$ttl = intval($config->get('cache_time', 15));

if ($config->get('cache', 1)) {
    $recentCloud = Cache::get('tags.recent');
    if (!$recentCloud) {
        $recentCloud = $cloud->render('html', $recentFilters, true);
        Cache::put('tags.recent', $recentCloud, $ttl);
    }
} else {
    $recentCloud = $cloud->render('html', $recentFilters, true);
}

// Top tags cloud
$topFilters = [
    'limit'    => 50,
    'admin'    => 0,
    'sort'     => 'objects',
    'sort_Dir' => 'DESC',
];

if ($config->get('cache', 1)) {
    $topCloud = Cache::get('tags.top');
    if (!$topCloud) {
        $topCloud = $cloud->render('html', $topFilters, true);
        Cache::put('tags.top', $topCloud, $ttl);
    }
} else {
    $topCloud = $cloud->render('html', $topFilters, true);
}
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_TAGS_BROWSE_LIST') }}
        </a>
    @endslot

    {{-- Introduction --}}
    <div class="mb-8">
        <x-search-bar
            :action="$viewUrl"
            name="tag"
            :placeholder="Lang::txt('COM_TAGS_SEARCH_LABEL')"
            :buttonLabel="Lang::txt('COM_TAGS_SEARCH')"
            class="mb-4"
        />
        <p class="text-base-content/70">
            {!! Lang::txt('COM_TAGS_ARE') !!}
        </p>
    </div>

    {{-- Tag clouds --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-lg font-semibold mb-4">
                {{ Lang::txt('COM_TAGS_RECENTLY_USED') }}
            </h2>
            @if ($recentCloud)
                {!! $recentCloud !!}
            @else
                <x-empty-state>
                    {{ Lang::txt('COM_TAGS_NO_TAGS') }}
                </x-empty-state>
            @endif
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-4">
                {{ Lang::txt('COM_TAGS_TOP_USED') }}
            </h2>
            @if ($topCloud)
                {!! $topCloud !!}
            @else
                <x-empty-state>
                    {{ Lang::txt('COM_TAGS_NO_TAGS') }}
                </x-empty-state>
            @endif
        </div>
    </div>
</x-page-container>
