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

@if (!User::isGuest() && !$params->get('access-create-item'))
    <ul id="page_options">
        <li>
            @if ($model->isFollowing())
                <a class="btn btn-outline btn-sm"
                    data-text-follow="{{ Lang::txt('Follow All') }}"
                    data-text-unfollow="{{ Lang::txt('Unfollow All') }}"
                    href="{{ Route::url($base . '&scope=unfollow') }}">
                    {{ Lang::txt('Unfollow All') }}
                </a>
            @else
                <a class="btn btn-primary btn-sm"
                    data-text-follow="{{ Lang::txt('Follow All') }}"
                    data-text-unfollow="{{ Lang::txt('Unfollow All') }}"
                    href="{{ Route::url($base . '&scope=follow') }}">
                    {{ Lang::txt('Follow All') }}
                </a>
            @endif
        </li>
    </ul>
@endif

<form method="get" action="{{ Route::url($base . '&scope=following') }}" id="collections">
    @php
    $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('group', $group)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'following')
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', $rows->total())
        ->display();
    @endphp

    @if ($rows->total() > 0)
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <th class="w-12">
                                @if ($row->following()->image())
                                    <div class="avatar">
                                        <div class="w-10 rounded-full">
                                            <img src="{{ $row->following()->image() }}"
                                                alt="Profile picture of {{ e(stripslashes($row->following()->title())) }}" />
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-ghost">
                                        {{ $row->get('following_id') }}
                                    </span>
                                @endif
                            </th>
                            <td>
                                <a class="link link-hover font-semibold"
                                    href="{{ Route::url($row->following()->link()) }}">
                                    {{ e(stripslashes($row->following()->title())) }}
                                </a>
                                @if ($row->get('following_type') == 'collection')
                                    {{ Lang::txt('by %s', e(stripslashes($row->following()->creator('name')))) }}
                                @endif
                                <br />
                                <span class="text-sm opacity-70">
                                    <span>{!! Lang::txt('<strong>%s</strong> followers', $row->count('followers')) !!}</span>
                                    @if ($row->get('following_type') != 'collection')
                                        <span class="ml-2">{!! Lang::txt('<strong>%s</strong> following', $row->count('following')) !!}</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if ($params->get('access-manage-collection'))
                                    <a class="btn btn-outline btn-xs"
                                        data-id="{{ $row->get('following_id') }}"
                                        data-text-follow="{{ Lang::txt('Follow') }}"
                                        data-text-unfollow="{{ Lang::txt('Unfollow') }}"
                                        href="{{ Route::url($row->following()->link('unfollow')) }}">
                                        {{ Lang::txt('Unfollow') }}
                                    </a>
                                @endif
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
        $pageNav->setAdditionalUrlParam('scope', 'following');
        @endphp
        {!! $pageNav->render() !!}
    @else
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @if ($params->get('access-manage-collection'))
                    <div class="prose mb-4">
                        <ol>
                            <li>{{ Lang::txt('Find a member or collection you like.') }}</li>
                            <li>{{ Lang::txt('Click on the "follow" button.') }}</li>
                            <li>{{ Lang::txt('Come back to collections and see all the posts!') }}</li>
                        </ol>
                    </div>
                    <div class="mt-4">
                        <p class="font-semibold">{{ Lang::txt('What is following?') }}</p>
                        <p>{{ Lang::txt('"Following" someone means you\'ll see that person\'s posts on this page in real time. If he/she creates a new collection, you\'ll automatically follow the new collection as well.') }}</p>
                        <p>{{ Lang::txt('You can follow individual collections if you\'re only interested in seeing posts being added to specific collections.') }}</p>
                        <p>{{ Lang::txt('You can unfollow other people or collections at any time.') }}</p>
                    </div>
                @else
                    <p>{{ Lang::txt('This group is not following anyone or any collections.') }}</p>
                @endif
            </div>
        </div>
    @endif
</form>
