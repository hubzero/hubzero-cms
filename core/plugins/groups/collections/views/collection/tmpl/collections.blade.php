{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;
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

<form method="get" action="{{ Route::url($base) }}" id="collections">
    @php
    $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('group', $group)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'collections')
        ->set('collections', $rows->total())
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', ($params->get('access-can-follow') ? $following : 0))
        ->display();
    @endphp

    @if (!User::isGuest())
        <div class="flex gap-2 mb-4">
            @if ($params->get('access-manage-collection'))
                <a class="btn btn-ghost btn-sm gap-2"
                    href="{{ Route::url($base . '&scope=settings') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS') }}
                </a>
            @endif

            @if ($model->isFollowing())
                <a class="btn btn-outline btn-sm"
                    data-text-follow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL') }}"
                    data-text-unfollow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL') }}"
                    href="{{ Route::url($base . '&scope=unfollow') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL') }}
                </a>
            @else
                <a class="btn btn-primary btn-sm"
                    data-text-follow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL') }}"
                    data-text-unfollow="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL') }}"
                    href="{{ Route::url($base . '&scope=follow') }}">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL') }}
                </a>
            @endif
        </div>
    @endif

    @if ($rows->total() > 0)
        <div id="posts"
            data-base="{{ rtrim(Request::base(true), '/') }}"
            class="{{ User::isGuest() ? 'loggedout' : 'loggedin' }} columns-1 sm:columns-2 lg:columns-3 gap-4">

            @if (!User::isGuest() && $params->get('access-create-collection') && !Request::getInt('no_html', 0))
                <div class="post new-collection break-inside-avoid mb-4">
                    <a class="btn btn-primary gap-2"
                        href="{{ Route::url($base . '&scope=new') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION') }}
                    </a>
                </div>
            @endif

            @foreach ($rows as $row)
                @php
                $scopeBase = $base . '&scope=' . $row->get('alias');
                $followTxt = Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW');
                $unfollowTxt = Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW');
                @endphp
                <div class="card bg-base-100 shadow-sm mb-4 break-inside-avoid {{ $row->get('access') == 4 ? 'border-l-4 border-warning' : '' }}"
                    id="b{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}">
                    <div class="card-body">
                        @php
                        $__view->view('default_collection', 'post')
                            ->set('row', $row)
                            ->set('collection', $row)
                            ->display();
                        @endphp

                        @if ($tags = $row->item()->tags('cloud'))
                            <div class="tags-wrap mt-2">
                                {!! $tags !!}
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-base-300">
                            <div class="flex gap-3 text-sm opacity-70">
                                <span>
                                    {!! Lang::txt('PLG_GROUPS_COLLECTIONS_POST_LIKES', $row->get('positive', 0)) !!}
                                </span>
                                <span>
                                    {!! Lang::txt('PLG_GROUPS_COLLECTIONS_POST_POSTS', $row->get('posts', 0)) !!}
                                </span>
                            </div>
                            <div class="flex gap-2">
                                @if (!User::isGuest())
                                    @if ($row->isFollowing())
                                        <a class="btn btn-outline btn-xs"
                                            data-id="{{ $row->get('id') }}"
                                            data-text-follow="{{ $followTxt }}"
                                            data-text-unfollow="{{ $unfollowTxt }}"
                                            href="{{ Route::url($scopeBase . '/unfollow') }}">
                                            {{ $unfollowTxt }}
                                        </a>
                                    @else
                                        <a class="btn btn-primary btn-xs"
                                            data-id="{{ $row->get('id') }}"
                                            data-text-follow="{{ $followTxt }}"
                                            data-text-unfollow="{{ $unfollowTxt }}"
                                            href="{{ Route::url($scopeBase . '/follow') }}">
                                            {{ $followTxt }}
                                        </a>
                                    @endif

                                    @if ($params->get('access-manage-collection'))
                                        @if ($params->get('access-edit-collection'))
                                            <a class="btn btn-ghost btn-xs"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($scopeBase . '/edit') }}"
                                                title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT') }}">
                                                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT') }}
                                            </a>
                                        @endif
                                        @if ($params->get('access-delete-collection'))
                                            <a class="btn btn-error btn-xs btn-outline"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($scopeBase . '/delete') }}"
                                                title="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE') }}">
                                                {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE') }}
                                            </a>
                                        @endif
                                    @else
                                        <a class="btn btn-ghost btn-xs"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($scopeBase . '/collect') }}">
                                            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT') }}
                                        </a>
                                    @endif
                                @else
                                    @php
                                    $returnUrl = base64_encode(Route::url($scopeBase, false, true));
                                    $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $returnUrl, false);
                                    @endphp
                                    <a class="btn btn-ghost btn-xs tooltip"
                                        href="{{ $loginUrl }}"
                                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs tooltip"
                                        href="{{ $loginUrl }}"
                                        data-tip="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW') }}">
                                        {{ $followTxt }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($total > $filters['limit'])
            @php
            $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('scope', 'all');
            @endphp
            {!! $pageNav->render() !!}
        @endif
    @else
        <div id="collection-introduction" class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @if ($params->get('access-create-collection'))
                    <div class="prose mb-4">
                        <ol>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP1') }}</li>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP2') }}</li>
                            <li>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP3') }}</li>
                        </ol>
                    </div>
                    <a class="btn btn-primary gap-2"
                        href="{{ Route::url($base . '&scope=new') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION') }}
                    </a>
                    <div class="mt-4">
                        <p class="font-semibold">
                            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_TITLE') }}
                        </p>
                        <p>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_DESC') }}</p>
                    </div>
                @else
                    <p>{{ Lang::txt('PLG_GROUPS_COLLECTIONS_NO_COLLECTIONS_FOUND') }}</p>
                @endif
            </div>
        </div>
    @endif
</form>
