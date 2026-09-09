{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

$cls = isset($cls) ? $cls : 'odd';

$name = Lang::txt('JANONYMOUS');
if (!$comment->get('anonymous')) {
    $name = e(stripslashes($comment->creator->get('name', $name)));
    if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $name = '<a href="' . Route::url($comment->creator->link()) . '">' . $name . '</a>';
    }
}

if ($comment->isReported()) {
    $commentBody = '<div class="alert alert-warning"><p>' . Lang::txt('PLG_GROUPS_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p></div>';
} else {
    $commentBody = $comment->content();
}

$createdDatetime = $comment->created();
$createdTime = $comment->created('time');
$createdDate = $comment->created('date');
$isEditingThis = Request::getWord('action') == 'editcomment'
    && Request::getInt('comment') == $comment->get('id')
    && ($config->get('access-edit-comment') || User::get('id') == $comment->get('created_by'));
$maxDepth = $config->get('comments_depth', 3);
@endphp

<li class="comment {{ $cls }}" id="c{{ $comment->get('id') }}">
    <div class="chat chat-start">
        <div class="chat-image avatar">
            <div class="w-10 rounded-full">
                <a name="c{{ $comment->get('id') }}"></a>
                <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}" alt="" />
            </div>
        </div>
        <div class="chat-header flex flex-wrap items-center gap-2">
            <span>{!! $name !!}</span>
            <a class="link link-hover text-xs text-base-content/60"
                href="{{ Route::url($base . '#c' . $comment->get('id')) }}"
                title="{{ Lang::txt('PLG_GROUPS_BLOG_PERMALINK') }}">
                <time datetime="{{ $createdDatetime }}">{{ $createdDate }}</time>
                {{ Lang::txt('PLG_GROUPS_BLOG_AT') }}
                <time datetime="{{ $createdDatetime }}">{{ $createdTime }}</time>
                @if ($comment->wasModified())
                    @php
                    $modifiedDatetime = $comment->modified();
                    $modifiedTime = $comment->modified('time');
                    $modifiedDate = $comment->modified('date');
                    @endphp
                    &mdash; {{ Lang::txt('PLG_GROUPS_BLOG_EDITED') }}
                    <time datetime="{{ $modifiedDatetime }}">{{ $modifiedDate }}</time>
                    {{ Lang::txt('PLG_GROUPS_BLOG_AT') }}
                    <time datetime="{{ $modifiedDatetime }}">{{ $modifiedTime }}</time>
                @endif
            </a>
        </div>

        <div class="chat-bubble chat-bubble-neutral">
            @if ($isEditingThis)
                <form id="cform{{ $comment->get('id') }}"
                    class="comment-edit"
                    action="{{ Route::url($base) }}"
                    method="post"
                    enctype="multipart/form-data">
                    <fieldset>
                        <legend><span>{{ Lang::txt('PLG_GROUPS_BLOG_COMMENT_EDIT') }}</span></legend>

                        <input type="hidden" name="comment[id]" value="{{ $comment->get('id') }}" />
                        <input type="hidden" name="comment[entry_id]" value="{{ $comment->get('entry_id') }}" />
                        <input type="hidden" name="comment[parent]" value="{{ $comment->get('parent') }}" />
                        <input type="hidden" name="comment[created]" value="{{ $comment->get('created') }}" />
                        <input type="hidden" name="comment[created_by]" value="{{ $comment->get('created_by') }}" />
                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                        <input type="hidden" name="active" value="blog" />
                        <input type="hidden" name="task" value="view" />
                        <input type="hidden" name="action" value="savecomment" />

                        {!! Html::input('token') !!}

                        <div class="form-group">
                            <label for="comment_{{ $comment->get('id') }}_content">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_COMMENTS') }}</span>
                                {!! $__view->editor(
                                    'comment[content]',
                                    $comment->get('content'),
                                    35,
                                    4,
                                    'comment_' . $comment->get('id') . '_content',
                                    ['class' => 'form-control minimal no-footer']
                                ) !!}
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox"
                                    class="checkbox checkbox-sm"
                                    name="comment[anonymous]"
                                    id="comment_{{ $comment->get('id') }}_anonymous"
                                    value="1"
                                    @checked($comment->get('anonymous')) />
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS') }}</span>
                            </label>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ Lang::txt('PLG_GROUPS_BLOG_SUBMIT') }}
                            </button>
                        </div>
                    </fieldset>
                </form>
            @else
                {!! $commentBody !!}
            @endif
        </div>

        @if (!$isEditingThis)
            <div class="chat-footer flex flex-wrap gap-2 mt-1">
                @if ($config->get('access-delete-comment'))
                    @php
                    $deleteUrl = Route::url($base . '&action=deletecomment&comment=' . $comment->get('id'));
                    @endphp
                    <a class="link link-error text-xs"
                        data-confirm="{{ Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE') }}"
                        href="{{ $deleteUrl }}">{{ Lang::txt('PLG_GROUPS_BLOG_DELETE') }}</a>
                @endif

                @if (!$comment->isReported())
                    @php
                    $canEditComment = $config->get('access-edit-comment')
                        || User::get('id') == $comment->get('created_by');
                    @endphp
                    @if ($canEditComment)
                        @php
                        $editUrl = Route::url($base . '&action=editcomment&comment=' . $comment->get('id'));
                        @endphp
                        <a class="link link-hover text-xs" href="{{ $editUrl }}">
                            {{ Lang::txt('PLG_GROUPS_BLOG_EDIT') }}
                        </a>
                    @endif

                    @if ($depth < $maxDepth)
                        @if (Request::getInt('reply', 0) == $comment->get('id'))
                            <a class="link link-primary text-xs reply active"
                                data-txt-active="{{ Lang::txt('JCANCEL') }}"
                                data-txt-inactive="{{ Lang::txt('PLG_GROUPS_BLOG_REPLY') }}"
                                href="{{ Route::url($base) }}"
                                rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('JCANCEL') }}</a>
                        @else
                            <a class="link link-primary text-xs reply"
                                data-txt-active="{{ Lang::txt('JCANCEL') }}"
                                data-txt-inactive="{{ Lang::txt('PLG_GROUPS_BLOG_REPLY') }}"
                                href="{{ Route::url($base . '&reply=' . $comment->get('id')) }}"
                                rel="comment-form{{ $comment->get('id') }}">{{ Lang::txt('PLG_GROUPS_BLOG_REPLY') }}</a>
                        @endif
                    @endif

                    @php
                    $abuseUrl = Route::url(
                        'index.php?option=com_support&task=reportabuse'
                        . '&category=blog&id=' . $comment->get('id')
                        . '&parent=' . $comment->get('entry_id')
                    );
                    @endphp
                    <a class="link link-hover text-xs" href="{{ $abuseUrl }}">
                        {{ Lang::txt('PLG_GROUPS_BLOG_REPORT_ABUSE') }}
                    </a>
                @endif
            </div>

            @if ($depth < $maxDepth)
                <div class="addcomment comment-add @if(Request::getInt('reply', 0) != $comment->get('id')) hide @endif"
                    id="comment-form{{ $comment->get('id') }}">
                    <form id="cform{{ $comment->get('id') }}"
                        action="{{ Route::url($base) }}"
                        method="post"
                        enctype="multipart/form-data">
                        <fieldset id="commentform{{ $comment->get('id') }}">
                            @php
                            $replyToName = !$comment->get('anonymous')
                                ? $name
                                : Lang::txt('JANONYMOUS');
                            @endphp
                            <legend>
                                <span>{!! Lang::txt('PLG_GROUPS_BLOG_REPLYING_TO', $replyToName) !!}</span>
                            </legend>

                            <input type="hidden" name="comment[id]" value="0" />
                            <input type="hidden" name="comment[entry_id]" value="{{ $comment->get('entry_id') }}" />
                            <input type="hidden" name="comment[parent]" value="{{ $comment->get('id') }}" />
                            <input type="hidden" name="comment[created]" value="" />
                            <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                            <input type="hidden" name="comment[state]" value="1" />
                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
                            <input type="hidden" name="active" value="blog" />
                            <input type="hidden" name="task" value="view" />
                            <input type="hidden" name="action" value="savecomment" />

                            {!! Html::input('token') !!}

                            <div class="form-group">
                                <label for="comment_{{ $comment->get('id') }}_content">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_COMMENTS') }}</span>
                                    {!! $__view->editor(
                                        'comment[content]',
                                        '',
                                        35,
                                        4,
                                        'comment_' . $comment->get('id') . '_content',
                                        ['class' => 'form-control minimal no-footer']
                                    ) !!}
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="checkbox"
                                        class="checkbox checkbox-sm"
                                        name="comment[anonymous]"
                                        id="comment-anonymous"
                                        value="1" />
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS') }}</span>
                                </label>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ Lang::txt('PLG_GROUPS_BLOG_SUBMIT') }}
                                </button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            @endif
        @endif
    </div>

    @if ($depth < $maxDepth)
        @php
        $replies = $comment->replies()
            ->whereIn('state', [
                \Components\Blog\Models\Comment::STATE_PUBLISHED,
                \Components\Blog\Models\Comment::STATE_FLAGGED
            ])
            ->ordered()
            ->rows();

        $__view->view('_list', 'comments')
            ->set('group', $group)
            ->set('parent', $comment->get('id'))
            ->set('cls', $cls)
            ->set('depth', $depth)
            ->set('option', $option)
            ->set('comments', $replies)
            ->set('config', $config)
            ->set('base', $base)
            ->display();
        @endphp
    @endif
</li>
