{{--
  Single response (answer) with voting, comments, and reply form.
  Rendered by question.blade.php for each response/answer.

  Variables (passed via view('_response')->set(...)):
    $response  — Response model instance
    $question  — Parent Question model
    $option    — Component option string
    $config    — Component params (Registry)
    $maxDepth  — Maximum comment nesting depth
    $isChosen  — Whether this is the accepted answer

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Author info
  $name    = Lang::txt('JANONYMOUS');
  $nameUrl = '';
  if (!$response->get('anonymous')) {
      $name = $response->creator->get('name', $name);
      $viewLevels = User::getAuthorisedViewLevels();
      if (
          in_array($response->creator->get('access'), $viewLevels)
          && !$response->creator->get('block')
          && $response->creator->get('approved')
      ) {
          $nameUrl = Route::url($response->creator->link(), false);
      }
  }

  $isReported = $response->isReported();
  $isAuthor   = $question->get('created_by') == $response->get('created_by');
  $responseId = $response->get('id');

  // Response body
  $responseBody = htmlspecialchars_decode($response->content);
  $componentPath = Component::path('com_redirect');
  if ($componentPath) {
      $responseBody = \Components\Redirect\Helpers\Converter::convert($responseBody);
  }

  // Voting
  $ballot  = $response->ballot();
  $voteVal = $ballot->get('vote', null);
  $voteStr = '';
  if ($voteVal == 1)  { $voteStr = 'like'; }
  if ($voteVal == -1) { $voteStr = 'dislike'; }

  $canVote = !User::isGuest()
      && User::get('id') != $response->get('created_by')
      && !$voteVal;
  $likeUrl = $canVote
      ? Route::url('index.php?option=' . $option . '&task=vote&category=response&id=' . $responseId . '&vote=yes', false)
      : '';
  $dislikeUrl = $canVote
      ? Route::url('index.php?option=' . $option . '&task=vote&category=response&id=' . $responseId . '&vote=no', false)
      : '';

  // Accept answer
  $canAccept = User::get('id') == $question->get('created_by')
      && $question->isOpen()
      && !$isChosen;

  // Comments on this response
  $comments = $response->replies()
      ->where('state', '!=', 2)
      ->rows();

  // Reply handling
  $replyToId = Request::getInt('reply', 0);
  $showReplyForm = ($replyToId == $responseId);

  $permalinkUrl = Route::url($question->link() . '#a' . $responseId, false);
@endphp

<article class="comment {{ $isChosen ? 'comment-chosen' : '' }} {{ $isAuthor ? 'comment-author' : '' }}"
         id="a{{ $responseId }}">
  <div class="avatar">
    <div class="rounded-full">
      <img src="{{ $response->creator->picture($response->get('anonymous')) }}"
           alt="" loading="lazy" />
    </div>
  </div>
  <div class="flex-1">
    {{-- Meta --}}
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
         title="{{ Lang::txt('COM_ANSWERS_PERMALINK') }}">
        <time datetime="{{ $response->created() }}">
          {{ $response->created('date') }} {{ Lang::txt('COM_ANSWERS_DATETIME_AT') }} {{ $response->created('time') }}
        </time>
      </a>
      @if($isChosen)
        <span class="badge badge-success badge-sm gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="2" stroke="currentColor" class="size-3" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
          {{ Lang::txt('COM_ANSWERS_CHOSEN_ANSWER') }}
        </span>
      @endif
    </div>

    {{-- Body --}}
    <div class="comment-body">
      @if($isReported)
        <p class="text-base-content/40 italic">
          {{ Lang::txt('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE') }}
        </p>
      @else
        {!! $responseBody !!}
      @endif
    </div>

    {{-- Actions --}}
    @if(!$isReported)
      <div class="comment-actions">
        <x-vote-widget
          :likes="$response->get('helpful', 0)"
          :dislikes="$response->get('nothelpful', 0)"
          :vote="$voteStr"
          :likeUrl="$likeUrl"
          :dislikeUrl="$dislikeUrl"
          :disabled="User::isGuest() || User::get('id') == $response->get('created_by')"
        />
        @if($maxDepth > 0)
          <a class="btn btn-xs btn-ghost"
             href="{{ Route::url($question->link() . '&reply=' . $responseId, false) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            {{ Lang::txt('COM_ANSWERS_REPLY') }}
          </a>
        @endif
        <a class="btn btn-xs btn-ghost"
           href="{{ Route::url($response->link('report'), false) }}"
           title="{{ Lang::txt('COM_ANSWERS_TITLE_REPORT_ABUSE') }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
          </svg>
          {{ Lang::txt('COM_ANSWERS_REPORT_ABUSE') }}
        </a>
        @if($canAccept)
          <a class="btn btn-xs btn-ghost text-success"
             href="{{ Route::url($response->link('accept'), false) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ Lang::txt('COM_ANSWERS_ACCEPT_ANSWER') }}
          </a>
        @endif
      </div>
    @endif

    {{-- Reply form --}}
    @if($showReplyForm && $maxDepth > 0)
      <div class="mt-4">
        @if(User::isGuest())
          @php
            $returnUrl = base64_encode(
                Route::url($question->link(), false, true)
            );
            $loginUrl = Route::url(
                'index.php?option=com_users&view=login&return=' . $returnUrl, false
            );
          @endphp
          <p class="login-to-comment">
            {!! Lang::txt(
                'COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER',
                '<a href="' . $loginUrl . '">' . Lang::txt('COM_ANSWERS_LOGIN') . '</a>'
            ) !!}
          </p>
        @else
          <form action="{{ Route::url($question->link(), false) }}"
                method="post"
                class="comment-form"
                aria-label="{{ Lang::txt('COM_ANSWERS_REPLYING_TO', $name) }}">

            <blockquote class="border-l-4 border-base-300 pl-4 mb-4 text-sm text-base-content/70">
              <p class="font-medium">{{ $name }}</p>
              <p>{{ \Hubzero\Utility\Str::truncate(strip_tags($response->get('answer')), 200) }}</p>
            </blockquote>

            <div class="comment-form-body">
              <label class="sr-only" for="comment_{{ $responseId }}_content">
                {{ Lang::txt('COM_ANSWERS_ENTER_COMMENTS') }}
              </label>
              <textarea class="comment-textarea"
                        id="comment_{{ $responseId }}_content"
                        name="comment[content]"
                        rows="4"
                        placeholder="{{ Lang::txt('COM_ANSWERS_ENTER_COMMENTS') }}"
                        required></textarea>
              <div class="comment-form-footer">
                <label class="comment-form-anon">
                  <input type="checkbox"
                         name="comment[anonymous]"
                         value="1" />
                  {{ Lang::txt('COM_ANSWERS_POST_COMMENT_ANONYMOUSLY') }}
                </label>
                <button class="btn btn-sm btn-primary" type="submit">
                  {{ Lang::txt('COM_ANSWERS_SUBMIT') }}
                </button>
              </div>
            </div>

            <input type="hidden" name="comment[id]" value="0" />
            <input type="hidden" name="comment[item_type]" value="response" />
            <input type="hidden" name="comment[item_id]" value="{{ $responseId }}" />
            <input type="hidden" name="comment[parent]" value="0" />
            <input type="hidden" name="comment[created]" value="" />
            <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
            <input type="hidden" name="comment[state]" value="1" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="questions" />
            <input type="hidden" name="rid" value="{{ $question->get('id') }}" />
            <input type="hidden" name="task" value="savereply" />
            {!! Html::input('token') !!}
          </form>
        @endif
      </div>
    @endif

    {{-- Nested comments --}}
    @if($comments->count() > 0)
      <div class="comment-replies">
        @foreach($comments as $comment)
          {!! $__view->view('_reply')
                ->set('comment', $comment)
                ->set('question', $question)
                ->set('response', $response)
                ->set('option', $option)
                ->set('config', $config)
                ->set('maxDepth', $maxDepth)
                ->set('depth', 1)
                ->loadTemplate() !!}
        @endforeach
      </div>
    @endif
  </div>
</article>
