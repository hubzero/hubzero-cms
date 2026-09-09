{{--
 * Tool pipeline browse — list of tools in the development pipeline.
 *
 * Variables from controller:
 *   $title    — Page title
 *   $option   — Component option (com_tools)
 *   $controller — Controller name
 *   $filters  — array: search, sortby, filterby, start, limit
 *   $rows     — Array of tool row objects
 *   $total    — Total number of matching tools
 *   $admin    — Whether the current user is an admin
 *   $config   — Component params (Registry)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// Config / defaults
$developer_site = $config->get('developer_site', 'hubFORGE');
$live_site = rtrim(Request::base(), '/');
$developer_url = "https://" . preg_replace('#^(https://|http://)#', '', $live_site);
$project_path = $config->get('project_path', '/tools/');
$dev_suffix = $config->get('dev_suffix', '_dev');

// Pagination
$pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
$pageNav->setAdditionalUrlParam('search', $filters['search']);
$pageNav->setAdditionalUrlParam('filterby', $filters['filterby']);
$pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);

// Escaped filter values for URL building
$srch = $__view->escape(urlencode($filters['search']));
$lim = $filters['limit'];
$filt = $filters['filterby'];
$sflt = urlencode($filters['sortby']);

$pipelineUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=pipeline'
);
$newUrl = Route::url('index.php?option=' . $option . '&task=create');

// Sort options
$sortOptions = [];

if ($admin) {
    $sortOptions[] = [
        'value' => 'f.state, f.priority, f.toolname',
        'label' => Lang::txt('COM_TOOLS_STATUS'),
        'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'),
    ];
} else {
    $sortOptions[] = [
        'value' => 'f.state, f.registered',
        'label' => Lang::txt('COM_TOOLS_STATUS'),
        'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'),
    ];
}

$sortOptions[] = [
    'value' => 'f.registered',
    'label' => Lang::txt('COM_TOOLS_DATE'),
    'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_REG'),
];

$sortOptions[] = [
    'value' => 'f.toolname',
    'label' => Lang::txt('COM_TOOLS_ALIAS'),
    'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_NAME'),
];

if ($admin) {
    $sortOptions[] = [
        'value' => 'f.priority',
        'label' => Lang::txt('COM_TOOLS_PRIORITY'),
        'title' => Lang::txt('COM_TOOLS_PRIORITY'),
    ];
    $sortOptions[] = [
        'value' => 'f.state_changed DESC',
        'label' => Lang::txt('COM_TOOLS_STATUS_CHANGE'),
        'title' => Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'),
    ];
}

// Filter options
$filterOptions = [
    ['value' => 'all',       'label' => Lang::txt('COM_TOOLS_ALL'),
     'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_ALL')],
    ['value' => 'mine',      'label' => Lang::txt('COM_TOOLS_MINE'),
     'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_MINE')],
    ['value' => 'published', 'label' => Lang::txt('COM_TOOLS_PUBLISHED'),
     'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_PUBLISHED')],
];

if ($admin) {
    $filterOptions[] = [
        'value' => 'dev',
        'label' => Lang::txt('COM_TOOLS_DEVELOPMENT'),
        'title' => Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_DEV'),
    ];
}
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-primary btn-sm" href="{{ $newUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
        </a>
    @endslot

    {{-- Search --}}
    <x-search-bar
        :action="$pipelineUrl"
        :query="$__view->escape($filters['search'])"
        :placeholder="Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER')"
        :label="Lang::txt('COM_TOOLS_FIND_TOOL')"
        :buttonLabel="Lang::txt('COM_TOOLS_SEARCH')"
        :clearUrl="$pipelineUrl"
        name="search">
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="pipeline" />
        <input type="hidden" name="sortby"
            value="{{ $__view->escape($filters['sortby']) }}" />
        <input type="hidden" name="filterby"
            value="{{ $__view->escape($filters['filterby']) }}" />
    </x-search-bar>

    {{-- Sort / Filter tabs --}}
    <div class="flex flex-wrap gap-4 mb-6 items-center">
        {{-- Sort options --}}
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-base-content/70">
                {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY') }}:
            </span>
            <div role="tablist" class="tabs tabs-border tabs-sm">
                @foreach ($sortOptions as $sortOpt)
                    @php
                    $sortUrl = Route::url(
                        'index.php?option=' . $option
                        . '&task=pipeline&limit=' . $lim
                        . '&filterby=' . $filt
                        . '&sortby=' . urlencode($sortOpt['value'])
                        . '&search=' . $srch
                    );
                    $isActive = ($filters['sortby'] == $sortOpt['value']);
                    @endphp
                    <a role="tab"
                        href="{{ $sortUrl }}"
                        title="{{ $sortOpt['title'] }}"
                        @class(['tab', 'tab-active' => $isActive])>
                        {{ $sortOpt['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Filter options --}}
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-base-content/70">
                {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER') }}:
            </span>
            <div role="tablist" class="tabs tabs-border tabs-sm">
                @foreach ($filterOptions as $filterOpt)
                    @php
                    $filterUrl = Route::url(
                        'index.php?option=' . $option
                        . '&task=pipeline&limit=' . $lim
                        . '&filterby=' . $filterOpt['value']
                        . '&sortby=' . $sflt
                        . '&search=' . $srch
                    );
                    $isFilterActive = ($filters['filterby'] == $filterOpt['value']);
                    @endphp
                    <a role="tab"
                        href="{{ $filterUrl }}"
                        title="{{ $filterOpt['title'] }}"
                        @class(['tab', 'tab-active' => $isFilterActive])>
                        {{ $filterOpt['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Results caption --}}
    @php
    $captionKey = 'COM_TOOLS_CONTRIBTOOL_FILTER_' . strtoupper($filters['filterby']);
    $startNum = (count($rows) > 0) ? $filters['start'] + 1 : 0;
    $endNum = $filters['start'] + count($rows);
    @endphp
    <p class="text-sm text-base-content/60 mb-4">
        {{ Lang::txt($captionKey) }}
        <span>({{ $startNum }} - {{ $endNum }} of {{ $pageNav->total }})</span>
    </p>

    @if (count($rows) > 0)
        {{-- Tool table --}}
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="hidden lg:table-cell w-16">
                            {{ Lang::txt('COM_TOOLS_ID') }}
                        </th>
                        <th>{{ Lang::txt('COM_TOOLS_TITLE') }}</th>
                        <th class="hidden md:table-cell">
                            {{ Lang::txt('COM_TOOLS_ALIAS') }}
                        </th>
                        <th class="hidden sm:table-cell">
                            {{ Lang::txt('COM_TOOLS_STATUS') }}
                        </th>
                        <th>{{ Lang::txt('COM_TOOLS_LINKS') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                        $row->state_changed = ($row->state_changed
                            && $row->state_changed != '0000-00-00 00:00:00')
                            ? $row->state_changed
                            : $row->registered;
                        $rowTitle = $row->title
                            . ($row->version ? ' v' . $row->version : '');

                        \Components\Tools\Helpers\Html::getStatusName(
                            $row->state, $status
                        );

                        $rowStatusUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=status&app=' . $row->toolname
                        );

                        $timeAgo = \Components\Tools\Helpers\Html::timeAgo(
                            $row->state_changed
                        ) . ' ' . Lang::txt('COM_TOOLS_AGO');

                        $rowEstablished = \Components\Tools\Helpers\Html::toolEstablished(
                            $row->state
                        );
                        $rowActive = \Components\Tools\Helpers\Html::toolActive(
                            $row->state
                        );

                        $resourceUrl = Route::url(
                            'index.php?option=' . $option
                            . '&app=' . $row->toolname
                        );
                        $ticketUrl = Route::url(
                            'index.php?option=com_support&task=ticket&id='
                            . $row->ticketid
                        );
                        $wikiUrl = $developer_url . $project_path
                            . $row->toolname . '/wiki';
                        $dateStr = Date::of($row->registered)->toLocal(
                            Lang::txt('DATE_FORMAT_HZ1')
                        );

                        // Badge color by status
                        $badgeClass = match (strtolower($status)) {
                            'published' => 'badge-success',
                            'approved'  => 'badge-info',
                            'retired'   => 'badge-warning',
                            'abandoned' => 'badge-error',
                            default     => 'badge-neutral',
                        };
                        @endphp
                        <tr>
                            <td class="hidden lg:table-cell">
                                <span class="text-base-content/50">
                                    {{ $__view->escape($row->id) }}
                                </span>
                            </td>
                            <td>
                                <a class="link link-primary link-hover font-medium"
                                    href="{{ $rowStatusUrl }}">
                                    {{ $__view->escape(stripslashes($rowTitle)) }}
                                </a>
                                <div class="text-xs text-base-content/60 mt-0.5">
                                    {{ Lang::txt('COM_TOOLS_REGISTERED') }}
                                    <time datetime="{{ $row->registered }}">
                                        {{ $dateStr }}
                                    </time>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                <a class="link link-hover text-sm"
                                    href="{{ $rowStatusUrl }}">
                                    {{ $__view->escape($row->toolname) }}
                                </a>
                            </td>
                            <td class="hidden sm:table-cell">
                                <a href="{{ $rowStatusUrl }}">
                                    <span class="badge {{ $badgeClass }} badge-sm">
                                        {{ $status }}
                                    </span>
                                </a>
                                <div class="text-xs text-base-content/60 mt-0.5">
                                    {{ $timeAgo }}
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-x-2 gap-y-1 text-sm">
                                    @if (!$rowActive)
                                        <span class="text-base-content/40">
                                            {{ Lang::txt('COM_TOOLS_RESOURCE') }}
                                        </span>
                                    @else
                                        <a class="link link-hover"
                                            href="{{ $resourceUrl }}">
                                            {{ Lang::txt('COM_TOOLS_RESOURCE') }}
                                        </a>
                                    @endif
                                    <span class="text-base-content/30">|</span>
                                    <a class="link link-hover"
                                        href="{{ $ticketUrl }}">
                                        {{ strtolower(Lang::txt('COM_TOOLS_HISTORY')) }}
                                    </a>
                                    <span class="text-base-content/30">|</span>
                                    @if (strtolower($status) == 'abandoned')
                                        <span class="text-base-content/40">
                                            {{ strtolower(Lang::txt('COM_TOOLS_PROJECT')) }}
                                        </span>
                                    @else
                                        <a class="link link-hover"
                                            href="{{ $wikiUrl }}"
                                            rel="external">
                                            {{ strtolower(Lang::txt('COM_TOOLS_PROJECT')) }}
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        {!! $pageNav->render() !!}
    @else
        <x-empty-state>
            {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
        </x-empty-state>
    @endif
</x-page-container>
