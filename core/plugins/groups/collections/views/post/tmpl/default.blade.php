{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$item = $post->item();
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;
@endphp

<div class="card bg-base-100 shadow-sm post full {{ $item->type() }}"
    id="b{{ $post->get('id') }}"
    data-id="{{ $post->get('id') }}"
    data-closeup-url="{{ Route::url($base . '&scope=post/' . $post->get('id')) }}"
    data-width="600"
    data-height="350">
    <div class="card-body">

        <div class="flex items-center gap-3 mb-4">
            @if ($item->get('type') == 'file' || $item->get('type') == 'collection')
                @php
                $creatorName = e(stripslashes($item->creator()->get('name')));
                @endphp
                @if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels()))
                    <a href="{{ Route::url($item->creator()->link()) }}" class="avatar">
                        <div class="w-10 rounded-full">
                            <img src="{{ $item->creator()->picture() }}"
                                alt="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $creatorName) }}" />
                        </div>
                    </a>
                @else
                    <div class="avatar">
                        <div class="w-10 rounded-full">
                            <img src="{{ $item->creator()->picture() }}"
                                alt="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $creatorName) }}" />
                        </div>
                    </div>
                @endif
                <p class="text-sm">
                    <a href="{{ Route::url($item->creator()->link()) }}">
                        {{ e(stripslashes($item->creator()->get('name'))) }}
                    </a> created this post
                    <br />
                    <span class="text-xs opacity-60">
                        @ <time datetime="{{ $item->created() }}">{{ $item->created('time') }}</time>
                        on <time datetime="{{ $item->created() }}">{{ $item->created('date') }}</time>
                    </span>
                </p>
            @else
                <p class="badge badge-ghost">{{ e($item->type('title')) }}</p>
            @endif
        </div>

        @php
        $__view->view('default_' . $item->type(), 'post')
            ->set('name', $name)
            ->set('option', $option)
            ->set('group', $group)
            ->set('params', $params)
            ->set('row', $post)
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
        </div>

        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-base-200">
            @php
            $postCreatorName = e(stripslashes($post->creator()->get('name')));
            @endphp
            <a href="{{ Route::url($post->creator()->link()) }}" class="avatar">
                <div class="w-8 rounded-full">
                    <img src="{{ $post->creator()->picture() }}"
                        alt="Profile picture of {{ $postCreatorName }}" />
                </div>
            </a>
            <p class="text-sm">
                @php
                $who = e(stripslashes($post->creator()->get('name')));
                if (in_array($post->creator()->get('access'), User::getAuthorisedViewLevels())) {
                    $who = '<a href="' . Route::url($post->creator()->link()) . '">' . $postCreatorName . '</a>';
                }
                $where = '<a href="' . Route::url($base . '&task=' . $collection->get('alias')) . '">'
                    . e(stripslashes($collection->get('title'))) . '</a>';
                @endphp
                {!! Lang::txt('PLG_GROUPS_COLLECTIONS_ONTO', $who, $where) !!}
                <br />
                <span class="text-xs opacity-60">
                    @ <time datetime="{{ $post->created() }}">{{ $post->created('time') }}</time>
                    on <time datetime="{{ $post->created() }}">{{ $post->created('date') }}</time>
                </span>
            </p>
        </div>

        {{-- Comments --}}
        @if ($item->get('comments'))
            @foreach ($item->comments() as $comment)
                @php
                $cuser = $comment->creator();
                @endphp
                <div class="flex items-start gap-3 mt-3 pt-3 border-t border-base-200">
                    <a href="{{ Route::url($cuser->link()) }}" class="avatar">
                        <div class="w-8 rounded-full">
                            <img src="{{ $cuser->picture($comment->get('anonymous')) }}"
                                alt="Profile picture of {{ e(stripslashes($cuser->get('name'))) }}" />
                        </div>
                    </a>
                    <div>
                        <p class="text-sm">
                            <a href="{{ Route::url($cuser->link()) }}">
                                {{ e(stripslashes($cuser->get('name'))) }}
                            </a> said
                            <br />
                            <span class="text-xs opacity-60">
                                @ <time datetime="{{ $comment->get('created') }}">{{ $comment->created('time') }}</time>
                                on <time datetime="{{ $comment->get('created') }}">{{ $comment->created('date') }}</time>
                            </span>
                        </p>
                        <blockquote class="mt-1 pl-3 border-l-2 border-base-300 text-sm">
                            <p>{!! stripslashes($comment->content) !!}</p>
                        </blockquote>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- Comment form --}}
        @if (!User::isGuest())
            @php
            $now = Date::of('now');
            @endphp
            <div class="flex items-start gap-3 mt-3 pt-3 border-t border-base-200">
                <a href="{{ Route::url('index.php?option=com_members&id=' . User::get('id')) }}" class="avatar">
                    <div class="w-8 rounded-full">
                        <img src="{{ User::picture(0) }}"
                            alt="Profile picture of {{ e(stripslashes(User::get('name'))) }}" />
                    </div>
                </a>
                <div class="flex-1">
                    <p class="text-sm mb-2">
                        <a href="{{ Route::url('index.php?option=com_members&id=' . User::get('id')) }}">
                            {{ e(stripslashes(User::get('name'))) }}
                        </a> will say
                        <br />
                        <span class="text-xs opacity-60">
                            @ <time datetime="{{ $now }}">{{ Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')) }}</time>
                            on <time datetime="{{ $now }}">{{ Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time>
                        </span>
                    </p>
                    <form action="{{ Route::url($base . '&scope=post/' . $post->get('id') . '/savecomment') }}"
                        method="post"
                        id="comment-form"
                        enctype="multipart/form-data">
                        <fieldset>
                            <input type="hidden" name="comment[id]" value="0" />
                            <input type="hidden" name="comment[item_id]" value="{{ $item->get('id') }}" />
                            <input type="hidden" name="comment[item_type]" value="collection" />
                            <input type="hidden" name="comment[state]" value="1" />

                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
                            <input type="hidden" name="scope" value="post/{{ $post->get('id') }}/savecomment" />
                            <input type="hidden" name="action" value="savecomment" />
                            <input type="hidden" name="no_html" value="{{ $no_html }}" />

                            {!! Html::input('token') !!}

                            <textarea name="comment[content]"
                                class="textarea textarea-bordered w-full mb-2"
                                cols="35"
                                rows="3"></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ Lang::txt('Post comment') }}
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
