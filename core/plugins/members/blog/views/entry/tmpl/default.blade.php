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

$base = $member->link() . '&active=blog';

$__view->css()->js();
@endphp

<div class="flex flex-wrap gap-2 mb-4">
  @if (User::get('id') == $member->get('id'))
    <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&task=new') }}">
      {{ Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY') }}
    </a>
  @endif
  <a class="btn btn-ghost btn-sm" href="{{ Route::url($base) }}">
    {{ Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE') }}
  </a>
</div>

@if ($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<div class="flex flex-col lg:flex-row gap-6">
  {{-- Main content --}}
  <div class="flex-1 min-w-0">
    @php
      $cls = '';
      if (!$row->isAvailable()) { $cls = 'opacity-60'; }
      if ($row->ended()) { $cls = 'opacity-60'; }
    @endphp
    <article class="card bg-base-100 shadow-sm {{ $cls }}">
      <div class="card-body">
        @if ($row->get('state') == 0)
          <div class="badge badge-warning mb-2">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PRIVACY_PRIVATE') }}</div>
        @endif

        <h2 class="card-title text-xl">
          {{ e(stripslashes($row->get('title'))) }}
        </h2>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/60 mt-1">
          <time datetime="{{ $row->published() }}">
            {{ $row->published('date') }}
          </time>
          <span>{{ $row->published('time') }}</span>

          @if ($row->get('allow_comments'))
            @php
              $commentCount = $row->comments()
                  ->whereIn('state', [
                      \Components\Blog\Models\Comment::STATE_PUBLISHED,
                      \Components\Blog\Models\Comment::STATE_FLAGGED
                  ])
                  ->count();
            @endphp
            <a class="link link-hover" href="{{ Route::url($row->link('comments')) }}">
              {{ Lang::txt('PLG_MEMBERS_BLOG_NUM_COMMENTS', $commentCount) }}
            </a>
          @else
            <span>{{ Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_OFF') }}</span>
          @endif

          @if (User::get('id') == $row->get('created_by'))
            <span class="badge badge-ghost badge-sm">{{ $row->visibility('text') }}</span>
          @endif
        </div>

        @if (User::get('id') == $row->get('created_by'))
          <div class="flex gap-2 mt-2">
            <a class="btn btn-ghost btn-xs"
               href="{{ Route::url($row->link('edit')) }}">
              {{ Lang::txt('PLG_MEMBERS_BLOG_EDIT') }}
            </a>
            <a class="btn btn-ghost btn-xs text-error"
               data-confirm="{{ Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE') }}"
               href="{{ Route::url($row->link('delete')) }}">
              {{ Lang::txt('PLG_MEMBERS_BLOG_DELETE') }}
            </a>
          </div>
        @endif

        <div class="prose max-w-none mt-4">
          {!! $row->content !!}
        </div>

        @if ($row->tags('cloud'))
          <div class="mt-4">
            {!! $row->tags('cloud') !!}
          </div>
        @endif
      </div>
    </article>

    {{-- Comments section --}}
    @if ($row->get('allow_comments'))
      <div class="mt-6">
        <h3 class="text-lg font-semibold mb-4">
          {{ Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_HEADER') }}
        </h3>

        @php
          $comments = $row->comments()
              ->including(['creator', function ($creator) {
                  $creator->select('*');
              }])
              ->whereIn('state', [
                  \Components\Blog\Models\Comment::STATE_PUBLISHED,
                  \Components\Blog\Models\Comment::STATE_FLAGGED
              ])
              ->whereEquals('parent', 0)
              ->ordered()
              ->rows();
        @endphp

        @if ($comments->count() > 0)
          @php
            $__view->view('_list', 'comments')
                ->set('parent', 0)
                ->set('cls', 'odd')
                ->set('depth', 0)
                ->set('option', $option)
                ->set('comments', $comments)
                ->set('config', $config)
                ->set('base', $row->link())
                ->set('member', $member)
                ->display();
          @endphp
        @else
          <p class="text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_BLOG_NO_COMMENTS') }}
          </p>
        @endif

        <h3 class="text-lg font-semibold mt-6 mb-4" id="post-comment">
          {{ Lang::txt('PLG_MEMBERS_BLOG_ADD_A_COMMENT') }}
        </h3>

        <form method="post" action="{{ Route::url($row->link()) }}" id="commentform">
          @php
            $user = \Components\Members\Models\Member::oneOrNew(User::get('id'));
            $anon = (User::isGuest() ? 0 : 1);

            $replyto = $row->comments()
                ->whereEquals('id', Request::getInt('reply', 0))
                ->whereIn('state', [
                    \Components\Blog\Models\Comment::STATE_PUBLISHED,
                    \Components\Blog\Models\Comment::STATE_FLAGGED
                ])
                ->row();
          @endphp

          <div class="flex gap-4">
            <div class="shrink-0">
              <img class="rounded-full w-10 h-10"
                   src="{{ $user->picture($anon) }}"
                   alt="" />
            </div>
            <div class="flex-1">
              <fieldset class="space-y-4">
                @if ($replyto->get('id'))
                  @php
                    $replyName = Lang::txt('JANONYMOUS');
                    if (!$replyto->get('anonymous')) {
                        $replyName = e(stripslashes($replyto->creator->get('name')));
                        if (in_array($replyto->creator->get('access'), User::getAuthorisedViewLevels())) {
                            $replyName = '<a href="' . Route::url($replyto->creator->link()) . '">' . $replyName . '</a>';
                        }
                    }
                  @endphp
                  <blockquote class="border-l-4 border-base-300 pl-4 text-sm text-base-content/70">
                    <p>
                      <strong>{!! $replyName !!}</strong>
                      <span class="text-xs">
                        {{ Lang::txt('PLG_MEMBERS_BLOG_AT') }}
                        <time datetime="{{ $replyto->get('created') }}">{{ $replyto->created('time') }}</time>
                        {{ Lang::txt('PLG_MEMBERS_BLOG_ON') }}
                        <time datetime="{{ $replyto->get('created') }}">{{ $replyto->created('date') }}</time>
                      </span>
                    </p>
                    <p>{{ \Hubzero\Utility\Str::truncate(stripslashes($replyto->get('content')), 300) }}</p>
                  </blockquote>
                @endif

                <div>
                  <label for="commentcontent" class="label">
                    <span class="label-text">
                      {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS') }}
                      <span class="text-error">*</span>
                    </span>
                  </label>
                  @if (!User::isGuest())
                    {!! $__view->editor('comment[content]', '', 40, 15, 'commentcontent', ['class' => 'minimal no-footer']) !!}
                  @else
                    @php
                      $returnUrl = base64_encode(
                          Route::url($row->link() . '#post-comment', false, true)
                      );
                      $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $returnUrl);
                    @endphp
                    <div class="alert alert-warning">
                      {!! Lang::txt('PLG_MEMBERS_BLOG_MUST_LOG_IN', '<a href="' . $loginUrl . '">' . Lang::txt('PLG_MEMBERS_BLOG_LOG_IN') . '</a>') !!}
                    </div>
                  @endif
                </div>

                @if (!User::isGuest())
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox"
                           class="checkbox checkbox-sm"
                           name="comment[anonymous]"
                           id="comment-anonymous"
                           value="1" />
                    <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS') }}</span>
                  </label>

                  <div>
                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                      {{ Lang::txt('PLG_MEMBERS_BLOG_SUBMIT') }}
                    </button>
                  </div>
                @endif

                <input type="hidden" name="id" value="{{ $member->get('id') }}" />
                <input type="hidden" name="comment[id]" value="0" />
                <input type="hidden" name="comment[entry_id]" value="{{ $row->get('id') }}" />
                <input type="hidden" name="comment[parent]" value="{{ $replyto->get('id') }}" />
                <input type="hidden" name="comment[created]" value="" />
                <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                <input type="hidden" name="comment[state]" value="1" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="active" value="blog" />
                <input type="hidden" name="task" value="view" />
                <input type="hidden" name="action" value="savecomment" />

                {!! Html::input('token') !!}
              </fieldset>

              <p class="text-xs text-base-content/50 mt-3">
                <strong>{{ Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_KEEP_POLITE') }}</strong>
                {{ Lang::txt('PLG_MEMBERS_BLOG_COMMENT_HELP') }}
              </p>
            </div>
          </div>
        </form>
      </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <aside class="w-full lg:w-64 shrink-0">
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body p-4">
        <h4 class="font-semibold mb-2">{{ Lang::txt('PLG_MEMBERS_BLOG_POPULAR_ENTRIES') }}</h4>
        @php
          $popFilters = [
              'state' => [\Components\Blog\Models\Entry::STATE_PUBLISHED],
              'access' => User::getAuthorisedViewLevels(),
          ];
          if (User::get('id') == $member->get('id')) {
              $popFilters['state'][] = \Components\Blog\Models\Entry::STATE_UNPUBLISHED;
              unset($popFilters['access']);
          }
          $popular = $archive->entries($popFilters)
              ->order('hits', 'desc')
              ->limit(5)
              ->rows();
        @endphp
        @if ($popular->count())
          <ol class="list-decimal list-inside space-y-1 text-sm">
            @foreach ($popular as $popRow)
              <li>
                <a class="link link-hover" href="{{ Route::url($popRow->link()) }}">
                  {{ e(stripslashes($popRow->get('title'))) }}
                </a>
              </li>
            @endforeach
          </ol>
        @else
          <p class="text-sm text-base-content/60">{{ Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND') }}</p>
        @endif
      </div>
    </div>

    @if ($row->get('allow_comments'))
      <div class="mt-4">
        <a class="btn btn-primary btn-sm w-full" href="#post-comment">
          {{ Lang::txt('PLG_MEMBERS_BLOG_ADD_A_COMMENT') }}
        </a>
      </div>
    @endif
  </aside>
</div>
