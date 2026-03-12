{{--
  Single KB comment with recursive nested replies.

  Variables (passed via view('_comment')->set(...)):
    $comment   — Comment model instance
    $article   — Parent Article model
    $option    — Component option string
    $maxDepth  — Maximum nesting depth
    $depth     — Current depth (0 = top-level)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $name    = Lang::txt('JANONYMOUS');
  $nameUrl = '';
  if (!$comment->get('anonymous')) {
      $name = $comment->creator->get('name', $name);
      $viewLevels = User::getAuthorisedViewLevels();
      if (
          in_array($comment->creator->get('access'), $viewLevels)
          && !$comment->creator->get('block')
          && $comment->creator->get('approved')
      ) {
          $nameUrl = Route::url($comment->creator->link(), false);
      }
  }

  $isReported = $comment->isReported();
  $isAuthor   = $article->get('created_by') == $comment->get('created_by');
  $commentId  = $comment->get('id');

  // Comment body
  $commentBody = $isReported ? '' : $comment->content;

  // Voting
  $voteStr = '';
  if (!User::isGuest() && $comment->get('vote')) {
      if ($comment->get('vote') == 'like')    { $voteStr = 'like'; }
      if ($comment->get('vote') == 'dislike') { $voteStr = 'dislike'; }
  }

  $canVote = !User::isGuest()
      && User::get('id') != $comment->get('created_by')
      && !$voteStr;
  $likeUrl = $canVote
      ? Route::url($comment->link('vote') . '&vote=like&' . \Hubzero\Facades\Session::getFormToken() . '=1', false)
      : '';
  $dislikeUrl = $canVote
      ? Route::url($comment->link('vote') . '&vote=dislike&' . \Hubzero\Facades\Session::getFormToken() . '=1', false)
      : '';

  // Nested replies
  $replies = ($depth < $maxDepth)
      ? $comment->replies()
          ->whereIn('state', [
              \Components\Kb\Models\Comment::STATE_PUBLISHED,
              \Components\Kb\Models\Comment::STATE_FLAGGED,
          ])
          ->rows()
      : collect();

  // Reply form
  $replyToId     = Request::getInt('reply', 0);
  $showReplyForm = ($replyToId == $commentId) && ($depth < $maxDepth);

  $commentsOpen  = $article->commentsOpen();
  $permalinkUrl  = Route::url($article->link() . '#c' . $commentId, false);
  $depthClass    = $depth > 0 ? ' comment-reply' : '';
@endphp

<article class="comment{{ $depthClass }}{{ $isAuthor ? ' comment-author' : '' }}"
         id="c{{ $commentId }}">
  <div class="avatar">
    <div class="rounded-full">
      <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}"
           alt="" loading="lazy" />
    </div>
  </div>
  <div class="flex-1">
    <div class="comment-meta">
      <span class="font-medium">
        @if($nameUrl)
          <a class="link link-hover" href="{{ $nameUrl }}">{{ $name }}</a>
        @else
          {{ $name }}
        @endif
      </span>
      <a class="comment-time link link-hover"
         href="{{ $permalinkUrl }}"
         title="{{ Lang::txt('COM_KB_PERMALINK') }}">
        <time datetime="{{ $comment->created() }}">
          {{ $comment->created('date') }} {{ Lang::txt('COM_KB_DATETIME_AT') }} {{ $comment->created('time') }}
        </time>
      </a>
    </div>

    <div class="comment-body">
      @if($isReported)
        <p class="text-base-content/40 italic">
          {{ Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE') }}
        </p>
      @else
        {!! $commentBody !!}
      @endif
    </div>

    @if(!$isReported)
      <div class="comment-actions">
        <x-vote-widget
          :likes="$comment->get('helpful', 0)"
          :dislikes="$comment->get('nothelpful', 0)"
          :vote="$voteStr"
          :likeUrl="$likeUrl"
          :dislikeUrl="$dislikeUrl"
          :disabled="User::isGuest() || User::get('id') == $comment->get('created_by')"
        />
        @if($depth < $maxDepth && $commentsOpen)
          <a class="btn btn-xs btn-ghost"
             href="{{ Route::url($comment->link('reply'), false) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ Lang::txt('COM_KB_REPLY') }}
          </a>
        @endif
        @if(!$comment->get('reports'))
          <a class="btn btn-xs btn-ghost"
             href="{{ Route::url($comment->link('report'), false) }}"
             title="{{ Lang::txt('COM_KB_REPORT_ABUSE') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
            </svg>
            {{ Lang::txt('COM_KB_REPORT_ABUSE') }}
          </a>
        @endif
      </div>
    @endif

    {{-- Reply form --}}
    @if($showReplyForm && $commentsOpen)
      <div class="mt-4">
        @if(User::isGuest())
          @php
            $returnUrl = base64_encode(
                Route::url($article->link(), false, true)
            );
            $loginUrl = Route::url(
                'index.php?option=com_users&view=login&return=' . $returnUrl, false
            );
          @endphp
          <p class="text-base-content/60">
            {!! Lang::txt('COM_KB_MUST_LOG_IN', $loginUrl) !!}
          </p>
        @else
          <form action="{{ Route::url($article->link(), false) }}"
                method="post"
                class="comment-form"
                aria-label="{{ Lang::txt('COM_KB_REPLYING_TO', $name) }}">

            <blockquote class="border-l-4 border-base-300 pl-4 mb-4 text-sm text-base-content/70">
              <p class="font-medium">{{ $name }}</p>
              <p>{{ $comment->content('raw', 200) }}</p>
            </blockquote>

            <div class="comment-form-body">
              <label class="sr-only" for="comment_{{ $commentId }}_content">
                {{ Lang::txt('COM_KB_ENTER_COMMENTS') }}
              </label>
              <textarea class="comment-textarea"
                        id="comment_{{ $commentId }}_content"
                        name="comment[content]"
                        rows="3"
                        placeholder="{{ Lang::txt('COM_KB_ENTER_COMMENTS') }}"
                        required></textarea>
              <div class="comment-form-footer">
                <label class="comment-form-anon">
                  <input type="checkbox"
                         name="comment[anonymous]"
                         value="1" />
                  {{ Lang::txt('COM_KB_FIELD_ANONYMOUS') }}
                </label>
                <button class="btn btn-sm btn-primary" type="submit">
                  {{ Lang::txt('COM_KB_SUBMIT') }}
                </button>
              </div>
            </div>

            <input type="hidden" name="comment[id]" value="0" />
            <input type="hidden" name="comment[entry_id]"
                   value="{{ $article->get('id') }}" />
            <input type="hidden" name="comment[parent]"
                   value="{{ $commentId }}" />
            <input type="hidden" name="comment[created]" value="" />
            <input type="hidden" name="comment[created_by]"
                   value="{{ User::get('id') }}" />
            <input type="hidden" name="comment[state]" value="1" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="task" value="savecomment" />
            {!! Html::input('token') !!}
          </form>
        @endif
      </div>
    @endif

    {{-- Nested replies --}}
    @if($depth < $maxDepth && count($replies) > 0)
      <div class="comment-replies">
        @foreach($replies as $reply)
          {!! $__view->view('_comment')
                ->set('comment', $reply)
                ->set('article', $article)
                ->set('option', $option)
                ->set('maxDepth', $maxDepth)
                ->set('depth', $depth + 1)
                ->loadTemplate() !!}
        @endforeach
      </div>
    @endif
  </div>
</article>
