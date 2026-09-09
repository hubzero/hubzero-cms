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

$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=forum';
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-neutral gap-2"
            href="{{ Route::url($base) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES') }}
        </a>
    </li>
</ul>

<section class="main section">
    <form action="{{ Route::url($base . '&scope=search') }}" method="get">
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

        <div class="container">
            <table class="table table-zebra">
                <caption>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR', e($filters['search'])) }}
                </caption>
                <tbody>
                    @php
                    $rows = $forum->posts($filters)
                        ->paginated()
                        ->rows();
                    @endphp
                    @if ($filters['search'] && $rows->count() > 0)
                        @foreach ($rows as $row)
                            @php
                            $title = e(stripslashes($row->get('title')));
                            $title = preg_replace(
                                '#' . $filters['search'] . '#i',
                                '<span class="highlight bg-warning/30">\\0</span>',
                                $title
                            );

                            $name = Lang::txt('JANONYMOUS');
                            if (!$row->get('anonymous')) {
                                $name = e(stripslashes($row->creator->get('name')));
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                                }
                            }
                            $cls = [];
                            if ($row->get('closed')) {
                                $cls[] = 'closed';
                            }
                            if ($row->get('sticky')) {
                                $cls[] = 'sticky';
                            }
                            @endphp
                            <tr @if (count($cls) > 0) class="{{ implode(' ', $cls) }}" @endif>
                                <th class="priority-5">
                                    <span class="entry-id">{{ e($row->get('id')) }}</span>
                                </th>
                                <td>
                                    @php
                                    $catId = $row->get('category_id');
                                    $secId = $categories[$catId]->get('section_id');
                                    $entryUrl = Route::url(
                                        $base . '&scope='
                                        . $sections[$secId]->get('alias')
                                        . '/' . $categories[$catId]->get('alias')
                                        . '/' . $row->get('thread')
                                    );
                                    @endphp
                                    <a class="entry-title link link-hover"
                                        href="{{ $entryUrl }}">
                                        <span>{!! $title !!}</span>
                                    </a>
                                    <span class="entry-details text-sm text-base-content/70">
                                        <span class="entry-date">
                                            {{ $row->created('date') }}
                                        </span>
                                        {{ Lang::txt('by') }}
                                        <span class="entry-author">
                                            {!! $name !!}
                                        </span>
                                    </span>
                                </td>
                                <td class="priority-4">
                                    <span>{{ Lang::txt('PLG_GROUPS_FORUM_SECTION') }}</span>
                                    @php
                                    $sectionTitle = $sections[
                                        $categories[$row->get('category_id')]->get('section_id')
                                    ]->get('title');
                                    $sectionName = e(
                                        \Hubzero\Utility\Str::truncate($sectionTitle, 100, ['exact' => true])
                                    );
                                    @endphp
                                    <span class="entry-details section-name text-sm">
                                        {{ $sectionName }}
                                    </span>
                                </td>
                                <td class="priority-3">
                                    <span>{{ Lang::txt('PLG_GROUPS_FORUM_CATEGORY') }}</span>
                                    @php
                                    $categoryTitle = $categories[$row->get('category_id')]->get('title');
                                    $categoryName = e(
                                        \Hubzero\Utility\Str::truncate($categoryTitle, 100, ['exact' => true])
                                    );
                                    @endphp
                                    <span class="entry-details category-name text-sm">
                                        {{ $categoryName }}
                                    </span>
                                </td>
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
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'forum');
            $pageNav->setAdditionalUrlParam('search', $filters['search']);
            echo $pageNav;
            @endphp
        </div>
    </form>
</section>
