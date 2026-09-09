{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;

if (!$collection->get('layout')) {
    $collection->set('layout', 'grid');
}
$viewas = Request::getWord('viewas', $collection->get('layout'));
if (!in_array($viewas, ['grid', 'list'])) {
    $viewas = 'grid';
}
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-ghost btn-sm gap-2"
            href="{{ Route::url('index.php?option=com_help&component=collections&page=index') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_GETTING_STARTED') }}
        </a>
    </li>
</ul>

<form method="get"
    action="{{ Route::url($base . '&scope=' . $collection->get('alias', 'posts')) }}"
    id="collections">

    @php
    $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('group', $group)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', ($collection->exists() ? '' : 'posts'))
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->display();
    @endphp

    @if (!User::isGuest() && $params->get('access-manage-collection'))
        <div class="mb-4">
            <a class="btn btn-ghost btn-sm gap-2"
                href="{{ Route::url($base . '&scope=settings') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS') }}
            </a>
        </div>
    @endif

    @if ($collection->exists())
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="text-lg font-semibold">
                "{{ e(stripslashes($collection->get('title'))) }}"
            </span>
            <span class="badge badge-ghost">
                {!! Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_POSTS', '<strong>' . $count . '</strong>') !!}
            </span>

            @if (!User::isGuest())
                @if ($collection->isFollowing())
                    <a class="btn btn-outline btn-xs tooltip"
                        data-text-follow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW') }}"
                        data-text-unfollow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW') }}"
                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_TITLE') }}"
                        href="{{ Route::url($base . '&scope=' . $collection->get('alias') . '/unfollow') }}">
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW') }}
                    </a>
                @else
                    <a class="btn btn-primary btn-xs tooltip"
                        data-text-follow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW') }}"
                        data-text-unfollow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW') }}"
                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_TITLE') }}"
                        href="{{ Route::url($base . '&scope=' . $collection->get('alias') . '/follow') }}">
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW') }}
                    </a>
                @endif
            @endif

            <span class="flex gap-1 ml-auto">
                @php
                $createdClass = 'btn btn-ghost btn-xs' . ($filters['sort'] == 'created' ? ' btn-active' : '');
                $orderingClass = 'btn btn-ghost btn-xs' . ($filters['sort'] == 'ordering' ? ' btn-active' : '');
                @endphp
                <a href="{{ Route::url($collection->link() . '&sort=created&viewas=' . $viewas) }}"
                    class="{{ $createdClass }}"
                    title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_CREATED_SORT') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_CREATED_SORT') }}
                </a>
                <a href="{{ Route::url($collection->link() . '&sort=ordering&viewas=' . $viewas) }}"
                    class="{{ $orderingClass }}"
                    title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_ORDERING_SORT') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_ORDERING_SORT') }}
                </a>
            </span>
            <span class="flex gap-1">
                @php
                $gridClass = 'btn btn-ghost btn-xs' . ($viewas == 'grid' ? ' btn-active' : '');
                $listClass = 'btn btn-ghost btn-xs' . ($viewas == 'list' ? ' btn-active' : '');
                @endphp
                <a href="{{ Route::url($collection->link() . '&sort=' . $filters['sort'] . '&viewas=grid') }}"
                    class="{{ $gridClass }}"
                    title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_GRID_VIEW') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_GRID_VIEW') }}
                </a>
                <a href="{{ Route::url($collection->link() . '&sort=' . $filters['sort'] . '&viewas=list') }}"
                    class="{{ $listClass }}"
                    title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_LIST_VIEW') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_LIST_VIEW') }}
                </a>
            </span>
        </div>
    @endif

    @if ($rows->total() > 0)
        @php
        $baseTrimmed = rtrim(Request::base(true), '/');
        $reorderUrl = Route::url(
            'index.php?option=com_collections&controller=posts&task=reorder&' . Session::getFormToken() . '=1'
        );
        $viewClass = 'view-' . $viewas . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin');
        @endphp
        <div id="posts"
            data-base="{{ $baseTrimmed }}"
            data-update="{{ $reorderUrl }}"
            class="{{ $viewClass }} {{ $viewas == 'grid' ? 'columns-1 sm:columns-2 lg:columns-3 gap-4' : '' }}">

            @if ($params->get('access-create-item') && !Request::getInt('no_html', 0))
                <div class="post new-post {{ $viewas == 'grid' ? 'break-inside-avoid' : '' }} mb-4" id="post_0">
                    <a class="btn btn-primary gap-2"
                        href="{{ Route::url($base . '&scope=post/new&board=' . $collection->get('alias')) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_POST') }}
                    </a>
                </div>
            @endif

            @foreach ($rows as $row)
                @php
                $item = $row->item();
                @endphp
                <div class="card bg-base-100 shadow-sm mb-4 {{ $viewas == 'grid' ? 'break-inside-avoid' : '' }} post {{ $item->type() }}"
                    id="post_{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}"
                    data-closeup-url="{{ Route::url($base . '&scope=post/' . $row->get('id')) }}">
                    <div class="card-body">
                        @php
                        $__view->view('default_' . $item->type(), 'post')
                            ->set('name', $name)
                            ->set('option', $option)
                            ->set('group', $group)
                            ->set('params', $params)
                            ->set('row', $row)
                            ->display();
                        @endphp

                        @if ($tags = $item->tags('cloud'))
                            <div class="tags-wrap mt-2">
                                {!! $tags !!}
                            </div>
                        @endif

                        @php
                        $metadataUrl = Route::url(
                            'index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id')
                        );
                        @endphp
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-base-300"
                            data-metadata-url="{{ $metadataUrl }}">
                            <div class="flex gap-3 text-sm opacity-70">
                                <span>{!! Lang::txt('PLG_GROUPS_COLLECTIONS_POST_LIKES', $item->get('positive', 0)) !!}</span>
                                <span>{!! Lang::txt('PLG_GROUPS_COLLECTIONS_POST_COMMENTS', $item->get('comments', 0)) !!}</span>
                                <span>{!! Lang::txt('PLG_GROUPS_COLLECTIONS_POST_REPOSTS', $item->get('reposts', 0)) !!}</span>
                            </div>
                            <div class="flex gap-1">
                                @if (!User::isGuest())
                                    @if ($group->published == 1)
                                        @if ($item->get('created_by') != User::get('id'))
                                            @php
                                            $voteLabel = $item->get('voted')
                                                ? Lang::txt('PLG_GROUPS_COLLECTIONS_UNLIKE')
                                                : Lang::txt('PLG_GROUPS_COLLECTIONS_LIKE');
                                            @endphp
                                            <a class="btn btn-ghost btn-xs {{ $item->get('voted') ? 'btn-active' : '' }}"
                                                data-id="{{ $row->get('id') }}"
                                                data-text-like="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_LIKE') }}"
                                                data-text-unlike="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNLIKE') }}"
                                                href="{{ Route::url($base . '&scope=post/' . $row->get('id') . '/vote') }}">
                                                {{ $voteLabel }}
                                            </a>
                                        @endif
                                        <a class="btn btn-ghost btn-xs"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COMMENT') }}
                                        </a>
                                    @endif

                                    <a class="btn btn-ghost btn-xs"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url($base . '&scope=post/' . $row->get('id') . '/collect') }}">
                                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT') }}
                                    </a>

                                    @if ($group->published == 1)
                                        @php
                                        $isCreator = $item->get('created_by') == User::get('id');
                                        $canManage = $params->get('access-manage-collection');
                                        @endphp
                                        @if ($isCreator || $canManage)
                                            <a class="btn btn-ghost btn-xs"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($base . '&scope=post/' . $row->get('id') . '/edit') }}"
                                                title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT') }}">
                                                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT') }}
                                            </a>
                                        @endif
                                        @if ($row->get('original') && ($isCreator || $canManage))
                                            <a class="btn btn-error btn-xs btn-outline"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($base . '&scope=post/' . $row->get('id') . '/delete') }}"
                                                title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE') }}">
                                                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE') }}
                                            </a>
                                        @elseif ($row->get('created_by') == User::get('id') || $canManage)
                                            <a class="btn btn-warning btn-xs btn-outline"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($base . '&scope=post/' . $row->get('id') . '/remove') }}">
                                                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_REMOVE') }}
                                            </a>
                                        @endif
                                    @endif
                                @else
                                    @php
                                    $loginReturn = base64_encode(
                                        Route::url($base . '&scope=' . $collection->get('alias'), false, true)
                                    );
                                    $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn, false);
                                    @endphp
                                    <a class="btn btn-ghost btn-xs tooltip"
                                        href="{{ $loginUrl }}"
                                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_LIKE') }}">
                                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_LIKE') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COMMENT') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs tooltip"
                                        href="{{ $loginUrl }}"
                                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-base-200">
                            @php
                            $creatorName = e(stripslashes($row->creator()->get('name')));
                            @endphp
                            @if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels()))
                                <a href="{{ Route::url($row->creator()->link()) }}" class="avatar">
                                    <div class="w-8 rounded-full">
                                        <img src="{{ $row->creator()->picture() }}"
                                            alt="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $creatorName) }}" />
                                    </div>
                                </a>
                            @else
                                <div class="avatar">
                                    <div class="w-8 rounded-full">
                                        <img src="{{ $row->creator()->picture() }}"
                                            alt="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $creatorName) }}" />
                                    </div>
                                </div>
                            @endif
                            <p class="text-sm">
                                @php
                                $who = $creatorName;
                                if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) {
                                    $who = '<a href="' . Route::url($row->creator()->link()) . '">' . $creatorName . '</a>';
                                }
                                $where = '<a href="' . Route::url($row->link()) . '">'
                                    . e(stripslashes($row->get('title'))) . '</a>';
                                @endphp
                                {!! Lang::txt('PLG_GROUPS_COLLECTIONS_ONTO', $who, $where) !!}
                                <br />
                                <span class="text-xs opacity-60">
                                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DATE_AT') }}
                                    <time datetime="{{ $row->created() }}">{{ $row->created('time') }}</time>
                                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DATE_ON') }}
                                    <time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time>
                                </span>
                            </p>
                        </div>

                        @if (!User::isGuest() && $params->get('access-create-item') && $filters['sort'] == 'ordering')
                            <div class="sort-handle tooltip cursor-move mt-2"
                                data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_GRAB_TO_REORDER') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($posts > $filters['limit'])
            @php
            $pageNav = $__view->pagination($count, $filters['start'], $filters['limit']);
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('scope', $scope);
            $pageNav->setAdditionalUrlParam('viewas', $viewas);
            $pageNav->setAdditionalUrlParam('sort', $filters['sort']);
            @endphp
            {!! $pageNav->render() !!}
        @endif
    @else
        <div id="collection-introduction" class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @if ($params->get('access-create-item'))
                    <div class="prose mb-4">
                        <ol>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP1') }}</li>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP2') }}</li>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP3') }}</li>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP4') }}</li>
                        </ol>
                    </div>
                    <a class="btn btn-primary gap-2"
                        href="{{ Route::url($base . '&scope=post/new&board=' . $collection->get('alias')) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_POST') }}
                    </a>
                    <div class="mt-4">
                        <p class="font-semibold">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_TITLE') }}</p>
                        <p>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_DESC') }}</p>
                    </div>
                @else
                    <p>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_NO_POSTS_FOUND') }}</p>
                @endif
            </div>
        </div>
    @endif
</form>
