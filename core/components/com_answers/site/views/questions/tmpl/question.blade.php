{{--
  Single question detail — full question with responses, comments, voting.

  Variables from controller (questionTask):
    $question — Question model instance
    $config   — Component params (Registry)

  Also used via answerTask (task='answer') and deleteTask (task='delete').

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt(strtoupper($option)),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      \Hubzero\Utility\Str::truncate(strip_tags($question->get('subject')), 50),
      $question->link()
  );

  Document::setTitle(
      Lang::txt('COM_ANSWERS') . ': ' . strip_tags($question->get('subject'))
  );

  $qNotFound = $question->isDeleted() || !$question->get('id');

  // Author info
  $authorName = Lang::txt('JANONYMOUS');
  $authorUrl  = '';
  if (!$question->get('anonymous')) {
      $authorName = $question->creator->get('name', Lang::txt('JUNKNOWN'));
      $viewLevels = User::getAuthorisedViewLevels();
      if (
          in_array($question->creator->get('access'), $viewLevels)
          && !$question->creator->get('block')
          && $question->creator->get('approved')
      ) {
          $authorUrl = Route::url($question->creator->link(), false);
      }
  }

  // Permissions
  $canDelete = (
      $question->get('created_by') == User::get('id')
      && User::authorise('core.delete', $option)
  ) || User::authorise('core.manage', $option);

  // Status
  $status = 'open';
  if ($question->isReported()) {
      $status = 'underreview';
  } elseif (!$question->isOpen()) {
      $status = 'closed';
  }

  // Responses
  $chosen = $question->chosen()
      ->including(['creator', function ($creator) { $creator->select('*'); }])
      ->rows();

  $responses = $question->responses()
      ->including(['creator', function ($creator) { $creator->select('*'); }])
      ->whereIn('state', [0, 3])
      ->rows();

  $maxDepth = $config->get('comments_depth', 3);

  // Vote data for question
  $ballot  = $question->ballot();
  $voteVal = $ballot->get('vote', null);
  $voteStr = '';
  if ($voteVal == 1)  { $voteStr = 'like'; }
  if ($voteVal == -1) { $voteStr = 'dislike'; }

  $canVoteQ = !User::isGuest()
      && User::get('id') != $question->get('created_by')
      && !$voteVal;
  $qLikeUrl = $canVoteQ
      ? Route::url('index.php?option=' . $option . '&task=vote&category=question&id=' . $question->get('id') . '&vote=yes', false)
      : '';
  $qDislikeUrl = $canVoteQ
      ? Route::url('index.php?option=' . $option . '&task=vote&category=question&id=' . $question->get('id') . '&vote=no', false)
      : '';

  // Tags
  $tagCloud = $question->tags('cloud', 0);

  // Question body
  $questionBody = '';
  if ($question->get('question')) {
      $questionBody = htmlspecialchars_decode($question->question);
      $componentPath = Component::path('com_redirect');
      if ($componentPath) {
          $questionBody = \Components\Redirect\Helpers\Converter::convert($questionBody);
      }
  }
@endphp

<x-page-container :title="Lang::txt('COM_ANSWERS')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option . '&task=search', false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
      </svg>
      {{ Lang::txt('COM_ANSWERS_ALL_QUESTIONS') }}
    </a>
  @endslot

  @if($qNotFound)
    {{-- Question not found --}}
    <x-empty-state
      :title="Lang::txt('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND')"
      :message="Lang::txt('COM_ANSWERS_NOTICE_QUESTION_REMOVED')"
    >
      <a class="btn btn-primary"
         href="{{ Route::url('index.php?option=' . $option . '&task=search', false) }}">
        {{ Lang::txt('COM_ANSWERS_ALL_QUESTIONS') }}
      </a>
    </x-empty-state>
  @else
    @slot('sidebar')
      {{-- Status --}}
      <x-sidebar-card :title="Lang::txt('COM_ANSWERS_STATUS')">
          @if($status == 'open')
            <span class="badge badge-success">
              {{ Lang::txt('COM_ANSWERS_STATUS_ACCEPTING_ANSWERS') }}
            </span>
          @elseif($status == 'underreview')
            <span class="badge badge-warning">
              {{ Lang::txt('COM_ANSWERS_STATUS_UNDER_REVIEW') }}
            </span>
          @else
            <span class="badge badge-ghost">
              {{ Lang::txt('COM_ANSWERS_STATUS_CLOSED') }}
            </span>
          @endif

          {{-- Resource/publication link from tags --}}
          @php
            $tags = $question->tags('array', 1);
            $linkedResource = null;
            foreach ($tags as $tag) {
                if (!$tag) { continue; }
                if (!is_object($tag)) {
                    $tag = new \Components\Tags\Models\Tag($tag);
                    if (!$tag->get('id')) { continue; }
                }
                if (preg_match('/^tool:/i', $tag->get('raw_tag'))) {
                    $linkedResource = Route::url(
                        'index.php?option=com_resources&alias='
                        . substr($tag->get('raw_tag'), strlen('tool:')), false
                    );
                    break;
                }
                if (preg_match('/^resource(\d+)$/i', $tag->get('tag'))) {
                    $linkedResource = Route::url(
                        'index.php?option=com_resources&id='
                        . substr($tag->get('tag'), strlen('resource')), false
                    );
                    break;
                }
                if (preg_match('/^publication(\d+)$/i', $tag->get('tag'))) {
                    $linkedResource = Route::url(
                        'index.php?option=com_publications&id='
                        . substr($tag->get('tag'), strlen('publication')), false
                    );
                    break;
                }
            }
          @endphp
          @if($linkedResource)
            <p class="text-sm mt-2">
              {!! Lang::txt(
                  'COM_ANSWERS_QUESTION_ASKED_ON',
                  '<a class="link" href="' . $linkedResource . '">'
                      . Lang::txt('COM_ANSWERS_FOLLOWING_RESOURCE') . '</a>'
              ) !!}
            </p>
          @endif
      </x-sidebar-card>

      {{-- Bonus points --}}
      @if($question->isOpen() && $config->get('banking') && $question->reward())
        <x-stat-card
          :label="Lang::txt('COM_ANSWERS_BONUS')"
          :value="Lang::txt('COM_ANSWERS_NUMBER_POINTS', $question->reward())"
        />
      @endif

      {{-- Max award --}}
      @if($question->isOpen() && $config->get('banking') && $question->get('maxaward'))
        <x-stat-card
          :label="Lang::txt('COM_ANSWERS_BEST_ANSWER_MAY_EARN')"
          :value="$question->get('maxaward')"
        />
      @endif

      {{-- Answer button --}}
      @if($question->isOpen() && $task != 'answer' && !$question->isReported())
        @php
          $answerRoute = Route::url($question->link('answer'), false, true);
          $answerUrl = User::isGuest()
              ? Route::url('index.php?option=com_users&view=login&return=' . base64_encode($answerRoute), false)
              : $answerRoute;
        @endphp
        <a class="btn btn-primary w-full"
           href="{{ $answerUrl }}">
          {{ Lang::txt('COM_ANSWERS_ANSWER_THIS') }}
        </a>
      @endif

      {{-- Reminder for question owner --}}
      @if(User::get('id') == $question->get('created_by') && $question->isOpen())
        <div class="alert alert-info text-sm">
          {{ Lang::txt('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE') }}
          @if($config->get('banking'))
            {{ Lang::txt('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE_POINTS') }}
          @endif
        </div>
      @endif
    @endslot

    {{-- Question --}}
    <article id="q{{ $question->get('id') }}" class="mb-8">
      <header class="mb-4">
        <h2 class="text-2xl font-bold mb-2">
          {{ $question->get('subject') }}
        </h2>
        <div class="entry-meta">
          <div class="flex items-center gap-3">
            <div class="avatar">
              <div class="w-8 rounded-full">
                <img src="{{ $question->creator->picture($question->get('anonymous')) }}"
                     alt="" loading="lazy" />
              </div>
            </div>
            <span class="font-medium">
              @if($authorUrl)
                <a class="link link-hover" href="{{ $authorUrl }}">{{ $authorName }}</a>
              @else
                {{ $authorName }}
              @endif
            </span>
            <a class="link link-hover text-base-content/50 text-sm"
               href="{{ Route::url($question->link(), false) }}"
               title="{{ Lang::txt('COM_ANSWERS_PERMALINK') }}">
              <time datetime="{{ $question->created() }}">
                {{ $question->created('date') }} {{ Lang::txt('COM_ANSWERS_DATETIME_AT') }} {{ $question->created('time') }}
              </time>
            </a>
          </div>
          @if(!$question->isReported())
            <span class="entry-actions">
              @if(!$question->isReported())
                <x-vote-widget
                  :likes="$question->get('helpful', 0)"
                  :dislikes="$question->get('nothelpful', 0)"
                  :vote="$voteStr"
                  :likeUrl="$qLikeUrl"
                  :dislikeUrl="$qDislikeUrl"
                  :disabled="User::isGuest() || User::get('id') == $question->get('created_by')"
                />
              @endif
              <a class="btn btn-xs btn-ghost"
                 href="{{ Route::url($question->link('report'), false) }}"
                 title="{{ Lang::txt('COM_ANSWERS_TITLE_REPORT_ABUSE') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                </svg>
                {{ Lang::txt('COM_ANSWERS_REPORT_ABUSE') }}
              </a>
              @if($canDelete)
                <a class="btn btn-xs btn-ghost text-error"
                   href="{{ Route::url($question->link('delete'), false) }}"
                   title="{{ Lang::txt('COM_ANSWERS_DELETE_QUESTION') }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                  {{ Lang::txt('COM_ANSWERS_DELETE') }}
                </a>
              @endif
            </span>
          @endif
        </div>
      </header>

      @if($question->isReported())
        <div class="alert alert-warning">
          {{ Lang::txt('COM_ANSWERS_NOTICE_QUESTION_REPORTED') }}
        </div>
      @else
        {{-- Question body --}}
        @if($questionBody)
          <div class="prose max-w-none mb-4">
            {!! $questionBody !!}
          </div>
        @endif

        {{-- Tags --}}
        @if($tagCloud)
          <footer class="entry-footer">
            <nav aria-label="Tags">
              {!! $tagCloud !!}
            </nav>
          </footer>
        @endif
      @endif
    </article>

    {{-- Delete confirmation --}}
    @if($task == 'delete' && $question->isOpen() && !$question->isReported())
      <x-confirm-dialog
        :title="Lang::txt('COM_ANSWERS_DELETE_QUESTION')"
        :description="Lang::txt('COM_ANSWERS_NOTICE_CONFIRM_DELETE')"
        :action="Route::url('index.php?option=' . $option . '&task=deleteq&id=' . $question->get('id'), false)"
        :confirmLabel="Lang::txt('COM_ANSWERS_YES_DELETE')"
        :cancelUrl="Route::url($question->link(), false)"
        :cancelLabel="Lang::txt('COM_ANSWERS_NO_DELETE')"
      >
        @slot('hiddenFields')
          <input type="hidden" name="qid" value="{{ $question->get('id') }}" />
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="questions" />
          <input type="hidden" name="task" value="deleteq" />
          {!! Html::input('token') !!}
        @endslot
      </x-confirm-dialog>
    @endif

    {{-- Reward breakdown --}}
    @if($question->isOpen() && $config->get('banking') && $question->get('reward'))
      <x-sidebar-card :title="Lang::txt('COM_ANSWERS_POINTS_BREAKDOWN')" class="mb-8">
          <div class="overflow-x-auto">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>{{ Lang::txt('COM_ANSWERS_POINTS_BREAKDOWN') }}</th>
                  <th>{{ Lang::txt('COM_ANSWERS_POINTS') }}</th>
                  <th>{{ Lang::txt('COM_ANSWERS_DETAILS') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ Lang::txt('COM_ANSWERS_ACTIVITY') }}*</td>
                  <td>{{ $question->reward('marketvalue') }}</td>
                  <td></td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_ANSWERS_BONUS') }}</td>
                  <td>{{ $question->reward() }}</td>
                  <td></td>
                </tr>
                <tr class="font-semibold">
                  <td>{{ Lang::txt('COM_ANSWERS_TOTAL_MARKET_VALUE') }}</td>
                  <td>{{ $question->reward('totalmarketvalue') }}</td>
                  <td>{{ Lang::txt('COM_ANSWERS_TOTAL') }}</td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_ANSWERS_ASKER_WILL_EARN') }}</td>
                  <td>{{ $question->reward('asker_earnings') }}</td>
                  <td>{{ Lang::txt('COM_ANSWERS_ONE_THIRD_OF_ACTIVITY_POINTS') }}</td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_ANSWERS_BEST_ANSWER_MAY_EARN') }}</td>
                  <td>{{ $question->reward('answer_earnings') }}</td>
                  <td>{{ Lang::txt('COM_ANSWERS_UP_TO_TWO_THIRDS_OF_ACTIVITY_POINTS') }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-xs text-base-content/50">
                    * {{ Lang::txt('COM_ANSWERS_ACTIVITY_POINTS_EXPLANATION') }}
                    <a class="link" href="{{ $config->get('infolink') }}">
                      {{ Lang::txt('COM_ANSWERS_READ_FURTHER_DETAILS') }}
                    </a>.
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
      </x-sidebar-card>
    @endif

    {{-- Answer form (task=answer) --}}
    @if(!$question->isReported() && $task == 'answer')
      <section class="mb-8" id="answer-form">
        <h3 class="text-xl font-bold mb-4">
          {{ Lang::txt('COM_ANSWERS_YOUR_ANSWER') }}
        </h3>

        @if(!User::isGuest())
          <form action="{{ Route::url('index.php?option=' . $option, false) }}"
                method="post"
                id="commentform"
                class="comment-form"
                aria-label="{{ Lang::txt('COM_ANSWERS_YOUR_ANSWER') }}">

            <div class="comment-form-body">
              <label class="sr-only" for="responseanswer">
                {{ Lang::txt('COM_ANSWERS_YOUR_RESPONSE') }}
              </label>
              <textarea class="comment-textarea"
                        id="responseanswer"
                        name="response[answer]"
                        rows="6"
                        placeholder="{{ Lang::txt('COM_ANSWERS_YOUR_RESPONSE') }}"
                        required></textarea>
              <div class="comment-form-footer">
                <label class="comment-form-anon">
                  <input type="checkbox"
                         name="response[anonymous]"
                         value="1" />
                  {{ Lang::txt('COM_ANSWERS_POST_ANON') }}
                </label>
                <button class="btn btn-sm btn-primary" type="submit">
                  {{ Lang::txt('COM_ANSWERS_SUBMIT') }}
                </button>
              </div>
            </div>

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="questions" />
            <input type="hidden" name="task" value="savea" />
            <input type="hidden" name="response[id]" value="0" />
            <input type="hidden" name="response[question_id]" value="{{ $question->get('id') }}" />
            {!! Html::input('token') !!}
          </form>
        @else
          @php
            $returnUrl = base64_encode(
                Route::url($question->link('answer'), false, true)
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
        @endif
      </section>
    @endif

    {{-- Chosen answer --}}
    @if($chosen->count())
      <section id="bestanswer" class="entry-comments mb-8"
               aria-label="{{ Lang::txt('COM_ANSWERS_CHOSEN_ANSWER') }}">
        <h3 class="text-xl font-bold mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-5 inline text-success" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          {{ Lang::txt('COM_ANSWERS_CHOSEN_ANSWER') }}
        </h3>
        <div class="comment-list">
          @foreach($chosen as $response)
            {!! $__view->view('_response')
                  ->set('response', $response)
                  ->set('question', $question)
                  ->set('option', $option)
                  ->set('config', $config)
                  ->set('maxDepth', $maxDepth)
                  ->set('isChosen', true)
                  ->loadTemplate() !!}
          @endforeach
        </div>
      </section>
    @endif

    {{-- All responses --}}
    <section id="answers" class="entry-comments"
             aria-label="{{ Lang::txt('COM_ANSWERS_RESPONSES') }}">
      <h3 class="text-xl font-bold mb-4">
        {{ Lang::txt('COM_ANSWERS_RESPONSES') }} ({{ $responses->count() }})
      </h3>

      @if($responses->count())
        <div class="comment-list">
          @foreach($responses as $response)
            {!! $__view->view('_response')
                  ->set('response', $response)
                  ->set('question', $question)
                  ->set('option', $option)
                  ->set('config', $config)
                  ->set('maxDepth', $maxDepth)
                  ->set('isChosen', false)
                  ->loadTemplate() !!}
          @endforeach
        </div>
      @elseif($chosen->count())
        <p class="text-base-content/60">{{ Lang::txt('COM_ANSWERS_NO_OTHER_RESPONSES') }}</p>
      @else
        <p class="text-base-content/60 mb-2">
          {{ Lang::txt('COM_ANSWERS_NO_ANSWERS_BE_FIRST') }}
          <a class="link link-primary"
             href="{{ Route::url($question->link('answer'), false) }}">
            {{ Lang::txt('COM_ANSWERS_BE_FIRST_ANSWER_THIS') }}
          </a>.
        </p>
        @if($config->get('banking'))
          <p class="text-sm text-base-content/50">
            {{ Lang::txt('COM_ANSWERS_DID_YOU_KNOW_ABOUT_POINTS') }}
            <a class="link" href="{{ $config->get('infolink') }}">
              {{ Lang::txt('COM_ANSWERS_LEARN_MORE') }}
            </a>
            {{ Lang::txt('COM_ANSWERS_LEARN_HOW_POINTS_AWARDED') }}.
          </p>
        @endif
      @endif
    </section>
  @endif

</x-page-container>
