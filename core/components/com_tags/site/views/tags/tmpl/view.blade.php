{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

$tagstring = str_replace(['%20', ' ', '+'], ',', $tagstring);
$tagsUrl = Route::url('index.php?option=' . $option);
$formUrl = Route::url('index.php?option=' . $option);

$name = Lang::txt('COM_TAGS_ALL_CATEGORIES');
$activeTotal = $total;
$here = 'index.php?option=' . $option . '&tag=' . $tagstring
    . ($filters['sort'] ? '&sort=' . $filters['sort'] : '');

// Build category list with "All" prepended
$all = [
    'name'    => '',
    'title'   => Lang::txt('COM_TAGS_ALL_CATEGORIES'),
    'total'   => $total,
    'results' => null,
    'sql'     => '',
];
$cats = $categories;
array_unshift($cats, $all);

// Process categories for sidebar
$catLinks = [];
foreach ($cats as $cat) {
    if (!$cat['total'] > 0) {
        continue;
    }

    $blob = $cat['name'] ?: '';
    $catUrl = Route::url($here . ($blob ? '&area=' . stripslashes($blob) : ''));
    $isActive = ($cat['name'] == $active && !$parent);

    if ($isActive) {
        $name = $cat['title'];
        $activeTotal = $cat['total'];
        Pathway::append($cat['title'], $here . '&area=' . stripslashes($blob));
    }

    $catItem = [
        'url' => $catUrl,
        'title' => e(stripslashes($cat['title'])),
        'total' => $cat['total'],
        'active' => $isActive,
        'children' => [],
    ];

    // Subcategories
    if (isset($cat['children']) && is_array($cat['children'])) {
        foreach ($cat['children'] as $subcat) {
            if ($subcat['total'] > 0) {
                $subBlob = $subcat['name'] ?: '';
                $subParent = $cat['name'];
                $subActive = (
                    $subcat['name'] == $active
                    && isset($parent)
                    && $parent == $subParent
                );

                if ($subActive) {
                    $name = $subcat['title'];
                    $activeTotal = $subcat['total'];
                    Pathway::append(
                        $subcat['title'],
                        $here . '&area=' . stripslashes($subBlob)
                    );
                }

                $subcatUrl = Route::url(
                    $here . '&parent=' . $subParent
                    . '&area=' . stripslashes($subBlob)
                );

                $catItem['children'][] = [
                    'url' => $subcatUrl,
                    'title' => e(stripslashes($subcat['title'])),
                    'total' => $subcat['total'],
                    'active' => $subActive,
                ];
            }
        }
    }

    $catLinks[] = $catItem;
}

// Sort URLs
$sortBase = 'index.php?option=' . $option
    . '&tag=' . $tagstring
    . '&area=' . $active
    . ($parent ? '&parent=' . $parent : '');
$sortSuffix = '&limit=' . $filters['limit']
    . '&start=' . $filters['start'];
$titleUrl = Route::url($sortBase . '&sort=title' . $sortSuffix);
$dateUrl = Route::url($sortBase . '&sort=date' . $sortSuffix);
$titleActive = ($filters['sort'] == 'title');
$dateActive = ($filters['sort'] == 'date' || $filters['sort'] == '');

// Pagination
$max = $filters['limit'] + $filters['start'];
$ttl = ($activeTotal > $max) ? $max : $activeTotal;
if ($activeTotal && !$ttl) {
    $ttl = $activeTotal;
}

$base = rtrim(Request::base(), '/');

$pageNav = $__view->pagination(
    $activeTotal,
    $filters['start'],
    $filters['limit']
);
$pageNav->setAdditionalUrlParam('task', '');
$pageNav->setAdditionalUrlParam('tag', $tagstring);
$pageNav->setAdditionalUrlParam('area', $active);
$pageNav->setAdditionalUrlParam('sort', $filters['sort']);
if ($parent) {
    $pageNav->setAdditionalUrlParam('parent', $parent);
}
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $tagsUrl }}">
            {{ Lang::txt('COM_TAGS_MORE_TAGS') }}
        </a>
    @endslot

    @slot('sidebar')
        <x-sidebar-card :title="Lang::txt('COM_TAGS_CATEGORIES')" class="border border-base-300 mt-10">
                @if (count($catLinks))
                    <ul class="menu menu-sm p-0">
                        @foreach ($catLinks as $catItem)
                            <li>
                                <a href="{{ $catItem['url'] }}"
                                    @class(['active' => $catItem['active']])>
                                    {{ $catItem['title'] }}
                                    <span class="badge badge-ghost badge-sm">
                                        {{ $catItem['total'] }}
                                    </span>
                                </a>
                                @if (!empty($catItem['children']))
                                    <ul>
                                        @foreach ($catItem['children'] as $sub)
                                            <li>
                                                <a href="{{ $sub['url'] }}"
                                                    @class(['active' => $sub['active']])>
                                                    {{ $sub['title'] }}
                                                    <span class="badge badge-ghost badge-xs">
                                                        {{ $sub['total'] }}
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="text-xs text-base-content/60 mt-3">
                    {!! Lang::txt('COM_TAGS_RESULTS_NOTE') !!}
                </p>
        </x-sidebar-card>
    @endslot

    {{-- Search form --}}
    <x-search-bar
        :action="$formUrl"
        name="tag"
        :query="$search"
        :placeholder="Lang::txt('COM_TAGS_SEARCH_LABEL')"
        :buttonLabel="Lang::txt('COM_TAGS_SEARCH')"
    >
        <input type="hidden" name="task" value="view" />
        @if ($active)
            <input type="hidden" name="area" value="{{ e($active) }}" />
        @endif
        @if ($parent)
            <input type="hidden" name="parent" value="{{ e($parent) }}" />
        @endif
        @if ($filters['sort'])
            <input type="hidden" name="sort" value="{{ e($filters['sort']) }}" />
        @endif
    </x-search-bar>

    {{-- Tag descriptions --}}
    @foreach ($tags as $tagobj)
        @if ($tagobj->get('description') != '')
            <div class="card bg-base-100 border border-base-300 mb-6">
                <div class="card-body p-4">
                    <h3 class="card-title text-sm">
                        {{ Lang::txt('COM_TAGS_DESCRIPTION') }}
                    </h3>
                    <div class="prose max-w-none text-sm">
                        {!! stripslashes($tagobj->get('description')) !!}
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Sort tabs --}}
    <div role="tablist" class="tabs tabs-border mb-6">
        <a role="tab" href="{{ $titleUrl }}"
            @class(['tab', 'tab-active' => $titleActive])>
            {{ Lang::txt('COM_TAGS_OPT_TITLE') }}
        </a>
        <a role="tab" href="{{ $dateUrl }}"
            @class(['tab', 'tab-active' => $dateActive])>
            {{ Lang::txt('COM_TAGS_OPT_DATE') }}
        </a>
    </div>

    {{-- Results --}}
    <h3 class="text-lg font-semibold mb-4">
        {{ e(stripslashes($name)) }}
        <span class="text-sm font-normal text-base-content/60">
            ({{ Lang::txt('COM_TAGS_RESULTS_THROUGH_OF', $filters['start'] + 1, $ttl, $activeTotal) }})
        </span>
    </h3>

    @if ($results)
        <ul class="list bg-base-100 rounded-box shadow-sm">
            @foreach ($results as $row)
                @php
                $section = ucfirst($row->section ?? '');
                $obj = 'Plugins\\Tags\\' . $section . '\\' . $section;
                $hasCustomOut = method_exists($obj, 'out');

                if (!$hasCustomOut) {
                    if (strstr($row->href, 'index.php')) {
                        $row->href = Route::url($row->href);
                    }
                }
                @endphp

                @if ($hasCustomOut)
                    {!! call_user_func([$obj, 'out'], $row) !!}
                @else
                    <li class="list-row">
                        <div class="list-col-grow">
                            <a class="link link-hover font-medium"
                                href="{{ $row->href }}">
                                {{ \Hubzero\Utility\Sanitize::clean($row->title) }}
                            </a>
                            <div class="text-sm text-base-content/60">
                                @if ($row->section)
                                    <span class="badge badge-ghost badge-sm mr-1">
                                        {{ ucfirst($row->section) }}
                                    </span>
                                @endif
                                @if (!empty($row->ftext))
                                    {{ \Hubzero\Utility\Str::truncate(strip_tags($row->ftext), 200) }}
                                @endif
                            </div>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>

        {!! $pageNav->render() !!}
    @else
        <x-empty-state>
            {{ Lang::txt('COM_TAGS_NO_RESULTS') }}
        </x-empty-state>
    @endif

</x-page-container>
