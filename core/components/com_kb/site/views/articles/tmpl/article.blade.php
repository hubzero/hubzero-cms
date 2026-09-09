{{--
  Knowledge Base — single article with comments.

  Variables from controller (articleTask):
    $article   — Article model
    $category  — Category object
    $archive   — Archive model
    $vote      — Current user's vote string ('like', 'dislike', or '')
    $catid     — Active parent category ID

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_KB'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      $category->get('title'),
      $category->link()
  );
  Pathway::append(
      $article->get('title'),
      $article->link()
  );

  Document::setTitle(
      Lang::txt('COM_KB') . ': '
      . $category->get('title') . ': '
      . $article->get('title')
  );

  $viewLevels = User::getAuthorisedViewLevels();
  $allUrl = Route::url('index.php?option=' . $option . '&section=all', false);

  // Category sidebar data
  $catFilters = [
      'state'  => 1,
      'access' => $viewLevels,
  ];
  $categories = $archive->categories($catFilters);

  // Vote data
  $voteStr = '';
  if ($vote == 'like' || $vote == 'yes' || $vote == 'positive' || $vote == 1) {
      $voteStr = 'like';
  }
  if ($vote == 'dislike' || $vote == 'no' || $vote == 'negative' || $vote == -1) {
      $voteStr = 'dislike';
  }

  $canVote = !User::isGuest() && !$voteStr;
  $likeUrl = $canVote
      ? Route::url($article->link('vote') . '&vote=like&' . \Hubzero\Facades\Session::getFormToken() . '=1', false)
      : '';
  $dislikeUrl = $canVote
      ? Route::url($article->link('vote') . '&vote=dislike&' . \Hubzero\Facades\Session::getFormToken() . '=1', false)
      : '';

  // Comments
  $comments = $article->comments()
      ->whereIn('state', [
          \Components\Kb\Models\Comment::STATE_PUBLISHED,
          \Components\Kb\Models\Comment::STATE_FLAGGED,
      ])
      ->rows();

  $maxDepth = $article->param('comments_depth', 3);
  $commentsOpen = $article->commentsOpen();

  // Reply-to handling
  $replyto = \Components\Kb\Models\Comment::oneOrNew(
      Request::getInt('replyto', 0)
  );
@endphp

<x-page-container :title="Lang::txt('COM_KB')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option, false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
      </svg>
      {{ Lang::txt('COM_KB_MAIN') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_KB_CATEGORIES')">
      <ul class="menu menu-sm">
        <li>
          <a class="{{ $catid <= 0 ? 'active' : '' }}"
             href="{{ $allUrl }}">
            {{ Lang::txt('COM_KB_ALL_ARTICLES') }}
          </a>
        </li>
        @foreach($categories as $row)
          @if($row->get('articles', 0) > 0)
            <li>
              <a class="{{ $catid == $row->get('id') ? 'active' : '' }}"
                 href="{{ Route::url($row->link(), false) }}">
                {{ $row->get('title') }}
                <span class="badge badge-sm badge-ghost">{{ $row->get('articles', 0) }}</span>
              </a>
              @if($catid == $row->get('id'))
                @php
                  $children = $row->children($catFilters)->rows();
                @endphp
                @if(count($children) > 0)
                  <ul>
                    @foreach($children as $child)
                      <li>
                        <a class="{{ $category->get('id') == $child->get('id') ? 'active' : '' }}"
                           href="{{ Route::url($child->link(), false) }}">
                          {{ $child->get('title') }}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                @endif
              @endif
            </li>
          @endif
        @endforeach
      </ul>
    </x-sidebar-card>
  @endslot

  {{-- Article --}}
  <article id="entry-{{ $article->get('id') }}">
    <h2 class="text-xl font-bold mb-4">
      {{ $article->get('title') }}
    </h2>

    <div class="prose max-w-none mb-6">
      {!! $article->fulltxt() !!}
    </div>

    @if($tags = $article->tags('cloud'))
      <div class="mb-4">
        {!! $tags !!}
      </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-base-300 pt-4">
      <x-vote-widget
        :likes="$article->get('helpful', 0)"
        :dislikes="$article->get('nothelpful', 0)"
        :vote="$voteStr"
        :likeUrl="$likeUrl"
        :dislikeUrl="$dislikeUrl"
        :disabled="User::isGuest()"
      />
      <p class="text-sm text-base-content/50">
        {{ Lang::txt('COM_KB_LAST_MODIFIED') }}
        <time datetime="{{ $article->modified() }}">
          {{ $article->modified('time') }}
        </time>
        {{ Lang::txt('COM_KB_DATETIME_ON') }}
        <time datetime="{{ $article->modified() }}">
          {{ $article->modified('date') }}
        </time>
      </p>
    </div>
  </article>

  {{-- Comments --}}
  <section id="comments" class="mt-10">
    <h3 class="text-lg font-semibold mb-4">
      {{ Lang::txt('COM_KB_COMMENTS_ON_ENTRY') }}
      <span class="text-base-content/50 font-normal text-sm">({{ $comments->count() }})</span>
    </h3>

    @if($comments->count() > 0)
      <div class="comment-list">
        @foreach($comments as $comment)
          @if($comment->get('parent') == 0)
            {!! $__view->view('_comment')
                  ->set('comment', $comment)
                  ->set('article', $article)
                  ->set('option', $option)
                  ->set('maxDepth', $maxDepth)
                  ->set('depth', 0)
                  ->loadTemplate() !!}
          @endif
        @endforeach
      </div>
    @else
      <p class="text-base-content/50 italic mb-6">
        {{ Lang::txt('COM_KB_NO_COMMENTS') }}
      </p>
    @endif

    {{-- Comment form --}}
    <h3 class="text-lg font-semibold mb-4" id="post-comment">
      {{ Lang::txt('COM_KB_POST_COMMENT') }}
    </h3>

    @if($commentsOpen)
      @if(User::isGuest())
        <x-auth-gate :returnUrl="Route::url($article->link() . '#post-comment', false, true)" />
      @else
        <form action="{{ Route::url($article->link(), false) }}"
              method="post"
              class="comment-form">

          @if(!$replyto->isNew())
            @php
              $replyName = Lang::txt('JANONYMOUS');
              if (!$replyto->get('anonymous')) {
                  $replyName = $replyto->creator->get('name');
              }
            @endphp
            <blockquote class="border-l-4 border-base-300 pl-4 mb-4 text-sm text-base-content/70">
              <p class="font-medium">{{ $replyName }}</p>
              <p>{{ $replyto->content('raw', 300) }}</p>
            </blockquote>
          @endif

          <div class="comment-form-body">
            <label class="sr-only" for="commentcontent">
              {{ Lang::txt('COM_KB_YOUR_COMMENTS') }}
            </label>
            <textarea class="comment-textarea"
                      id="commentcontent"
                      name="comment[content]"
                      rows="5"
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
                 value="{{ $replyto->get('id') ? e($replyto->get('id')) : '' }}" />
          <input type="hidden" name="comment[created]" value="" />
          <input type="hidden" name="comment[created_by]"
                 value="{{ User::get('id') }}" />
          <input type="hidden" name="comment[state]" value="1" />
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="task" value="savecomment" />
          {!! Html::input('token') !!}
        </form>

        <p class="text-xs text-base-content/40 mt-2">
          {{ Lang::txt('COM_KB_COMMENT_KEEP_RELEVANT') }}
        </p>
      @endif
    @else
      <p class="text-base-content/50 italic">
        {{ Lang::txt('COM_KB_COMMENTS_CLOSED') }}
      </p>
    @endif
  </section>

</x-page-container>
