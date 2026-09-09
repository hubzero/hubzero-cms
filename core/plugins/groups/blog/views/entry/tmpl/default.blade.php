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

$__view->css();
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog';

$first = $archive->entries([
        'state'      => $filters['state'],
        'authorized' => $filters['authorized']
    ])
    ->order('publish_up', 'asc')
    ->limit(1)
    ->row();

$canManage = $authorized == 'manager' || $authorized == 'admin';
$showOptions = $group->published == 1 && ($canpost || $canManage);
@endphp

@if ($showOptions)
    <ul id="page_options">
        @if ($canpost)
            <li>
                <a class="btn btn-primary gap-2" href="{{ Route::url($base . '&action=new') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    {{ Lang::txt('PLG_GROUPS_BLOG_NEW_ENTRY') }}
                </a>
            </li>
        @endif
        @if ($canManage)
            <li>
                <a class="btn btn-ghost gap-2" href="{{ Route::url($base . '&action=settings') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS') }}
                </a>
            </li>
        @endif
    </ul>
@endif

<section class="main section entry-container">
    <div class="subject">
        @php
        $cls = '';
        if (!$row->isAvailable()) {
            $cls = ' pending';
        }
        if ($row->ended()) {
            $cls = ' expired';
        }
        if ($row->get('state') == 0) {
            $cls = ' private';
        }
        @endphp

        <article class="card bg-base-100 shadow-sm {{ $cls }}" id="e{{ $row->get('id') }}">
            <div class="card-body">
                <h2 class="card-title text-2xl">
                    {{ e(stripslashes($row->get('title'))) }}
                </h2>

                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/70 mb-4">
                    <span>
                        {{ Lang::txt('PLG_GROUPS_BLOG_ENTRY_NUMBER', $row->get('id')) }}
                    </span>
                    <span>
                        <time datetime="{{ $row->published() }}">
                            {{ $row->published('date') }}
                        </time>
                    </span>
                    <span>
                        <time datetime="{{ $row->published() }}">
                            {{ $row->published('time') }}
                        </time>
                    </span>
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
                            {{ Lang::txt('PLG_GROUPS_BLOG_NUM_COMMENTS', $commentCount) }}
                        </a>
                    @else
                        <span>{{ Lang::txt('PLG_GROUPS_BLOG_COMMENTS_OFF') }}</span>
                    @endif

                    @php
                    $isAuthor = User::get('id') == $row->get('created_by');
                    $canEdit = $isAuthor || $canManage;
                    @endphp
                    @if ($canEdit)
                        <span class="badge badge-outline badge-sm">
                            {{ $row->visibility('text') }}
                        </span>
                        @if ($group->published == 1)
                            <div class="flex gap-2 ml-auto">
                                <a class="btn btn-ghost btn-xs gap-1"
                                    href="{{ Route::url($row->link('edit')) }}"
                                    title="{{ Lang::txt('PLG_GROUPS_BLOG_EDIT') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    {{ Lang::txt('PLG_GROUPS_BLOG_EDIT') }}
                                </a>
                                <a class="btn btn-ghost btn-xs text-error gap-1"
                                    data-confirm="{{ Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE') }}"
                                    href="{{ Route::url($row->link('delete')) }}"
                                    title="{{ Lang::txt('PLG_GROUPS_BLOG_DELETE') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ Lang::txt('PLG_GROUPS_BLOG_DELETE') }}
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="entry-content prose max-w-none">
                    {!! $row->content !!}
                    {!! $row->tags('cloud') !!}
                </div>

                @php $authorName = $row->creator->get('name'); @endphp
                @if ($authorName)
                    @php $authorName = e(stripslashes($authorName)); @endphp
                    <div class="divider"></div>
                    <div class="flex items-start gap-4">
                        <div class="avatar">
                            <div class="w-16 rounded-full">
                                <img src="{{ $row->creator->picture() }}" alt="" />
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-base-content/60 mb-1">
                                {{ Lang::txt('PLG_GROUPS_BLOG_ABOUT_AUTHOR') }}
                            </h3>
                            <h4 class="font-bold">
                                @php
                                $creatorAccess = $row->creator->get('access');
                                $canViewCreator = in_array($creatorAccess, User::getAuthorisedViewLevels());
                                @endphp
                                @if ($canViewCreator)
                                    <a class="link link-hover" href="{{ Route::url($row->creator->link()) }}">
                                        {{ $authorName }}
                                    </a>
                                @else
                                    {{ $authorName }}
                                @endif
                            </h4>
                            <p class="text-sm text-base-content/70">
                                @if ($row->creator->get('bio'))
                                    {!! $row->creator->get('bio') !!}
                                @else
                                    <em>{{ Lang::txt('PLG_GROUPS_BLOG_AUTHOR_BIO_BLANK') }}</em>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </div>

    <aside class="aside">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-base">{{ Lang::txt('PLG_GROUPS_BLOG_POPULAR_ENTRIES') }}</h4>
                @php
                $popular = $archive->entries([
                        'state'  => $filters['state'],
                        'access' => $filters['access']
                    ])
                    ->order('hits', 'desc')
                    ->limit(5)
                    ->rows();
                @endphp
                @if ($popular->count())
                    <ul class="menu menu-sm bg-base-100 rounded-box p-0">
                        @foreach ($popular as $prow)
                            @if ($prow->isAvailable() || $prow->get('created_by') == User::get('id'))
                                <li>
                                    <a href="{{ Route::url($prow->link()) }}">
                                        {{ e(stripslashes($prow->get('title'))) }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-base-content/60">{{ Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND') }}</p>
                @endif
            </div>
        </div>
    </aside>
</section>

@if ($row->get('allow_comments'))
    <section class="section below mt-6">
        <div class="subject">
            <h3 id="comments" class="text-xl font-bold mb-4">
                {{ Lang::txt('PLG_GROUPS_BLOG_COMMENTS_HEADER') }}
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
                    ->set('group', $group)
                    ->set('parent', 0)
                    ->set('cls', 'odd')
                    ->set('depth', 0)
                    ->set('option', $option)
                    ->set('comments', $comments)
                    ->set('config', $config)
                    ->set('base', $row->link())
                    ->display();
                @endphp
            @else
                <p class="text-base-content/60 italic">
                    {{ Lang::txt('PLG_GROUPS_BLOG_NO_COMMENTS') }}
                </p>
            @endif

            @if ($group->published == 1)
                <h3 class="text-lg font-bold mt-6 mb-4" id="post-comment">
                    {{ Lang::txt('PLG_GROUPS_BLOG_POST_COMMENT') }}
                </h3>

                <form method="post" action="{{ Route::url($row->link()) }}" id="commentform">
                    <div class="flex items-start gap-4">
                        <div class="avatar">
                            <div class="w-10 rounded-full">
                                <img src="{{ User::picture(User::isGuest() ? 1 : 0) }}" alt="" />
                            </div>
                        </div>

                        <fieldset class="flex-1">
                            @php
                            $replyto = $row->comments()
                                ->whereEquals('id', Request::getInt('reply', 0))
                                ->whereIn('state', [
                                    \Components\Blog\Models\Comment::STATE_PUBLISHED,
                                    \Components\Blog\Models\Comment::STATE_FLAGGED
                                ])
                                ->row();
                            @endphp

                            @if ($replyto->get('id'))
                                @php
                                $replyName = Lang::txt('JANONYMOUS');
                                if (!$replyto->get('anonymous')) {
                                    $replyCreatorName = $replyto->creator->get('name', $replyName);
                                    $replyName = e(stripslashes($replyCreatorName));
                                    $replyAccess = $replyto->creator->get('access');
                                    $viewLevels = User::getAuthorisedViewLevels();
                                    if (in_array($replyAccess, $viewLevels)) {
                                        $creatorUrl = Route::url($replyto->creator->link());
                                        $replyName = '<a href="' . $creatorUrl . '">' . $replyName . '</a>';
                                    }
                                }
                                $replyCreated = $replyto->get('created');
                                $replyTime = $replyto->created('time');
                                $replyDate = $replyto->created('date');
                                $replyContent = \Hubzero\Utility\Str::truncate(
                                    stripslashes($replyto->get('content')),
                                    300
                                );
                                @endphp
                                <blockquote class="border-l-4 border-base-300 pl-4 mb-4 text-sm text-base-content/70" cite="c{{ $replyto->get('id') }}">
                                    <p>
                                        <strong>{!! $replyName !!}</strong>
                                        {{ Lang::txt('PLG_GROUPS_BLOG_AT') }}
                                        <time datetime="{{ $replyCreated }}">{{ $replyTime }}</time>
                                        {{ Lang::txt('PLG_GROUPS_BLOG_ON') }}
                                        <time datetime="{{ $replyCreated }}">{{ $replyDate }}</time>
                                    </p>
                                    <p>{{ $replyContent }}</p>
                                </blockquote>
                            @endif

                            @if (!User::isGuest())
                                @php
                                $commentType = ($replyto->get('id')) ? 'reply' : 'comments';
                                @endphp
                                <div class="form-control w-full mb-4">
                                    <label class="label" for="comment_content">
                                        <span class="label-text">
                                            Your {{ $commentType }}:
                                            <span class="text-error">{{ Lang::txt('PLG_GROUPS_BLOG_REQUIRED') }}</span>
                                        </span>
                                    </label>
                                    {!! $__view->editor(
                                        'comment[content]',
                                        '',
                                        40,
                                        15,
                                        'comment_content',
                                        ['class' => 'minimal no-footer']
                                    ) !!}
                                </div>

                                <div class="form-control mb-4">
                                    <label class="label cursor-pointer justify-start gap-2">
                                        <input type="checkbox"
                                            class="checkbox checkbox-sm"
                                            name="comment[anonymous]"
                                            id="comment-anonymous"
                                            value="1" />
                                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS') }}</span>
                                    </label>
                                </div>

                                <button type="submit" name="submit" class="btn btn-primary">
                                    {{ Lang::txt('PLG_GROUPS_BLOG_SUBMIT') }}
                                </button>
                            @else
                                @php
                                $returnUrl = base64_encode(
                                    Route::url($row->link() . '#post-comment', false, true)
                                );
                                $loginUrl = Route::url(
                                    'index.php?option=com_users&view=login&return=' . $returnUrl
                                );
                                $loginTxt = Lang::txt('PLG_GROUPS_BLOG_LOG_IN');
                                $loginLink = '<a href="' . $loginUrl . '">' . $loginTxt . '</a>';
                                @endphp
                                <div class="alert alert-warning">
                                    <p>{!! Lang::txt('PLG_GROUPS_BLOG_MUST_LOG_IN', $loginLink) !!}</p>
                                </div>
                            @endif

                            <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
                            <input type="hidden" name="comment[id]" value="0" />
                            <input type="hidden" name="comment[entry_id]" value="{{ $row->get('id') }}" />
                            <input type="hidden" name="comment[parent]" value="{{ $replyto->get('id') }}" />
                            <input type="hidden" name="comment[created]" value="" />
                            <input type="hidden" name="comment[created_by]" value="{{ User::get('id') }}" />
                            <input type="hidden" name="comment[state]" value="1" />
                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="active" value="blog" />
                            <input type="hidden" name="action" value="savecomment" />

                            {!! Html::input('token') !!}

                            <div class="mt-4 p-4 bg-base-200 rounded-box text-sm">
                                <p><strong>{{ Lang::txt('PLG_GROUPS_BLOG_COMMENTS_KEEP_POLITE') }}</strong></p>
                                <p>{{ Lang::txt('PLG_GROUPS_BLOG_COMMENT_HELP') }}</p>
                            </div>
                        </fieldset>
                    </div>
                </form>
            @endif
        </div>

        <aside class="aside">
            @if ($group->published == 1)
                <p>
                    <a class="btn btn-primary gap-2" href="#post-comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_BLOG_ADD_A_COMMENT') }}
                    </a>
                </p>
            @endif
        </aside>
    </section>
@endif
