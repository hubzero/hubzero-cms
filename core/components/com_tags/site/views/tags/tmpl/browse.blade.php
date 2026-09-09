{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;

$browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
$tagsUrl = Route::url('index.php?option=' . $option);

$canEdit = $config->get('access-edit-tag');
$canDelete = $config->get('access-delete-tag');

$filterParams = '&search=' . urlencode($filters['search'])
    . '&limit=' . Request::getInt('limit', 25)
    . '&limitstart=' . Request::getInt('limitstart', 0);

// Sort URLs
$sort = $filters['sort'];
$sortDir = $filters['sort_Dir'];

$popActive = ($sort == 'total');
$popDir = $popActive ? ($sortDir == 'desc' ? 'asc' : 'desc') : 'asc';
$popUrl = Route::url(
    'index.php?option=' . $option
    . '&task=browse&sort=total&sortdir=' . $popDir . $filterParams
);

$alphaActive = ($sort == '' || $sort == 'raw_tag');
$alphaDir = $alphaActive ? ($sortDir == 'desc' ? 'asc' : 'desc') : 'asc';
$alphaUrl = Route::url(
    'index.php?option=' . $option
    . '&task=browse&sort=raw_tag&sortdir=' . $alphaDir . $filterParams
);

// Pagination
$pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
$pageNav->setAdditionalUrlParam('search', $filters['search']);
$pageNav->setAdditionalUrlParam('sort', $filters['sort']);
$pageNav->setAdditionalUrlParam('sortdir', $filters['sort_Dir']);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $tagsUrl }}">
            {{ Lang::txt('COM_TAGS_MORE_TAGS') }}
        </a>
    @endslot

    @slot('sidebar')
        <x-sidebar-card class="border border-base-300 mt-10">
            <p class="text-sm text-base-content/70">
                {{ Lang::txt('COM_TAGS_BROWSE_EXPLANATION') }}
            </p>
            <div class="mt-3">
                <p class="text-sm font-semibold">
                    {!! Lang::txt('COM_TAGS_WHATS_AN_ALIAS') !!}
                </p>
                <p class="text-sm text-base-content/70 mt-1">
                    {!! Lang::txt('COM_TAGS_ALIAS_EXPLANATION') !!}
                </p>
            </div>
        </x-sidebar-card>
    @endslot

    {{-- Search --}}
    <x-search-bar
        :action="$browseUrl"
        name="search"
        :query="$filters['search']"
        :placeholder="Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER')"
        :label="Lang::txt('COM_TAGS_SEARCH_TAGS')"
        :buttonLabel="Lang::txt('COM_TAGS_SEARCH')"
        :clearUrl="$browseUrl"
    >
        <input type="hidden" name="sort"
            value="{{ e($filters['sort']) }}" />
    </x-search-bar>

    {{-- Sort tabs --}}
    <div role="tablist" class="tabs tabs-border mb-6">
        <a role="tab" href="{{ $popUrl }}"
            @class(['tab', 'tab-active' => $popActive])>
            {{ Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY') }}
            @if ($popActive)
                <span class="ml-1">{{ $sortDir == 'desc' ? '↓' : '↑' }}</span>
            @endif
        </a>
        <a role="tab" href="{{ $alphaUrl }}"
            @class(['tab', 'tab-active' => $alphaActive])>
            {{ Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA') }}
            @if ($alphaActive)
                <span class="ml-1">{{ $sortDir == 'desc' ? '↓' : '↑' }}</span>
            @endif
        </a>
    </div>

    {{-- Tag list --}}
    @if ($rows->count())
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>{{ Lang::txt('COM_TAGS_TAG') }}</th>
                        <th class="hidden md:table-cell">
                            {{ Lang::txt('COM_TAGS_COL_ALIAS') }}
                        </th>
                        @if ($canEdit || $canDelete)
                            <th>{{ Lang::txt('COM_TAGS_COL_ACTION') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                        $tagUrl = Route::url(
                            'index.php?option=' . $option
                            . '&tag=' . $row->get('tag')
                        );
                        $subs = $row->get('substitutes')
                            ? e($row->substitutes) : '';
                        @endphp
                        <tr>
                            <td>
                                <a class="link link-primary link-hover font-medium"
                                    href="{{ $tagUrl }}">
                                    {{ e(stripslashes($row->get('raw_tag'))) }}
                                </a>
                                @if ($row->get('admin'))
                                    <span class="badge badge-warning badge-xs ml-1">admin</span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell text-sm text-base-content/60">
                                @if ($subs)
                                    {{ \Hubzero\Utility\Str::truncate($subs, 75) }}
                                @else
                                    <span class="text-base-content/40">
                                        {{ Lang::txt('COM_TAGS_NONE') }}
                                    </span>
                                @endif
                            </td>
                            @if ($canEdit || $canDelete)
                                <td>
                                    <div class="flex gap-2">
                                        @if ($canEdit)
                                            @php
                                            $editUrl = Route::url(
                                                'index.php?option=' . $option
                                                . '&task=edit&id=' . $row->get('id')
                                                . '&search=' . urlencode($filters['search'])
                                                . '&sort=' . $filters['sort']
                                                . '&sortdir=' . $filters['sort_Dir']
                                                . '&limit=' . $filters['limit']
                                                . '&limitstart=' . $filters['start']
                                            );
                                            @endphp
                                            <a class="btn btn-ghost btn-xs"
                                                href="{{ $editUrl }}"
                                                title="{{ Lang::txt('COM_TAGS_EDIT_TAG', e(stripslashes($row->get('raw_tag')))) }}">
                                                {{ Lang::txt('JACTION_EDIT') }}
                                            </a>
                                        @endif
                                        @if ($canDelete)
                                            <form action="{{ Route::url('index.php?option=' . $option . '&task=delete') }}"
                                                method="post" class="inline"
                                                data-confirm="{{ e(Lang::txt('COM_TAGS_CONFIRM_DELETE')) }}">
                                                <input type="hidden" name="id[]" value="{{ $row->get('id') }}" />
                                                <input type="hidden" name="search" value="{{ e($filters['search']) }}" />
                                                <input type="hidden" name="sort" value="{{ e($filters['sort']) }}" />
                                                <input type="hidden" name="sortdir" value="{{ e($filters['sort_Dir']) }}" />
                                                <input type="hidden" name="limit" value="{{ $filters['limit'] }}" />
                                                <input type="hidden" name="limitstart" value="{{ $filters['start'] }}" />
                                                {!! Html::input('token') !!}
                                                <button type="submit" class="btn btn-ghost btn-xs text-error">
                                                    {{ Lang::txt('JACTION_DELETE') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {!! $pageNav->render() !!}
    @else
        <x-empty-state>
            {{ Lang::txt('COM_TAGS_NO_RESULTS') }}
        </x-empty-state>
    @endif
</x-page-container>
