{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$cls = isset($cls) ? $cls : 'odd';

$name = Lang::txt('JANONYMOUS');
$nameHtml = e($name);
if (!$comment->get('anonymous')) {
    $name = e(stripslashes($comment->creator->get('name', $name)));
    if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
        $nameHtml = '<a class="link link-hover" href="' . Route::url($comment->creator->link()) . '">' . $name . '</a>';
    } else {
        $nameHtml = $name;
    }
}

$isReported = $comment->isReported();
$commentContent = $isReported
    ? '<div class="alert alert-warning text-sm">' . Lang::txt('PLG_MEMBERS_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</div>'
    : $comment->content;

$commentId = $comment->get('id');
$canEdit = $config->get('access-edit-comment') || User::get('id') == $comment->get('created_by');
$canDelete = $config->get('access-delete-comment');

$deleteUrl = Route::url($base . '&action=deletecomment&comment=' . $commentId);
$editUrl = Route::url($base . '&action=editcomment&comment=' . $commentId);
$abuseUrl = Route::url(
    'index.php?option=com_support&task=reportabuse&category=blog&id=' . $commentId
    . '&parent=' . $comment->get('entry_id')
);
$isEditing = (Request::getWord('action') == 'editcomment'
    && Request::getInt('comment') == $commentId
    && $canEdit);
$isReplying = (Request::getInt('reply', 0) == $commentId);
@endphp

<li class="card bg-base-100 shadow-sm" id="c{{ $commentId }}">
  <div class="card-body p-4">
    <div class="flex gap-3">
      <div class="shrink-0">
        <img class="rounded-full w-8 h-8"
             src="{{ $comment->creator->picture($comment->get('anonymous')) }}"
             alt="" />
      </div>
      <div class="flex-1 min-w-0">
        {{-- Header --}}
        <p class="text-sm">
          <strong>{!! $nameHtml !!}</strong>
          <a class="text-base-content/50 hover:text-base-content/70 ml-1"
             href="{{ Route::url($base . '#c' . $commentId) }}">
            <span>{{ Lang::txt('PLG_MEMBERS_BLOG_AT') }}</span>
            <time datetime="{{ $comment->created() }}">{{ $comment->created('time') }}</time>
            <span>{{ Lang::txt('PLG_MEMBERS_BLOG_ON') }}</span>
            <time datetime="{{ $comment->created() }}">{{ $comment->created('date') }}</time>
            @if ($comment->wasModified())
              &mdash; {{ Lang::txt('PLG_MEMBERS_BLOG_EDITED') }}
              <span>{{ Lang::txt('PLG_MEMBERS_BLOG_AT') }}</span>
              <time datetime="{{ $comment->modified() }}">{{ $comment->modified('time') }}</time>
              <span>{{ Lang::txt('PLG_MEMBERS_BLOG_ON') }}</span>
              <time datetime="{{ $comment->modified() }}">{{ $comment->modified('date') }}</time>
            @endif
          </a>
        </p>

        {{-- Edit form or content --}}
        @if ($isEditing)
          <form id="cform{{ $commentId }}"
                action="{{ Route::url($base) }}"
                method="post"
                enctype="multipart/form-data"
                class="mt-2 space-y-3">
            <input type="hidden" name="comment[id]" value="{{ $commentId }}" />
            <input type="hidden" name="comment[entry_id]" value="{{ $comment->get('entry_id') }}" />
            <input type="hidden" name="comment[parent]" value="{{ $comment->get('parent') }}" />
            <input type="hidden" name="comment[created]" value="{{ $comment->get('created') }}" />
            <input type="hidden" name="comment[created_by]" value="{{ $comment->get('created_by') }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="id" value="{{ $member->get('id') }}" />
            <input type="hidden" name="active" value="blog" />
            <input type="hidden" name="task" value="view" />
            <input type="hidden" name="action" value="savecomment" />
            {!! Html::input('token') !!}

            <label for="comment_{{ $commentId }}_content" class="sr-only">
              {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS') }}
            </label>
            {!! $__view->editor('comment[content]', $comment->get('content'), 35, 4, 'comment_' . $commentId . '_content', ['class' => 'minimal no-footer']) !!}

            <label class="label cursor-pointer justify-start gap-2">
              <input type="checkbox" class="checkbox checkbox-sm"
                     name="comment[anonymous]"
                     id="comment_{{ $commentId }}_anonymous"
                     value="1" {{ $comment->get('anonymous') ? 'checked' : '' }} />
              <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS') }}</span>
            </label>

            <button type="submit" class="btn btn-primary btn-sm">
              {{ Lang::txt('PLG_MEMBERS_BLOG_SUBMIT') }}
            </button>
          </form>
        @else
          <div class="prose prose-sm max-w-none mt-1">
            {!! $commentContent !!}
          </div>

          @if (!$isReported)
            <div class="flex flex-wrap gap-2 mt-2">
              @if ($canDelete)
                <a class="btn btn-ghost btn-xs text-error"
                   data-confirm="{{ Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE') }}"
                   href="{{ $deleteUrl }}">
                  {{ Lang::txt('PLG_MEMBERS_BLOG_DELETE') }}
                </a>
              @endif
              @if ($canEdit)
                <a class="btn btn-ghost btn-xs" href="{{ $editUrl }}">
                  {{ Lang::txt('PLG_MEMBERS_BLOG_EDIT') }}
                </a>
              @endif
              @if ($depth < $config->get('comments_depth', 3))
                @if ($isReplying)
                  <a class="btn btn-ghost btn-xs"
                     href="{{ Route::url($base) }}">
                    {{ Lang::txt('JCANCEL') }}
                  </a>
                @else
                  <a class="btn btn-ghost btn-xs"
                     href="{{ Route::url($base . '&reply=' . $commentId) }}">
                    {{ Lang::txt('PLG_MEMBERS_BLOG_REPLY') }}
                  </a>
                @endif
              @endif
              <a class="btn btn-ghost btn-xs" href="{{ $abuseUrl }}">
                {{ Lang::txt('PLG_MEMBERS_BLOG_REPORT_ABUSE') }}
              </a>
            </div>
          @endif

          {{-- Reply form --}}
          @if ($depth < $config->get('comments_depth', 3) && $isReplying)
            <div class="mt-3" id="comment-form{{ $commentId }}">
              <form id="cform{{ $commentId }}"
                    action="{{ Route::url($base) }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="space-y-3">
                <input type="hidden" name="comment[id]" value="0" />
                <input type="hidden" name="comment[entry_id]" value="{{ $comment->get('entry_id') }}" />
                <input type="hidden" name="comment[parent]" value="{{ $commentId }}" />
                <input type="hidden" name="comment[created]" value="" />
                <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                <input type="hidden" name="comment[state]" value="1" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="id" value="{{ $member->get('id') }}" />
                <input type="hidden" name="active" value="blog" />
                <input type="hidden" name="task" value="view" />
                <input type="hidden" name="action" value="savecomment" />

                <label for="comment_{{ $commentId }}_content" class="sr-only">
                  {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS') }}
                </label>
                {!! $__view->editor('comment[content]', '', 35, 4, 'comment_' . $commentId . '_content', ['class' => 'minimal no-footer']) !!}

                <label class="label cursor-pointer justify-start gap-2">
                  <input type="checkbox" class="checkbox checkbox-sm"
                         name="comment[anonymous]"
                         id="comment_{{ $commentId }}_anonymous"
                         value="1" />
                  <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS') }}</span>
                </label>

                {!! Html::input('token') !!}

                <button type="submit" class="btn btn-primary btn-sm">
                  {{ Lang::txt('PLG_MEMBERS_BLOG_SUBMIT') }}
                </button>
              </form>
            </div>
          @endif
        @endif
      </div>
    </div>

    {{-- Nested replies --}}
    @if ($depth < $config->get('comments_depth', 3))
      @php
        $replies = $comment->replies()
            ->including(['creator', function ($creator) {
                $creator->select('*');
            }])
            ->whereIn('state', [
                \Components\Blog\Models\Comment::STATE_PUBLISHED,
                \Components\Blog\Models\Comment::STATE_FLAGGED,
            ])
            ->ordered()
            ->rows();
      @endphp
      @if ($replies->count())
        @php
          $__view->view('_list')
              ->set('parent', $commentId)
              ->set('option', $option)
              ->set('comments', $replies)
              ->set('config', $config)
              ->set('depth', $depth)
              ->set('cls', $cls)
              ->set('base', $base)
              ->set('member', $member)
              ->display();
        @endphp
      @endif
    @endif
  </div>
</li>
