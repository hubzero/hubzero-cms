{{--
  Blog single entry — full display with comments, sidebar archive/popular.

  Variables from controller (entryTask):
    $row      — Entry model instance
    $archive  — Archive model instance
    $config   — Component params (Registry) with access-* flags
    $filters  — array: state, authorized, access, scope, scope_id

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Breadcrumbs
  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_BLOG'), 'index.php?option=' . $option);
  }
  Pathway::append(
      $row->published('Y'),
      'index.php?option=' . $option . '&year=' . $row->published('Y')
  );
  Pathway::append(
      $row->published('m'),
      'index.php?option=' . $option
      . '&year=' . $row->published('Y')
      . '&month=' . sprintf('%02d', $row->published('m'))
  );
  Pathway::append(
      stripslashes($row->get('title')),
      $row->link()
  );

  Document::setTitle(Lang::txt('COM_BLOG') . ': ' . stripslashes($row->get('title')));

  $__view->css();
  $__view->js();

  // Oldest entry for sidebar year range
  $first = $archive->entries([
      'state'      => $filters['state'],
      'authorized' => $filters['authorized'],
  ])->order('publish_up', 'asc')->limit(1)->row();

  // Owner/admin check
  $isOwnerOrAdmin = (
      User::get('id') == $row->get('created_by')
      || User::authorise('core.manage', $option)
  );

  // Entry status class
  $statusCls = '';
  if (!$row->isAvailable()) {
      $statusCls = 'pending';
  }
  if ($row->ended()) {
      $statusCls = 'expired';
  }
  if ($row->get('state') == 0) {
      $statusCls = 'private';
  }

  // Comments
  $commentCount = 0;
  $comments = [];
  if ($row->get('allow_comments')) {
      $commentCount = $row->comments()
          ->whereIn('state', [
              \Components\Blog\Models\Comment::STATE_PUBLISHED,
              \Components\Blog\Models\Comment::STATE_FLAGGED,
          ])
          ->total();

      // Top-level comments only
      $comments = $row->comments()
          ->whereIn('state', [
              \Components\Blog\Models\Comment::STATE_PUBLISHED,
              \Components\Blog\Models\Comment::STATE_FLAGGED,
          ])
          ->whereEquals('parent', 0)
          ->ordered()
          ->rows();
  }

  // Reply-to comment (if replying)
  $replyToId = Request::getInt('reply', 0);
  $replyTo = null;
  if ($replyToId && $row->get('allow_comments')) {
      $replyTo = $row->comments()
          ->whereEquals('id', $replyToId)
          ->whereIn('state', [
              \Components\Blog\Models\Comment::STATE_PUBLISHED,
              \Components\Blog\Models\Comment::STATE_FLAGGED,
          ])
          ->row();
      if (!$replyTo->get('id')) {
          $replyTo = null;
      }
  }

  // Tags — 'cloud' returns pre-built HTML, 'string' returns comma-separated
  $tagCloud = $row->tags('cloud');

  // Archive URL
  $archiveUrl = Route::url('index.php?option=' . $option . '&task=archive');

  // Max comment depth
  $maxDepth = $config->get('comments_depth', 3);
@endphp

{{-- Page header --}}
<header class="page-header">
  <h1>{{ Lang::txt('COM_BLOG') }}</h1>
  <div class="page-header-actions">
    <a class="btn" href="{{ $archiveUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_BLOG_ARCHIVE') }}
    </a>
  </div>
</header>

<section class="page-body">
  <div class="page-layout">
    <div class="page-main">

      <article id="e{{ $row->get('id') }}">
        {{-- Entry header --}}
        <header class="mb-6">
          <h2 class="text-3xl font-bold mb-2">
            {{ e(stripslashes($row->get('title'))) }}
          </h2>
          <div class="entry-meta">
            <time datetime="{{ $row->published() }}">
              {{ $row->published('date') }}
            </time>
            @if($config->get('show_authors'))
              @php
                $authorName = e(stripslashes($row->creator->get('name')));
                $authorAccess = $row->creator->get('access');
                $viewLevels = User::getAuthorisedViewLevels();
              @endphp
              <span>
                by
                @if(in_array($authorAccess, $viewLevels))
                  <a class="link link-hover" href="{{ Route::url($row->creator->link()) }}">
                    {{ $authorName }}
                  </a>
                @else
                  {{ $authorName }}
                @endif
              </span>
            @endif
            @if($row->get('allow_comments'))
              <a class="link link-hover" href="#comments">
                {{ Lang::txt('COM_BLOG_NUM_COMMENTS', $commentCount) }}
              </a>
            @else
              <span>{{ Lang::txt('COM_BLOG_COMMENTS_OFF') }}</span>
            @endif
            @if($isOwnerOrAdmin && $statusCls)
              <span class="badge badge-sm badge-warning">
                {{ $row->visibility('text') }}
              </span>
            @endif
            @if($isOwnerOrAdmin)
              <span class="entry-actions">
                <a class="btn btn-xs btn-ghost"
                   href="{{ Route::url($row->link('edit')) }}"
                   title="{{ Lang::txt('JACTION_EDIT') }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
                  </svg>
                </a>
                <a class="btn btn-xs btn-ghost text-error"
                   href="{{ Route::url($row->link('delete')) }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}"
                   data-confirm="{{ Lang::txt('COM_BLOG_CONFIRM_DELETE') }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-3.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </a>
              </span>
            @endif
          </div>
        </header>

        {{-- Entry body --}}
        <div class="prose max-w-none mb-6">
          {!! $row->content !!}
        </div>

        {{-- Footer: tags + actions --}}
        @if($tagCloud)
          <footer class="entry-footer">
            <nav aria-label="Tags" class="tags-cloud">
              {!! $tagCloud !!}
            </nav>
          </footer>
        @endif
      </article>

      {{-- Comments --}}
      @if($row->get('allow_comments'))
        <section id="comments" class="entry-comments" aria-label="{{ Lang::txt('COM_BLOG_COMMENTS_HEADER') }}">
          <h3 class="text-xl font-bold mb-4">
            {{ Lang::txt('COM_BLOG_COMMENTS_HEADER') }} ({{ $commentCount }})
          </h3>

          @if($comments->count() > 0)
            <div class="comment-list">
              @foreach($comments as $comment)
                {!! $__view->view('_comment')
                      ->set('comment', $comment)
                      ->set('row', $row)
                      ->set('config', $config)
                      ->set('maxDepth', $maxDepth)
                      ->set('depth', 0)
                      ->loadTemplate() !!}
              @endforeach
            </div>
          @else
            <p class="text-base-content/60">
              {{ Lang::txt('COM_BLOG_NO_COMMENTS') }}
            </p>
          @endif

          {{-- Comment form --}}
          <div class="comment-form-wrapper mt-8">
              <h4 class="text-lg font-bold mb-4">
                {{ Lang::txt('COM_BLOG_POST_COMMENT') }}
              </h4>

              @if(!User::isGuest())
                <form method="post"
                      action="{{ Route::url($row->link()) }}"
                      id="commentform"
                      class="comment-form"
                      aria-label="{{ Lang::txt('COM_BLOG_POST_COMMENT') }}">

                  @if($replyTo)
                    @php
                      $replyName = Lang::txt('JANONYMOUS');
                      if (!$replyTo->get('anonymous')) {
                          $replyName = e(stripslashes($replyTo->creator->get('name', $replyName)));
                      }
                    @endphp
                    <blockquote class="border-l-4 border-base-300 pl-4 mb-4 text-sm text-base-content/70">
                      <p class="font-medium">{{ $replyName }}</p>
                      <p>{{ \Hubzero\Utility\Str::truncate(stripslashes($replyTo->get('content')), 300) }}</p>
                    </blockquote>
                  @endif

                  <div class="comment-form-body">
                    <label class="sr-only" for="commentcontent">
                      {{ $replyTo ? Lang::txt('COM_BLOG_YOUR_REPLY') : Lang::txt('COM_BLOG_FIELD_COMMENTS') }}
                    </label>
                    <textarea class="comment-textarea"
                              id="commentcontent"
                              name="comment[content]"
                              rows="4"
                              placeholder="{{ $replyTo ? Lang::txt('COM_BLOG_YOUR_REPLY') : Lang::txt('COM_BLOG_FIELD_COMMENTS') }}"
                              required></textarea>
                    <div class="comment-form-footer">
                      <label class="comment-form-anon">
                        <input type="checkbox"
                               name="comment[anonymous]"
                               value="1" />
                        {{ Lang::txt('COM_BLOG_POST_ANONYMOUS') }}
                      </label>
                      <button class="btn btn-sm btn-primary" type="submit">
                        {{ Lang::txt('JSUBMIT') }}
                      </button>
                    </div>
                  </div>

                  <input type="hidden" name="comment[id]" value="0" />
                  <input type="hidden" name="comment[entry_id]" value="{{ $row->get('id') }}" />
                  <input type="hidden" name="comment[parent]" value="{{ $replyTo ? $replyTo->get('id') : 0 }}" />
                  <input type="hidden" name="comment[created]" value="" />
                  <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                  <input type="hidden" name="comment[state]" value="1" />
                  <input type="hidden" name="option" value="{{ $option }}" />
                  <input type="hidden" name="task" value="savecomment" />
                  {!! Html::input('token') !!}
                </form>
              @else
                @php
                  $returnUrl = base64_encode(
                      Route::url($row->link() . '#post-comment', false, true)
                  );
                  $loginUrl = Route::url(
                      'index.php?option=com_users&view=login&return=' . $returnUrl
                  );
                @endphp
                <p class="login-to-comment">
                  {!! Lang::txt(
                      'COM_BLOG_MUST_LOG_IN',
                      '<a href="' . $loginUrl . '">'
                      . Lang::txt('COM_BLOG_LOG_IN') . '</a>'
                  ) !!}
                </p>
              @endif
          </div>
        </section>
      @endif

    </div>

    {{-- Sidebar --}}
    <aside class="page-sidebar">

      {{-- Author card --}}
      @if($config->get('show_authors') && $row->creator->get('name'))
        <div class="card bg-base-100 shadow-sm author-card">
          <div class="card-body">
            <div class="author-card-inner">
              <div class="avatar">
                <div class="w-16 rounded-full">
                  <img src="{{ $row->creator->picture() }}" alt="" />
                </div>
              </div>
              <div>
                <h3 class="card-title text-sm">
                  @if(in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
                    <a class="link link-hover"
                       href="{{ Route::url($row->creator->link()) }}">
                      {{ e(stripslashes($row->creator->get('name'))) }}
                    </a>
                  @else
                    {{ e(stripslashes($row->creator->get('name'))) }}
                  @endif
                </h3>
                @if($row->creator->get('bio'))
                  <p class="text-xs text-base-content/60 line-clamp-3">
                    {{ strip_tags($row->creator->get('bio')) }}
                  </p>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- New entry button --}}
      @if($config->get('access-create-entry'))
        <a class="btn btn-outline btn-primary w-full"
           href="{{ Route::url('index.php?option=' . $option . '&task=new') }}">
          {{ Lang::txt('COM_BLOG_NEW_ENTRY') }}
        </a>
      @endif

      {{-- Archive by year/month --}}
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-sm">{{ Lang::txt('COM_BLOG_ENTRIES_BY_YEAR') }}</h3>
          @if($first->get('id'))
            @php
              $startYear  = intval(substr($first->get('publish_up'), 0, 4));
              $nowYear    = intval(Date::format('Y'));
              $entryYear  = substr($row->get('publish_up'), 0, 4);
              $entryMonth = substr($row->get('publish_up'), 5, 2);
              $months = [
                  'COM_BLOG_JANUARY', 'COM_BLOG_FEBRUARY', 'COM_BLOG_MARCH',
                  'COM_BLOG_APRIL',   'COM_BLOG_MAY',      'COM_BLOG_JUNE',
                  'COM_BLOG_JULY',    'COM_BLOG_AUGUST',    'COM_BLOG_SEPTEMBER',
                  'COM_BLOG_OCTOBER', 'COM_BLOG_NOVEMBER',  'COM_BLOG_DECEMBER',
              ];
            @endphp
            <ul class="menu menu-sm p-0">
              @for($i = $nowYear; $i >= $startYear; $i--)
                <li>
                  <a href="{{ Route::url('index.php?option=' . $option . '&year=' . $i) }}">
                    {{ $i }}
                  </a>
                  @if($i == $entryYear)
                    <ul>
                      @for($k = 0; $k < intval($entryMonth); $k++)
                        <li>
                          <a href="{{ Route::url('index.php?option=' . $option . '&year=' . $i . '&month=' . sprintf('%02d', $k + 1)) }}"
                             @if(sprintf('%02d', $k + 1) == $entryMonth) class="active" @endif>
                            {{ Lang::txt($months[$k]) }}
                          </a>
                        </li>
                      @endfor
                    </ul>
                  @endif
                </li>
              @endfor
            </ul>
          @else
            <p class="text-sm text-base-content/60">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
          @endif
        </div>
      </div>

      {{-- Popular entries --}}
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-sm">{{ Lang::txt('COM_BLOG_POPULAR_ENTRIES') }}</h3>
          @php
            $popular = $archive->entries([
                'state'  => $filters['state'],
                'access' => $filters['access'],
            ])->order('hits', 'desc')->limit(5)->rows();
          @endphp
          @if($popular->count())
            <ul class="menu menu-sm p-0">
              @foreach($popular as $prow)
                <li>
                  <a href="{{ Route::url($prow->link()) }}">
                    {{ e(stripslashes($prow->get('title'))) }}
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm text-base-content/60">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
          @endif
        </div>
      </div>

    </aside>
  </div>
</section>
