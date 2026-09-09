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

@if ($config->get('access-manage-section'))
<ul id="page_options">
    <li>
        <a class="btn btn-neutral gap-2" href="{{ Route::url($base . '/settings') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS') }}
        </a>
    </li>
</ul>
@endif

<section class="main section">
@if ($sections->count())
    <div class="subject">
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
        </form>

        @php
        $ct = $sections->count() - 1;
        $i = 0;
        @endphp
        @foreach ($sections as $section)
            @php
            $sectionCategories = $section
                ->categories()
                ->whereEquals('state', $filters['state'])
                ->whereIn('access', $filters['access'])
                ->order('title', 'asc')
                ->rows();
            $sAlias = $section->get('alias');
            @endphp
            <div class="container">
                @if ($config->get('access-edit-section'))
                    <span class="ordering-controls flex gap-1">
                        @if ($i != 0)
                            <a class="btn btn-ghost btn-xs"
                                href="{{ Route::url($base . '&section=' . $sAlias . '&action=orderup') }}"
                                title="{{ Lang::txt('Move up') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                            </a>
                        @else
                            <span class="btn btn-ghost btn-xs btn-disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                            </span>
                        @endif

                        @if ($i < $ct)
                            <a class="btn btn-ghost btn-xs"
                                href="{{ Route::url($base . '&section=' . $sAlias . '&action=orderdown') }}"
                                title="{{ Lang::txt('Move down') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </a>
                        @else
                            <span class="btn btn-ghost btn-xs btn-disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        @endif
                    </span>
                @endif

                <table class="table table-zebra entries categories">
                    <caption>
                        @php $canEditSection = $config->get('access-edit-section'); @endphp
                        @if ($canEditSection && $edit == $section->get('alias'))
                            <form action="{{ Route::url($base) }}"
                                method="post"
                                id="s{{ $section->get('id') }}">
                                <div class="join">
                                    <input type="text"
                                        name="fields[title]"
                                        class="input input-bordered input-sm join-item"
                                        value="{{ e(stripslashes($section->get('title'))) }}" />
                                    <input type="submit" class="btn btn-sm btn-primary join-item" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
                                </div>

                                <input type="hidden" name="fields[id]" value="{{ $section->get('id') }}" />
                                <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
                                <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />
                                <input type="hidden" name="option" value="{{ $option }}" />
                                <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                                <input type="hidden" name="action" value="savesection" />
                                <input type="hidden" name="active" value="forum" />
                                {!! Html::input('token') !!}
                            </form>
                        @else
                            {{ e(stripslashes($section->get('title'))) }}
                        @endif
                        @php $canDeleteSection = $config->get('access-delete-section'); @endphp
                        @if ($canEditSection || $canDeleteSection)
                            @if ($canDeleteSection)
                                <a class="btn btn-ghost btn-xs text-error"
                                    href="{{ Route::url($base . '&scope=' . $sAlias . '/delete') }}"
                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_DELETE') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            @endif
                            @if ($canEditSection && $edit != $section->get('alias'))
                                <a class="btn btn-ghost btn-xs"
                                    href="{{ Route::url($base . '&scope=' . $sAlias . '/edit#s' . $section->get('id')) }}"
                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_EDIT') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                            @endif
                        @endif
                    </caption>
                    @if ($config->get('access-create-category'))
                        <tfoot>
                            <tr>
                                <td @if ($section->categories()->total() > 0) colspan="5" @endif>
                                    <a class="btn btn-primary btn-sm gap-2"
                                        href="{{ Route::url($base . '&scope=' . $sAlias . '/new') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        {{ Lang::txt('PLG_GROUPS_FORUM_NEW_CATEGORY') }}
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                    <tbody>
                        @if ($sectionCategories->count() > 0)
                            @foreach ($sectionCategories as $row)
                                <tr @if ($row->get('closed')) class="closed" @endif>
                                    <th class="priority-5" scope="row">
                                        <span class="entry-id">{{ e($row->get('id')) }}</span>
                                    </th>
                                    <td>
                                        <a class="entry-title link link-hover" href="{{ Route::url($row->link()) }}">
                                            <span>{{ e(stripslashes($row->get('title'))) }}</span>
                                        </a>
                                        <span class="entry-details text-sm text-base-content/70">
                                            <span class="entry-description">
                                                {{ e(stripslashes($row->get('description'))) }}
                                            </span>
                                        </span>
                                    </td>
                                    <td class="priority-4">
                                        @php
                                        $threadCount = $row->threads()
                                            ->whereEquals('state', $filters['state'])
                                            ->whereIn('access', $filters['access'])
                                            ->total();
                                        @endphp
                                        <span class="badge badge-ghost">{{ $threadCount }}</span>
                                        <span class="entry-details text-sm">
                                            {{ Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS') }}
                                        </span>
                                    </td>
                                    <td class="priority-4">
                                        @php
                                        $postCount = $row->posts()
                                            ->whereEquals('state', $filters['state'])
                                            ->whereIn('access', $filters['access'])
                                            ->total();
                                        @endphp
                                        <span class="badge badge-ghost">{{ $postCount }}</span>
                                        <span class="entry-details text-sm">
                                            {{ Lang::txt('PLG_GROUPS_FORUM_POSTS') }}
                                        </span>
                                    </td>
                                    @php
                                    $canEditCat = $config->get('access-edit-category');
                                    $canDeleteCat = $config->get('access-delete-category');
                                    @endphp
                                    @if ($canEditCat || $canDeleteCat)
                                        <td class="entry-options">
                                            @php $isAuthor = $row->get('created_by') == User::get('id'); @endphp
                                            @if ($isAuthor || $canEditCat)
                                                <a class="btn btn-ghost btn-xs"
                                                    href="{{ Route::url($row->link('edit')) }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_EDIT') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                            @endif
                                            @if ($canDeleteCat)
                                                <a class="btn btn-ghost btn-xs text-error"
                                                    href="{{ Route::url($row->link('delete')) }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_FORUM_DELETE_CATEGORY') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>{{ Lang::txt('PLG_GROUPS_FORUM_NO_CATEGORIES') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @php $i++; @endphp
        @endforeach

        @if ($config->get('access-create-section'))
            <div class="container">
                <form method="post" action="{{ Route::url($base) }}">
                    <table class="table entries categories">
                        <caption>
                            <label for="field-title" class="inline-flex items-center gap-2">
                                {{ Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION') }}
                                <div class="join">
                                    <input type="text" name="fields[title]" id="field-title"
                                        class="input input-bordered input-sm join-item" value="" />
                                    <input type="submit" class="btn btn-sm btn-primary join-item"
                                        value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
                                </div>
                            </label>
                        </caption>
                        <tbody>
                            <tr>
                                <td>{{ Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION_EXPLANATION') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <input type="hidden" name="option" value="{{ $option }}" />
                    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                    <input type="hidden" name="fields[id]" value="" />
                    <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
                    <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />
                    <input type="hidden" name="active" value="forum" />
                    <input type="hidden" name="action" value="savesection" />

                    {!! Html::input('token') !!}
                </form>
            </div>
        @endif
    </div><!-- /.subject -->

    <aside class="aside">
        <div class="container">
            <h3>{{ Lang::txt('PLG_GROUPS_FORUM_STATISTICS') }}</h3>
            <table class="table table-compact">
                <tbody>
                    <tr>
                        <th>{{ Lang::txt('PLG_GROUPS_FORUM_CATEGORIES') }}</th>
                        <td><span class="badge badge-ghost">{{ $forum->count('categories', $filters) }}</span></td>
                    </tr>
                    <tr>
                        <th>{{ Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS') }}</th>
                        <td><span class="badge badge-ghost">{{ $forum->count('threads', $filters) }}</span></td>
                    </tr>
                    <tr>
                        <th>{{ Lang::txt('PLG_GROUPS_FORUM_POSTS') }}</th>
                        <td><span class="badge badge-ghost">{{ $forum->count('posts', $filters) }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @php
        $__view->view('_email_settings', '/shared')
            ->set('config', $config)
            ->set('base', $base)
            ->set('recvEmailOptionID', $recvEmailOptionID)
            ->set('recvEmailOptionValue', $recvEmailOptionValue)
            ->set('categories', $categories)
            ->display();
        @endphp

        <div class="container">
            <h3>{{ Lang::txt('PLG_GROUPS_FORUM_LAST_POST') }}</h3>
            <p>
                @php
                $lastPost = $forum->lastActivity();
                @endphp
                @if ($lastPost->get('id'))
                    @php
                    $lname = Lang::txt('JANONYMOUS');
                    if (!$lastPost->get('anonymous')) {
                        $lname = e(stripslashes($lastPost->creator->get('name', $lname)));
                        if (in_array($lastPost->creator->get('access'), User::getAuthorisedViewLevels())) {
                            $lname = '<a href="' . Route::url($lastPost->creator->link()) . '">' . $lname . '</a>';
                        }
                    }
                    foreach ($sections as $section) {
                        if ($section->categories()->total() > 0) {
                            foreach ($section->categories() as $row) {
                                if ($row->get('id') == $lastPost->get('category_id')) {
                                    $lastPost->set('category', $row->get('alias'));
                                    $lastPost->set('section', $section->get('alias'));
                                    break;
                                }
                            }
                        }
                    }
                    @endphp
                    <a class="entry-comment link link-hover" href="{{ Route::url($lastPost->link()) }}">
                        {{ \Hubzero\Utility\Str::truncate(strip_tags($lastPost->get('comment')), 170) }}
                    </a>
                    <span class="entry-author">
                        {!! $lname !!}
                    </span>
                    <span class="entry-date text-sm text-base-content/70">
                        <span class="entry-date-at">{{ Lang::txt('PLG_GROUPS_FORUM_AT') }}</span>
                        <span class="icon-time time">
                            <time datetime="{{ $lastPost->get('created') }}">
                                {{ $lastPost->created('time') }}
                            </time>
                        </span>
                        <span class="entry-date-on">
                            {{ Lang::txt('PLG_GROUPS_FORUM_ON') }}
                        </span>
                        <span class="icon-date date">
                            <time datetime="{{ $lastPost->get('created') }}">
                                {{ $lastPost->created('date') }}
                            </time>
                        </span>
                    </span>
                @else
                    {{ Lang::txt('PLG_GROUPS_FORUM_NONE') }}
                @endif
            </p>
        </div>
    </aside>
@else
    <div class="instructions">
        @if ($config->get('access-create-section'))
            @php
            $populateUrl = Route::url($base . '&action=populate');
            @endphp
            <div class="alert alert-info">
                <p>{!! Lang::txt('PLG_GROUPS_FORUM_EMPTY_MODERATOR', $populateUrl) !!}</p>
            </div>

            <div class="container">
                <form method="post" action="{{ Route::url($base) }}">
                    <fieldset class="entry-section">
                        <legend>{{ Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION') }}</legend>

                        <div class="flex gap-2 items-end">
                            <div class="form-control flex-1">
                                <label for="field-title" class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE') }}</span>
                                </label>
                                <input type="text"
                                    name="fields[title]"
                                    id="field-title"
                                    class="input input-bordered w-full"
                                    value=""
                                    placeholder="{{ Lang::txt('PLG_GROUPS_FORUM_ENTER_TITLE') }}" />
                            </div>
                            <input type="submit"
                                class="btn btn-primary"
                                value="{{ Lang::txt('PLG_GROUPS_FORUM_CREATE') }}" />
                        </div>

                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                        <input type="hidden" name="fields[id]" value="" />
                        <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
                        <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />
                        <input type="hidden" name="active" value="forum" />
                        <input type="hidden" name="action" value="savesection" />
                        <input type="hidden" name="fields[id]" value="" />
                        <input type="hidden" name="fields[access]" value="0" />

                        {!! Html::input('token') !!}
                    </fieldset>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <p>{{ Lang::txt('PLG_GROUPS_FORUM_EMPTY_NOT_MODERATOR') }}</p>
            </div>
        @endif
    </div>
@endif
</section>
