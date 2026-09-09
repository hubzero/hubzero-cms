{{--
 * Single comment — author, content, attachments, reply form, nested replies
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Filesystem;
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $cls = $cls ?? 'odd';

    if ($wish->get('proposed_by') == $comment->get('created_by')) {
        $cls .= ' author';
    }

    $name = Lang::txt('JANONYMOUS');
    if (!$comment->get('anonymous')) {
        $name = e(stripslashes($comment->creator->get('name', $name)));
        if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
            $creatorLink = Route::url($comment->creator->link(), false);
            $name = '<a class="link link-hover" href="' . $creatorLink . '">' . $name . '</a>';
        }
    }

    $isReported = $comment->isReported();
    $commentContent = $isReported
        ? '<p class="text-warning">' . Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE') . '</p>'
        : $comment->content;

    $comment->set('listcategory', $wishlist->get('category'));
    $comment->set('listreference', $wishlist->get('referenceid'));

    $permalinkUrl = Route::url($wish->link() . '#c' . $comment->get('id'), false);
    $maxDepth = $wish->config()->get('comments_depth', 3);
    $isAuthor = $wish->get('proposed_by') == $comment->get('created_by');
@endphp

<li class="card bg-base-100 shadow-sm {{ $isAuthor ? 'border-l-4 border-primary' : '' }}"
    id="c{{ $comment->get('id') }}">
    <div class="card-body p-4">
        {{-- Comment header --}}
        <div class="flex items-center gap-2 text-sm mb-2">
            <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}"
                 alt="" class="w-8 h-8 rounded-full" />
            <span class="font-medium">{!! $name !!}</span>
            <a class="link link-hover text-base-content/60"
               href="{{ $permalinkUrl }}"
               title="{{ Lang::txt('COM_WISHLIST_PERMALINK') }}">
                <time datetime="{{ $comment->created() }}">
                    {{ $comment->created('date') }}
                    {{ Lang::txt('COM_WISHLIST_AT') }}
                    {{ $comment->created('time') }}
                </time>
            </a>
        </div>

        {{-- Comment body --}}
        <div class="prose prose-sm max-w-none mb-2">
            {!! $commentContent !!}
        </div>

        {{-- Attachments --}}
        @if($comment->attachments->count() > 0)
            <div class="flex flex-wrap gap-3 mt-2">
                @foreach($comment->attachments as $attachment)
                    @php
                        $desc = trim($attachment->get('description'))
                            ? $attachment->get('description')
                            : $attachment->get('filename');
                        $link = $attachment->link('download');
                    @endphp
                    @if($attachment->exists())
                        @if($attachment->isImage())
                            @php $attachUrl = Route::url($link, false); @endphp
                            <a href="{{ $attachUrl }}">
                                <img src="{{ $attachUrl }}"
                                     alt="{{ e($desc) }}"
                                     class="max-w-sm rounded-lg shadow-sm"
                                     {{ $attachment->width() > 400 ? 'width=400' : '' }} />
                            </a>
                        @else
                            <a class="btn btn-sm btn-ghost gap-2"
                               href="{{ Route::url($link, false) }}"
                               title="{{ e($desc) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                {{ e($desc) }}
                                <span class="text-xs text-base-content/60">
                                    ({{ \Hubzero\Utility\Number::formatBytes($attachment->size()) }})
                                </span>
                            </a>
                        @endif
                    @else
                        <span class="text-sm text-base-content/60">
                            {{ e($desc) }} &mdash; {{ Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Comment actions --}}
        @if(!$isReported)
            <div class="flex gap-2 mt-3 text-sm">
                @if($depth < $maxDepth)
                    @php
                        $isActiveReply = (Request::getInt('reply', 0) == $comment->get('id'));
                        $replyUrl = $isActiveReply
                            ? Route::url($comment->link(), false)
                            : Route::url($comment->link('reply'), false);
                    @endphp
                    <a class="btn btn-xs btn-ghost"
                       href="{{ $replyUrl }}"
                       data-rel="comment-form{{ $comment->get('id') }}">
                        {{ $isActiveReply ? Lang::txt('JCANCEL') : Lang::txt('COM_WISHLIST_REPLY') }}
                    </a>
                @endif
                <a class="btn btn-xs btn-ghost"
                   href="{{ Route::url($comment->link('report'), false) }}">
                    {{ Lang::txt('COM_WISHLIST_REPORT_ABUSE') }}
                </a>
            </div>
        @endif

        {{-- Reply form --}}
        @if($depth < $maxDepth)
            @php
                $hideReply = (Request::getInt('reply', 0) != $comment->get('id'));
                $anonName = !$comment->get('anonymous') ? strip_tags($name) : Lang::txt('JANONYMOUS');
            @endphp
            <div class="{{ $hideReply ? 'hidden' : '' }} mt-4 p-3 bg-base-200/50 rounded-lg"
                 id="comment-form{{ $comment->get('id') }}">
                @if(User::isGuest())
                    <p class="text-warning text-sm">
                        @php
                            $loginLink = '<a class="link" href="'
                                . Route::url(
                                    'index.php?option=com_users&view=login&return='
                                    . base64_encode(Route::url($wish->link(), false, true)),
                                    false
                                ) . '">' . Lang::txt('COM_WISHLIST_LOGIN') . '</a>';
                        @endphp
                        {!! Lang::txt('COM_WISHLIST_PLEASE_LOGIN_TO_COMMENT', $loginLink) !!}
                    </p>
                @else
                    <form id="cform{{ $comment->get('id') }}"
                          action="{{ Route::url($wish->link(), false) }}"
                          method="post" enctype="multipart/form-data">
                        <p class="text-sm font-medium mb-2">
                            {{ Lang::txt('COM_WISHLIST_REPLYING_TO', $anonName) }}
                        </p>

                        <input type="hidden" name="comment[item_type]"
                               value="{{ $comment->get('item_type') }}" />
                        <input type="hidden" name="comment[item_id]"
                               value="{{ $comment->get('item_id') }}" />
                        <input type="hidden" name="comment[parent]"
                               value="{{ $comment->get('id') }}" />
                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="listid"
                               value="{{ e($wish->get('wishlist')) }}" />
                        <input type="hidden" name="wishid"
                               value="{{ e($wish->get('id')) }}" />
                        <input type="hidden" name="task" value="savereply" />
                        <input type="hidden" name="referenceid"
                               value="{{ $wishlist->get('referenceid') }}" />
                        <input type="hidden" name="cat" value="wish" />
                        {!! Html::input('token') !!}

                        <x-form-field name="comment[content]"
                                      inputId="comment_{{ $comment->get('id') }}_content"
                                      :label="Lang::txt('COM_WISHLIST_ENTER_COMMENTS')">
                            {!! $__view->editor(
                                'comment[content]', '', 35, 4,
                                'comment_' . $comment->get('id') . '_content',
                                ['class' => 'textarea textarea-bordered w-full']
                            ) !!}
                        </x-form-field>

                        <x-form-field name="comment[anonymous]"
                                      inputId="comment-{{ $comment->get('id') }}-anonymous"
                                      :label="Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY')"
                                      type="checkbox">
                            <input type="checkbox" name="comment[anonymous]"
                                   id="comment-{{ $comment->get('id') }}-anonymous"
                                   class="checkbox checkbox-sm" value="1" />
                        </x-form-field>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                {{ Lang::txt('COM_WISHLIST_SUBMIT') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Nested replies --}}
    @if($depth < $maxDepth)
        @php
            $replies = $comment->replies()
                ->whereIn('state', [
                    \Components\Wishlist\Models\Comment::STATE_PUBLISHED,
                    \Components\Wishlist\Models\Comment::STATE_FLAGGED,
                ])
                ->rows();
        @endphp
        @if($replies->count() > 0)
            {!! $__view->view('_list')
                ->set('parent', $comment->get('id'))
                ->set('cls', $cls)
                ->set('depth', $depth)
                ->set('option', $option)
                ->set('comments', $replies)
                ->set('wishlist', $wishlist)
                ->set('wish', $wish)
                ->loadTemplate() !!}
        @endif
    @endif
</li>
