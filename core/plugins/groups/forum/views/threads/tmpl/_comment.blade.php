{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('like.js');

$likeArray = $like;
$countLike = count($likeArray);
$currentUserId = User::get('id');

$userLikesComment = false;
$userNameLikesArray = "";

foreach ($likeArray as $likeObj) {
    if ($currentUserId == $likeObj->userId) {
        $userLikesComment = true;
    }
    $userNameLikesArray .= "/" . ($likeObj->userName) . "#" . ($likeObj->userId);
}

$userNameLikesArray = substr($userNameLikesArray, 1);

$comment->set('section', $filters['section']);
$comment->set('category', $category->get('alias'));

$config->set('access-edit-post', false);
if (User::get('id') == $comment->get('created_by')) {
    $config->set('access-edit-post', true);
}

$name = Lang::txt('JANONYMOUS');
if (!$comment->get('anonymous')) {
    $name = e(stripslashes($comment->creator->get('name', $name)));
    if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $name = '<a href="' . Route::url($comment->creator->link()) . '">' . $name . '</a>';
    }
}

$cls = isset($cls) ? $cls : 'odd';

if ($comment->isReported()) {
    $commentBody = '<p class="alert alert-warning">' . Lang::txt('PLG_GROUPS_FORUM_COMMENT_REPORTED') . '</p>';
} else {
    $commentBody = $comment->comment;
}

$canReply = false;
@endphp

<li class="comment {{ $cls }}{{ !$comment->get('parent') ? ' start' : '' }}" id="c{{ $comment->get('id') }}">
    <p class="comment-member-photo">
        <img class="rounded-full" src="{{ $comment->creator->picture($comment->get('anonymous')) }}" alt="" />
    </p>
    <div class="comment-content">
        <p class="comment-title">
            <strong>{!! $name !!}</strong>
            <a class="permalink link link-hover text-sm text-base-content/70"
                href="{{ Route::url($comment->link('anchor')) }}"
                title="{{ Lang::txt('PLG_GROUPS_FORUM_PERMALINK') }}">
                <span class="comment-date-at">{{ Lang::txt('PLG_GROUPS_FORUM_AT') }}</span>
                @php
                $createdDatetime = $comment->created();
                $createdTime = $comment->created('time');
                $createdDate = $comment->created('date');
                @endphp
                <span class="time">
                    <time datetime="{{ $createdDatetime }}">{{ $createdTime }}</time>
                </span>
                <span class="comment-date-on">{{ Lang::txt('PLG_GROUPS_FORUM_ON') }}</span>
                <span class="date">
                    <time datetime="{{ $createdDatetime }}">{{ $createdDate }}</time>
                </span>
                @if ($comment->wasModified())
                    @php
                    $modifiedDatetime = $comment->modified();
                    $modifiedTime = $comment->modified('time');
                    $modifiedDate = $comment->modified('date');
                    @endphp
                    &mdash;
                    {{ Lang::txt('PLG_GROUPS_FORUM_EDITED') }}
                    <span class="comment-date-at">{{ Lang::txt('PLG_GROUPS_FORUM_AT') }}</span>
                    <span class="time">
                        <time datetime="{{ $modifiedDatetime }}">{{ $modifiedTime }}</time>
                    </span>
                    <span class="comment-date-on">{{ Lang::txt('PLG_GROUPS_FORUM_ON') }}</span>
                    <span class="date">
                        <time datetime="{{ $modifiedDatetime }}">{{ $modifiedDate }}</time>
                    </span>
                @endif
            </a>
        </p>
        <div class="comment-body">
            {!! $commentBody !!}

            {{-- Like button - only show for logged in users --}}
            @if (!User::isGuest())
                <div class="likeContainer flex items-center gap-2 mt-2">
                    <a class="icon-heart like text-xl mr-1 {{ $userLikesComment ? 'text-error hover:text-error/80 userLiked' : 'text-base-content/40 hover:text-base-content/60' }}" href="#"
                        data-thread="{{ $thread->get('id') }}"
                        data-post="{{ $comment->get('id') }}"
                        data-user="{{ User::get('id') }}"
                        data-user-name="{{ User::get('name') }}"
                        data-likes-list="{{ $userNameLikesArray }}"
                        data-count="{{ $countLike }}"></a>
                    <span class="likesStat text-sm {{ $countLike > 0 ? 'underline cursor-pointer hover:text-base-content' : 'no-underline cursor-default' }}">
                        {{ ($countLike > 0) ? "View Likes (" . $countLike . ")" : "No Likes" }}
                    </span>
                </div>

                <div class="whoLikedPost overflow-hidden max-h-0 transition-all duration-700 ease-in-out mt-1">
                    @if (strlen($userNameLikesArray) > 0)
                        <div class="names bg-base-200 p-2.5 rounded text-right text-xs">
                            @php
                            $nameArray = preg_split("#/#", $userNameLikesArray);
                            $links = [];
                            foreach ($nameArray as $nameString) {
                                $parts = explode("#", $nameString);
                                $userName = $parts[0];
                                $userId = isset($parts[1]) ? $parts[1] : '0';
                                $userProfileUrl = "/members/$userId/profile";
                                $links[] = '<a href="' . $userProfileUrl . '" target="_blank">' . $userName . '</a>';
                            }
                            echo join(", ", $links) . " liked this";
                            @endphp
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="comment-attachments">
            @php
            $publishedState = \Components\Forum\Models\Attachment::STATE_PUBLISHED;
            $attachments = $comment->attachments()
                ->whereEquals('state', $publishedState)
                ->rows();
            @endphp
            @foreach ($attachments as $attachment)
                @php
                if (!trim($attachment->get('description'))) {
                    $attachment->set('description', $attachment->get('filename'));
                }
                @endphp
                @if ($attachment->exists())
                    @php $attachLink = $attachment->link(); @endphp
                    @if ($attachment->isImage())
                        @if ($attachment->width() > 400)
                            <p><a href="{{ Route::url($attachLink) }}"><img src="{{ Route::url($attachLink) }}" alt="{{ e($attachment->get('description')) }}" width="400" /></a></p>
                        @else
                            <p><img src="{{ Route::url($attachLink) }}" alt="{{ e($attachment->get('description')) }}" /></p>
                        @endif
                    @else
                        <a class="attachment {{ Filesystem::extension($attachment->get('filename')) }}"
                            href="{{ Route::url($attachLink) }}"
                            title="{{ e($attachment->get('description')) }}">
                            <p class="attachment-description">{{ e($attachment->get('description')) }}</p>
                            <p class="attachment-meta">
                                <span class="attachment-size">{{ Hubzero\Utility\Number::formatBytes($attachment->size()) }}</span>
                                <span class="attachment-action">{{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}</span>
                            </p>
                        </a>
                    @endif
                @else
                    <div class="attachment {{ Filesystem::extension($attachment->get('filename')) }}"
                        title="{{ e($attachment->get('description')) }}">
                        <p class="attachment-description">{{ e($attachment->get('description')) }}</p>
                        <p class="attachment-meta">
                            <span class="attachment-size">{{ e($attachment->get('filename')) }}</span>
                            <span class="attachment-action">{{ Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') }}</span>
                        </p>
                    </div>
                @endif
            @endforeach
        </div>

        @if ($group->published == 1)
            <p class="comment-options flex gap-2 mt-2">
            @if ($config->get('access-manage-thread')
                || $config->get('access-delete-thread')
                || $config->get('access-edit-thread')
                || $config->get('access-delete-post')
                || $config->get('access-edit-post'))
                @php
                $canDeletePost = $config->get('access-delete-post');
                $isCreator = $comment->get('created_by') == User::get('id');
                @endphp
                @if ($comment->get('parent') && ($canDeletePost || $isCreator))
                    <a class="btn btn-ghost btn-xs text-error"
                        data-id="c{{ $comment->get('id') }}"
                        href="{{ Route::url($comment->link('delete')) }}">{{ Lang::txt('PLG_GROUPS_FORUM_DELETE') }}</a>
                @endif
                @php
                $canEditComment = $config->get('access-edit-thread') || $config->get('access-edit-post');
                @endphp
                @if ($canEditComment && $isCreator)
                    <a class="btn btn-ghost btn-xs"
                        data-id="c{{ $comment->get('id') }}"
                        href="{{ Route::url($comment->link('edit')) }}">{{ Lang::txt('PLG_GROUPS_FORUM_EDIT') }}</a>
                @endif
            @endif

            @if (!$comment->isReported())
                @php
                $isThreadOpen = !$thread->get('closed');
                $isTreeThreading = $config->get('threading') == 'tree';
                $maxDepth = $config->get('threading_depth', 3);
                $withinDepth = $depth < $maxDepth;
                $canReply = $isThreadOpen && $isTreeThreading && $withinDepth;
                @endphp
                @if ($canReply)
                    @if (Request::getInt('reply', 0) == $comment->get('id'))
                        <a class="btn btn-ghost btn-xs active"
                            data-txt-active="{{ Lang::txt('JCANCEL') }}"
                            data-txt-inactive="{{ Lang::txt('PLG_GROUPS_FORUM_REPLY') }}"
                            href="{{ Route::url($comment->link()) }}"
                            rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('JCANCEL') }}</a>
                    @else
                        <a class="btn btn-ghost btn-xs"
                            data-txt-active="{{ Lang::txt('JCANCEL') }}"
                            data-txt-inactive="{{ Lang::txt('PLG_GROUPS_FORUM_REPLY') }}"
                            href="{{ Route::url($comment->link('reply')) }}"
                            rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('PLG_GROUPS_FORUM_REPLY') }}</a>
                    @endif
                @endif
                <a class="btn btn-ghost btn-xs"
                    href="{{ Route::url($comment->link('abuse')) }}"
                    rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('PLG_GROUPS_FORUM_REPORT_ABUSE') }}</a>
            @endif
            </p>

            @if ($canReply)
                @php
                $commentId = $comment->get('id');
                $hideClass = (Request::getInt('reply', 0) != $commentId) ? ' hide' : '';
                @endphp
                <div class="comment-add{{ $hideClass }}"
                    id="comment-form{{ $commentId }}">
                    <form id="cform{{ $comment->get('id') }}"
                        action="{{ Route::url($thread->link()) }}"
                        method="post"
                        enctype="multipart/form-data">
                        <fieldset>
                            @php
                            $replyToName = !$comment->get('anonymous')
                                ? $name
                                : Lang::txt('JANONYMOUS');
                            @endphp
                            <legend>
                                <span>{!! Lang::txt('PLG_GROUPS_FORUM_REPLYING_TO', $replyToName) !!}</span>
                            </legend>

                            <input type="hidden" name="fields[id]" value="0" />
                            <input type="hidden" name="fields[state]" value="1" />
                            <input type="hidden" name="fields[access]" value="{{ $thread->get('access', 0) }}" />
                            <input type="hidden" name="fields[scope]" value="{{ $thread->get('scope') }}" />
                            <input type="hidden" name="fields[category_id]" value="{{ $thread->get('category_id') }}" />
                            <input type="hidden" name="fields[scope_id]" value="{{ $thread->get('scope_id') }}" />
                            <input type="hidden" name="fields[scope_sub_id]" value="{{ $thread->get('scope_sub_id') }}" />
                            <input type="hidden" name="fields[object_id]" value="{{ $thread->get('object_id') }}" />
                            <input type="hidden" name="fields[parent]" value="{{ $comment->get('id') }}" />
                            <input type="hidden" name="fields[thread]" value="{{ $comment->get('thread') }}" />
                            <input type="hidden" name="fields[created]" value="" />
                            <input type="hidden" name="fields[created_by]" value="{{ User::get('id') }}" />

                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                            <input type="hidden" name="active" value="forum" />
                            <input type="hidden" name="action" value="savethread" />

                            {!! Html::input('token') !!}

                            <div class="form-group">
                                @php
                                $fieldId = 'field_' . $commentId . '_comment';
                                @endphp
                                <label for="{{ $fieldId }}" class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS') }}</span>
                                </label>
                                {!! $__view->editor(
                                    'fields[comment]',
                                    '',
                                    35,
                                    4,
                                    $fieldId,
                                    ['class' => 'minimal no-footer']
                                ) !!}
                            </div>

                            <div class="form-group">
                                @php $fileId = 'comment-' . $commentId . '-file'; @endphp
                                <label for="{{ $fileId }}" class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE') }}:</span>
                                </label>
                                <input type="file"
                                    class="file-input file-input-bordered w-full"
                                    name="upload"
                                    id="{{ $fileId }}" />
                            </div>

                            @if ($config->get('allow_anonymous'))
                                <div class="form-group">
                                    <label class="label cursor-pointer justify-start gap-2"
                                        for="comment-{{ $comment->get('id') }}-anonymous">
                                        <input class="checkbox checkbox-sm"
                                            type="checkbox"
                                            name="fields[anonymous]"
                                            id="comment-{{ $comment->get('id') }}-anonymous"
                                            value="1" />
                                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS') }}</span>
                                    </label>
                                </div>
                            @endif

                            <p class="submit mt-2">
                                <input type="submit"
                                    class="btn btn-primary btn-sm"
                                    value="{{ Lang::txt('PLG_GROUPS_FORUM_SUBMIT') }}" />
                            </p>
                        </fieldset>
                    </form>
                </div>
            @endif
        @endif
    </div>
    @php
    if ($config->get('threading') == 'tree' && $depth < $config->get('threading_depth', 3)) {
        $__view->view('_list')
             ->set('option', $option)
             ->set('group', $group)
             ->set('comments', $comment->get('replies'))
             ->set('thread', $thread)
             ->set('likes', $likes)
             ->set('parent', $comment->get('id'))
             ->set('config', $config)
             ->set('depth', $depth)
             ->set('cls', $cls)
             ->set('filters', $filters)
             ->set('category', $category)
             ->display();
    }
    @endphp
</li>
