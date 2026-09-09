@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$item = $post->item();
$base = $member->link() . '&active=' . $name;

$__view->css()->js();

$allow_comments = Component::params('com_collections')->get('allow_comments');
@endphp

<div class="post full {{ $item->type() }}"
    id="b{{ $post->get('id') }}"
    data-id="{{ $post->get('id') }}"
    data-closeup-url="{{ Route::url($base . '&task=post/' . $post->get('id')) }}"
    data-width="600"
    data-height="350">
    <div class="content">
        <div class="creator attribution cf">
            @if ($item->get('type') == 'file' || $item->get('type') == 'collection')
                @php
                    $name = e(stripslashes($item->creator()->get('name')));
                @endphp
                @if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels()))
                    <a href="{{ Route::url($item->creator()->link()) }}"
                        title="{{ $name }}"
                        class="img-link">
                        <img src="{{ $item->creator()->picture() }}"
                            alt="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) }}" />
                    </a>
                @else
                    <span class="img-link">
                        <img src="{{ $item->creator()->picture() }}"
                            alt="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) }}" />
                    </span>
                @endif
                <p>
                    <a href="{{ Route::url($item->creator()->link()) }}">
                        {{ e(stripslashes($item->creator()->get('name'))) }}
                    </a> created this post
                    <br />
                    <span class="entry-date">
                        <span class="entry-date-at">@</span>
                        <span class="time"><time datetime="{{ $item->created() }}">{{ $item->created('time') }}</time></span>
                        <span class="entry-date-on">on</span>
                        <span class="date"><time datetime="{{ $item->created() }}">{{ $item->created('date') }}</time></span>
                    </span>
                </p>
            @else
                <p class="typeof {{ $item->get('type') }}">
                    {{ e($item->type('title')) }}
                </p>
            @endif
        </div>

        {!! $__view->view('default_' . $item->type(), 'post')
            ->set('actual', true)
            ->set('name', $name)
            ->set('option', $option)
            ->set('member', $member)
            ->set('params', $params)
            ->set('row', $post)
            ->loadTemplate() !!}

        @if (count($item->tags()) > 0)
            <div class="tags-wrap">{!! $item->tags('render') !!}</div>
        @endif

        <div class="meta">
            <p class="stats">
                <span class="likes">{{ Lang::txt('%s likes', $item->get('positive', 0)) }}</span>
                @if ($allow_comments)
                    <span class="comments">{{ Lang::txt('%s comments', $item->get('comments', 0)) }}</span>
                @endif
                <span class="reposts">{{ Lang::txt('%s reposts', $item->get('reposts', 0)) }}</span>
            </p>
        </div>

        @php
            $postCreatorName = e(stripslashes($post->creator()->get('name')));
            $postCreatorLink = Route::url($post->creator()->link());
        @endphp
        <div class="convo attribution clearfix">
            <a href="{{ $postCreatorLink }}"
                title="{{ $postCreatorName }}"
                class="img-link">
                <img src="{{ $post->creator()->picture() }}"
                    alt="Profile picture of {{ $postCreatorName }}" />
            </a>
            <p>
                @php
                    $who = e(stripslashes($post->creator()->get('name')));
                    if (in_array($post->creator()->get('access'), User::getAuthorisedViewLevels())) {
                        $who = '<a href="' . Route::url($post->creator()->link()) . '">' . $postCreatorName . '</a>';
                    }
                    $where = '<a href="' . Route::url($base . '&task=' . $collection->get('alias')) . '">'
                        . e(stripslashes($collection->get('title'))) . '</a>';
                @endphp
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where) !!}
                <br />
                <span class="entry-date">
                    <span class="entry-date-at">@</span>
                    <span class="time"><time datetime="{{ $post->created() }}">{{ $post->created('time') }}</time></span>
                    <span class="entry-date-on">on</span>
                    <span class="date"><time datetime="{{ $post->created() }}">{{ $post->created('date') }}</time></span>
                </span>
            </p>
        </div>

        @if ($item->get('comments') && $allow_comments)
            <div class="commnts">
                @foreach ($item->comments() as $comment)
                    @php
                        $cuser = $comment->creator;
                    @endphp
                    <div class="comment convo clearfix" id="c{{ $comment->get('id') }}">
                        <a href="{{ Route::url($cuser->link()) }}" class="img-link">
                            <img src="{{ $cuser->picture($comment->anonymous) }}"
                                class="profile user_image"
                                alt="Profile picture of {{ e(stripslashes($cuser->get('name'))) }}" />
                        </a>
                        <p>
                            <a href="{{ Route::url($cuser->link()) }}">
                                {{ e(stripslashes($cuser->get('name'))) }}
                            </a> said <br />
                            <span class="entry-date">
                                <span class="entry-date-at">@</span>
                                <span class="time"><time datetime="{{ $comment->get('created') }}">{{ $comment->created('time') }}</time></span>
                                <span class="entry-date-on">on</span>
                                <span class="date"><time datetime="{{ $comment->get('created') }}">{{ $comment->created('date') }}</time></span>
                            </span>
                        </p>
                        <blockquote>
                            <p>{!! stripslashes($comment->content) !!}</p>
                        </blockquote>
                    </div>
                @endforeach
            </div>
        @endif

        @if (!User::isGuest() && $allow_comments)
            @php
                $now = Date::of('now');
            @endphp
            <div class="commnts">
                <div class="comment convo clearfix">
                    <a href="{{ Route::url('index.php?option=com_members&id=' . User::get('id')) }}" class="img-link">
                        <img src="{{ User::picture(0) }}"
                            class="profile user_image"
                            alt="Profile picture of {{ e(stripslashes(User::get('name'))) }}" />
                    </a>
                    <p>
                        <a href="{{ Route::url('index.php?option=com_members&id=' . User::get('id')) }}">
                            {{ e(stripslashes(User::get('name'))) }}
                        </a> will say <br />
                        <span class="entry-date">
                            <span class="entry-date-at">@</span>
                            <span class="time"><time datetime="{{ $now }}">{{ Date::of($now)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) }}</time></span>
                            <span class="entry-date-on">on</span>
                            <span class="date"><time datetime="{{ $now }}">{{ Date::of($now)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time></span>
                        </span>
                    </p>
                    <form action="{{ Route::url($base . '&task=post/' . $post->get('id') . '/savecomment') }}"
                        method="post"
                        id="comment-form"
                        enctype="multipart/form-data">
                        <fieldset>
                            <input type="hidden" name="comment[id]" value="0" />
                            <input type="hidden" name="comment[item_id]" value="{{ $item->get('id') }}" />
                            <input type="hidden" name="comment[item_type]" value="collection" />
                            <input type="hidden" name="comment[state]" value="1" />
                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="id" value="{{ $member->get('id') }}" />
                            <input type="hidden" name="scope" value="post/{{ $post->get('id') }}/savecomment" />
                            <input type="hidden" name="action" value="savecomment" />
                            <input type="hidden" name="no_html" value="{{ $no_html }}" />
                            {!! Html::input('token') !!}

                            <label for="comment-content" class="sr-only">{{ Lang::txt('Post comment') }}</label>
                            <textarea name="comment[content]" id="comment-content" cols="35" rows="3"></textarea>
                            <input type="submit" class="comment-submit" value="{{ Lang::txt('Post comment') }}" />
                        </fieldset>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
