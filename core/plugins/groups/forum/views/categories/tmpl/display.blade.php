{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();

$base = 'index.php?option='
    . $option
    . '&cn='
    . $group->get('cn')
    . '&active=forum&scope='
    . $filters['section']
    . '/'
    . $filters['category'];

if (!function_exists('sortDir')) {
    function sortDir($filters, $current, $dir = 'DESC')
    {
        if ($filters['sortby'] == $current && $filters['sort_Dir'] == $dir) {
            $dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
        }
        return strtolower($dir);
    }
}

$sortingValues = (object) [
    'sortdir' => $filters['sort_Dir'],
    'sortby' => $filters['sortby'],
    'start' => $filters['start'],
    'limit' => $filters['limit'],
];
$sortingQueryString = '';
if (!empty($sortingValues->sortby) || !empty($sortingValues->start || !empty($sortingValues->limit))) {
    $sortingQueryString = '?' . http_build_query($sortingValues);
}

$category->set('section_alias', $filters['section']);
@endphp

<ul id="page_options">
    <li>
        @php
        $allCategoriesUrl = Route::url(
            'index.php?option=' . $option
            . '&cn=' . $group->get('cn')
            . '&active=forum'
        );
        @endphp
        <a class="btn btn-neutral gap-2"
            href="{{ $allCategoriesUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES') }}
        </a>
    </li>
</ul>

<section class="main section">
    @php
    $searchFormUrl = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->get('cn')
        . '&active=forum&scope=search'
    );
    @endphp
    <form action="{{ $searchFormUrl }}" method="get">
        <div class="container data-entry">
            <fieldset class="entry-search">
                <legend>{{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND') }}</legend>
                <label for="entry-search-field" class="sr-only">
                    {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL') }}
                </label>
                <div class="join w-full">
                    <input type="text"
                        name="q"
                        id="entry-search-field"
                        class="input input-bordered join-item flex-1"
                        value="{{ e($filters['search']) }}"
                        placeholder="{{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER') }}" />
                    <button type="submit" class="btn btn-neutral join-item">
                        {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH') }}
                    </button>
                </div>
            </fieldset>
        </div>
    </form>

    <form action="{{ Route::url($category->link()) }}" method="get">
        @if ($category->get('closed'))
            <div class="alert alert-warning">
                <p>{{ Lang::txt('PLG_GROUPS_FORUM_CATEGORY_CLOSED') }}</p>
            </div>
        @endif

        <div class="container">
            <nav class="entries-filters" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
                <ul class="menu menu-horizontal bg-base-200 rounded-box mb-4">
                    <li>
                        @php
                        $createdClass = $filters['sortby'] == 'created'
                            ? 'active ' . strtolower($filters['sort_Dir'])
                            : sortDir($filters, 'created');
                        $createdUrl = Route::url(
                            $base . '&sortby=created&sortdir='
                            . sortDir($filters, 'created')
                        );
                        @endphp
                        <a class="{{ $createdClass }}"
                            href="{{ $createdUrl }}"
                            title="{{ Lang::txt('PLG_GROUPS_FORUM_SORT_BY_CREATED') }}">
                            {{ Lang::txt('PLG_GROUPS_FORUM_SORT_CREATED') }}
                        </a>
                    </li>
                    <li>
                        @php
                        $activityClass = $filters['sortby'] == 'activity'
                            ? 'active ' . strtolower($filters['sort_Dir'])
                            : sortDir($filters, 'activity');
                        $activityUrl = Route::url(
                            $base . '&sortby=activity&sortdir='
                            . sortDir($filters, 'activity')
                        );
                        @endphp
                        <a class="{{ $activityClass }}"
                            href="{{ $activityUrl }}"
                            title="{{ Lang::txt('PLG_GROUPS_FORUM_SORT_BY_ACTIVITY') }}">
                            {{ Lang::txt('PLG_GROUPS_FORUM_SORT_ACTIVITY') }}
                        </a>
                    </li>
                    <li>
                        @php
                        $repliesClass = $filters['sortby'] == 'replies'
                            ? 'active ' . strtolower($filters['sort_Dir'])
                            : sortDir($filters, 'replies');
                        $repliesUrl = Route::url(
                            $base . '&sortby=replies&sortdir='
                            . sortDir($filters, 'replies')
                        );
                        @endphp
                        <a class="{{ $repliesClass }}"
                            href="{{ $repliesUrl }}"
                            title="{{ Lang::txt('PLG_GROUPS_FORUM_SORT_BY_NUM_POSTS') }}">
                            {{ Lang::txt('PLG_GROUPS_FORUM_SORT_NUM_POSTS') }}
                        </a>
                    </li>
                    <li>
                        @php
                        $titleClass = $filters['sortby'] == 'title'
                            ? 'active ' . strtolower($filters['sort_Dir'])
                            : sortDir($filters, 'title', 'ASC');
                        $titleUrl = Route::url(
                            $base . '&sortby=title&sortdir='
                            . sortDir($filters, 'title', 'ASC')
                        );
                        @endphp
                        <a class="{{ $titleClass }}"
                            href="{{ $titleUrl }}"
                            title="{{ Lang::txt('PLG_GROUPS_FORUM_SORT_BY_TITLE') }}">
                            {{ Lang::txt('PLG_GROUPS_FORUM_SORT_TITLE') }}
                        </a>
                    </li>
                </ul>
            </nav>

            <table class="table table-zebra">
                <caption>
                    @if ($filters['search'])
                        @if ($category->get('title'))
                            {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR_IN', e($filters['search']), e(stripslashes($category->get('title')))) }}
                        @else
                            {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR', e($filters['search'])) }}
                        @endif
                    @else
                        {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_IN', e(stripslashes($category->get('title')))) }}
                    @endif
                </caption>
                @php
                $canCreate = !$category->get('closed') && $config->get('access-create-thread');
                $canDelete = $config->get('access-delete-thread');
                $canEdit = $config->get('access-edit-thread');
                $colspan = ($canDelete || $canEdit) ? '5' : '4';
                $newUrl = Route::url($base . '/new');
                @endphp
                @if ($canCreate)
                    <thead>
                        <tr>
                            <td colspan="{{ $colspan }}">
                                <a class="btn btn-primary btn-sm gap-2"
                                    href="{{ $newUrl }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    {{ Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION') }}
                                </a>
                            </td>
                        </tr>
                    </thead>
                    @if (count($threads) > 10)
                    <tfoot>
                        <tr>
                            <td colspan="{{ $colspan }}">
                                <a class="btn btn-primary btn-sm gap-2"
                                    href="{{ $newUrl }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    {{ Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION') }}
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                @endif
                <tbody>
                    @if ($threads->count() > 0)
                        @foreach ($threads as $row)
                            @php
                            $name = Lang::txt('JANONYMOUS');
                            if (!$row->get('anonymous')) {
                                $name = e(stripslashes($row->creator->get('name', $name)));
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                                }
                            }
                            $cls = [];
                            if ($row->isClosed()) {
                                $cls[] = 'closed';
                            }
                            if ($row->isSticky()) {
                                $cls[] = 'sticky';
                            }

                            $row->set('category', $filters['category']);
                            $row->set('section', $filters['section']);
                            @endphp
                            <tr @if (count($cls) > 0) class="{{ implode(' ', $cls) }}" @endif>
                                <th class="priority-5">
                                    <span class="entry-id">{{ e($row->get('id')) }}</span>
                                </th>
                                <td>
                                    <a class="entry-title link link-hover"
                                        href="{{ Route::url($base . '/' . $row->get('id')) }}">
                                        <span>{{ e(stripslashes($row->get('title'))) }}</span>
                                    </a>
                                    <span class="entry-details text-sm text-base-content/70">
                                        <span class="entry-date">
                                            <time datetime="{{ $row->created() }}">
                                                {{ $row->created('date') }}
                                            </time>
                                        </span>
                                        {!! Lang::txt('PLG_GROUPS_FORUM_BY_USER', '<span class="entry-author">' . $name . '</span>') !!}
                                    </span>
                                </td>
                                <td class="priority-4">
                                    @php
                                    $commentCount = $row->thread()
                                        ->whereEquals('state', $row->get('state'))
                                        ->whereIn('access', $filters['access'])
                                        ->total();
                                    @endphp
                                    <span class="badge badge-ghost">{{ $commentCount }}</span>
                                    <span class="entry-details text-sm">
                                        {{ Lang::txt('PLG_GROUPS_FORUM_COMMENTS') }}
                                    </span>
                                </td>
                                <td class="priority-3">
                                    <span>{{ Lang::txt('PLG_GROUPS_FORUM_LAST_POST') }}</span>
                                    <span class="entry-details text-sm text-base-content/70">
                                        @php
                                        $lastpost = $row->lastActivity();
                                        @endphp
                                        @if ($lastpost->get('id'))
                                            @php
                                            $lname = Lang::txt('JANONYMOUS');
                                            if (!$lastpost->get('anonymous')) {
                                                $lname = e(stripslashes($lastpost->creator->get('name')));
                                                if (in_array($lastpost->creator->get('access'), User::getAuthorisedViewLevels())) {
                                                    $lname = '<a href="' . Route::url($lastpost->creator->link()) . '">' . $lname . '</a>';
                                                }
                                            }
                                            @endphp
                                            <span class="entry-date">
                                                <time datetime="{{ $lastpost->created() }}">
                                                    {{ $lastpost->created('date') }}
                                                </time>
                                            </span>
                                            {!! Lang::txt('PLG_GROUPS_FORUM_BY_USER', '<span class="entry-author">' . $lname . '</span>') !!}
                                        @else
                                            {{ Lang::txt('PLG_GROUPS_FORUM_NONE') }}
                                        @endif
                                    </span>
                                </td>
                                @if ($group->published == 1)
                                    @php
                                    $canDeleteThread = $config->get('access-delete-thread');
                                    $canEditThread = $config->get('access-edit-thread');
                                    $isCreator = User::get('id') == $row->get('created_by');
                                    @endphp
                                    @if ($canDeleteThread || $canEditThread || $isCreator)
                                        <td class="entry-options">
                                            @if ($isCreator || $canEditThread)
                                                <a class="btn btn-ghost btn-xs"
                                                    href="{{ Route::url($base . '/' . $row->get('id') . '/edit') }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_EDIT') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                            @endif
                                            @if ($canDeleteThread)
                                                <a class="btn btn-ghost btn-xs text-error"
                                                    href="{{ Route::url($base . '/' . $row->get('id') . '/delete') . $sortingQueryString }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_DELETE') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ Lang::txt('PLG_GROUPS_FORUM_CATEGORY_EMPTY') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @php
            $pageNav = $threads->pagination;
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'forum');
            $pageNav->setAdditionalUrlParam('scope', $filters['section'] . '/' . $filters['category']);
            echo $pageNav;
            @endphp
        </div>
        <input type="hidden" name="sortdir" value="{{ e($filters['sort_Dir']) }}" />
        <input type="hidden" name="sortby" value="{{ e($filters['sortby']) }}" />
    </form>
</section>
