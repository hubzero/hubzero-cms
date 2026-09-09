{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&id=' . $member->get('uidNumber') . '&active=' . $name;
@endphp

<form method="get"
    action="{{ Route::url($base . '&task=' . $collection->get('alias')) }}"
    id="collections">

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="text-lg font-semibold">
            "{{ e(stripslashes($collection->get('title'))) }}"
        </span>
        <span class="badge badge-ghost">
            {!! Lang::txt('<strong>%s</strong> posts', $rows->total()) !!}
        </span>
        @if (!User::isGuest())
            @if ($rows && $params->get('access-create-item'))
                <a class="btn btn-primary btn-sm gap-2 tooltip"
                    data-tip="{{ Lang::txt('New post :: Add a new post to this collection') }}"
                    href="{{ Route::url($base . '&task=post/new&board=' . $collection->get('alias')) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    {{ Lang::txt('New post') }}
                </a>
            @else
                <a class="btn btn-primary btn-sm tooltip"
                    data-tip="{{ Lang::txt('Repost :: Watch this collection') }}"
                    href="{{ Route::url($base . '&task=' . $collection->get('alias') . '/follow') }}">
                    {{ Lang::txt('Follow') }}
                </a>
            @endif
        @endif
    </div>

    <div id="posts">
        @if ($rows->total() > 0)
            @foreach ($rows as $row)
                @php
                $item = $row->item();
                if ($item->get('state') == 2) {
                    $item->set('type', 'deleted');
                }
                $type = $item->get('type');
                if (!in_array($type, ['collection', 'deleted', 'image', 'file', 'text', 'link'])) {
                    $type = 'link';
                }
                @endphp
                <div class="card bg-base-100 shadow-sm mb-4 post {{ $type }}"
                    id="b{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}"
                    data-closeup-url="{{ Route::url($base . '&task=post/' . $row->get('id')) }}"
                    data-width="600"
                    data-height="350">
                    <div class="card-body">
                        @php
                        $__view->view('default_' . $type, 'post')
                            ->set('name', $name)
                            ->set('option', $option)
                            ->set('group', $group)
                            ->set('params', $params)
                            ->set('row', $row)
                            ->display();
                        @endphp

                        @if (count($item->tags()) > 0)
                            <div class="tags-wrap mt-2">
                                {!! $item->tags('render') !!}
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-base-300">
                            <div class="flex gap-3 text-sm opacity-70">
                                <span>{!! Lang::txt('%s likes', $item->get('positive', 0)) !!}</span>
                                <span>{!! Lang::txt('%s comments', $item->get('comments', 0)) !!}</span>
                                <span>{!! Lang::txt('%s reposts', $item->get('reposts', 0)) !!}</span>
                            </div>
                            @if (!User::isGuest())
                                <div class="flex gap-1">
                                    @if ($item->get('created_by') == User::get('id'))
                                        <a class="btn btn-ghost btn-xs"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/edit') }}">
                                            {{ Lang::txt('Edit') }}
                                        </a>
                                    @else
                                        <a class="btn btn-ghost btn-xs {{ $item->get('voted') ? 'btn-active' : '' }}"
                                            data-id="{{ $row->get('id') }}"
                                            data-text-like="{{ Lang::txt('Like') }}"
                                            data-text-unlike="{{ Lang::txt('Unlike') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/vote') }}">
                                            {{ $item->get('voted') ? Lang::txt('Unlike') : Lang::txt('Like') }}
                                        </a>
                                    @endif
                                    <a class="btn btn-ghost btn-xs"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/comment') }}">
                                        {{ Lang::txt('Comment') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/collect') }}">
                                        {{ Lang::txt('Collect') }}
                                    </a>
                                    @php
                                    $canDelete = $row->get('original')
                                        && ($item->get('created_by') == User::get('id')
                                            || $params->get('access-delete-item'));
                                    @endphp
                                    @if ($canDelete)
                                        <a class="btn btn-error btn-xs btn-outline"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/delete') }}">
                                            {{ Lang::txt('Delete') }}
                                        </a>
                                    @elseif ($row->get('created_by') == User::get('id') || $params->get('access-edit-item'))
                                        <a class="btn btn-warning btn-xs btn-outline"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/remove') }}">
                                            {{ Lang::txt('Remove') }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if ($row->original() || $item->get('created_by') != $member->get('uidNumber'))
                            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-base-200">
                                @php
                                $creatorName = e(stripslashes($item->creator()->get('name')));
                                $creatorUrl = Route::url('index.php?option=com_members&id=' . $item->get('created_by'));
                                $itemTime = Date::of($item->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                                $itemDate = Date::of($item->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                                @endphp
                                <a href="{{ $creatorUrl }}" class="avatar">
                                    <div class="w-8 rounded-full">
                                        <img src="{{ $item->creator()->picture(0) }}"
                                            alt="Profile picture of {{ $creatorName }}" />
                                    </div>
                                </a>
                                <p class="text-sm">
                                    <a href="{{ $creatorUrl }}">{{ $creatorName }}</a> posted
                                    <br />
                                    <span class="text-xs opacity-60">
                                        @ {{ $itemTime }} on {{ $itemDate }}
                                    </span>
                                </p>
                            </div>
                        @endif

                        @if (!$row->original())
                            @php
                            $repostCreatorName = e(stripslashes($row->creator()->get('name')));
                            $repostCreatorUrl = Route::url('index.php?option=com_members&id=' . $row->get('created_by'));
                            $collectionPath = $collection->get('is_default') ? '' : '/' . $collection->get('alias');
                            $collectionUrl = Route::url($base . $collectionPath);
                            $rowTime = Date::of($row->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                            $rowDate = Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                            @endphp
                            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-base-200">
                                <a href="{{ $repostCreatorUrl }}" class="avatar">
                                    <div class="w-8 rounded-full">
                                        <img src="{{ $member->picture(0) }}"
                                            alt="Profile picture of {{ $repostCreatorName }}" />
                                    </div>
                                </a>
                                <p class="text-sm">
                                    <a href="{{ $repostCreatorUrl }}">{{ $repostCreatorName }}</a>
                                    onto
                                    <a href="{{ $collectionUrl }}">{{ e(stripslashes($collection->get('title'))) }}</a>
                                    <br />
                                    <span class="text-xs opacity-60">
                                        @ {{ $rowTime }} on {{ $rowDate }}
                                    </span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    @if ($params->get('access-create-item'))
                        <div class="prose">
                            <ol>
                                <li>{{ Lang::txt('Find images, files, links or text you want to share.') }}</li>
                                <li>{{ Lang::txt('Click on "New post" button.') }}</li>
                                <li>{{ Lang::txt('Add anything extra you want (tags are nice).') }}</li>
                                <li>{{ Lang::txt('Done!') }}</li>
                            </ol>
                        </div>
                    @else
                        <p>{{ Lang::txt('No posts available for this collection.') }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</form>
