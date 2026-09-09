{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js();

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

<form method="get" action="{{ Route::url($base . '&scope=followers') }}" id="collections">
    @php
    $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('group', $group)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'followers')
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $total)
        ->set('following', ($params->get('access-can-follow') ? $following : 0))
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

    @if ($rows->total() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <caption class="text-left font-semibold p-2">
                    {{ Lang::txt('People following this group') }}
                </caption>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                        $followerTitle = e(stripslashes($row->follower()->title()));
                        $followerLink = Route::url($row->follower()->link());
                        $createdLocal = Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        @endphp
                        <tr>
                            <th class="w-12">
                                <div class="avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $row->follower()->image() }}"
                                            alt="Profile picture of {{ $followerTitle }}" />
                                    </div>
                                </div>
                            </th>
                            <td>
                                <a class="link link-hover font-semibold"
                                    href="{{ $followerLink }}">
                                    {{ $followerTitle }}
                                </a>
                                <br />
                                <span class="text-sm opacity-70">
                                    <span>{!! Lang::txt('<strong>%s</strong> followers', $row->count('followers')) !!}</span>
                                    <span class="ml-2">{!! Lang::txt('<strong>%s</strong> following', $row->count('following')) !!}</span>
                                </span>
                            </td>
                            <td>
                                <time datetime="{{ $row->get('created') }}">
                                    {{ $createdLocal }}
                                </time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
        $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
        $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
        $pageNav->setAdditionalUrlParam('active', 'collections');
        $pageNav->setAdditionalUrlParam('scope', 'followers');
        @endphp
        {!! $pageNav->render() !!}
    @else
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @if ($params->get('access-manage-collection'))
                    <p>
                        {{ Lang::txt('This group currently does not have anyone following it or any of its collections.') }}
                    </p>
                    <div class="mt-4">
                        <p class="font-semibold">{{ Lang::txt('What are followers?') }}</p>
                        <p>{{ Lang::txt('"Followers" are members that have decided to receive all public posts this group makes or all posts in one of this group\'s collections.') }}</p>
                        <p>{{ Lang::txt('Followers cannot see of your private collections or posts made to private collections.') }}</p>
                    </div>
                @else
                    <p>{{ Lang::txt('This group is not following anyone or any collections.') }}</p>
                @endif
            </div>
        </div>
    @endif
</form>
