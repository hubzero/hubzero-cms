{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js('api');
$__view->js('notify');
$__view->js('user');
$__view->js('forum');

$base = 'index.php?option='
    . $option
    . '&cn='
    . $group->get('cn')
    . '&active=forum&scope='
    . $filters['section']
    . '/'
    . $category->get('alias')
    . '/'
    . $thread->get('thread');

$category->set('section_alias', $filters['section']);

$thread->set('section', $filters['section']);
$thread->set('category', $category->get('alias'));

$now = time();
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-neutral gap-2" href="{{ Route::url($category->link()) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_ALL_DISCUSSIONS') }}
        </a>
    </li>
</ul>

<section class="main section">
    <div class="subject">
        <h3 class="thread-title text-xl font-bold {{ $thread->get('closed') ? 'closed text-base-content/50' : '' }}">
            {{ e(stripslashes($thread->get('title'))) }}
        </h3>

        @php
        $threading = $config->get('threading', 'list');

        $total = $thread->thread()
            ->whereIn('state', $filters['state'])
            ->whereIn('access', $filters['access'])
            ->total();

        $posts = $thread->thread()
            ->whereIn('state', $filters['state'])
            ->whereIn('access', $filters['access'])
            ->order(($threading == 'tree' ? 'lft' : 'id'), 'asc')
            ->limit($filters['limit'])
            ->start($filters['start'])
            ->rows();

        $pageNav = new Hubzero\Pagination\Paginator(
            $total,
            $filters['start'],
            $filters['limit']
        );
        @endphp

        @if ($posts->count() > 0)
            @php
            if ($threading == 'tree') {
                $posts = $thread->toTree($posts);
            }

            $__view->view('_list')
                 ->set('option', $option)
                 ->set('group', $group)
                 ->set('comments', $posts)
                 ->set('thread', $thread)
                 ->set('likes', $likes)
                 ->set('parent', 0)
                 ->set('config', $config)
                 ->set('depth', 0)
                 ->set('cls', 'odd')
                 ->set('filters', $filters)
                 ->set('category', $category)
                 ->display();
            @endphp
        @else
            <ol class="comments">
                <li>
                    <p>{{ Lang::txt('PLG_GROUPS_FORUM_NO_REPLIES_FOUND') }}</p>
                </li>
            </ol>
        @endif

        <form action="{{ Route::url($thread->link()) }}" method="get">
            @php
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'forum');
            $pageNav->setAdditionalUrlParam(
                'scope',
                $filters['section'] . '/' . $category->get('alias') . '/' . $thread->get('id')
            );
            echo $pageNav;
            @endphp
        </form>
    </div>

    <aside class="aside">
        <div class="container">
            <h4>{{ Lang::txt('PLG_GROUPS_FORUM_ALL_TAGS') }}</h4>
            @if ($thread->tags('cloud'))
                {!! $thread->tags('cloud') !!}
            @else
                <p>{{ Lang::txt('PLG_GROUPS_FORUM_NONE') }}</p>
            @endif
        </div>

        @php
        $participants = $thread->participants()
            ->whereIn('state', $filters['state'])
            ->whereIn('access', $filters['access'])
            ->rows();
        @endphp

        @if ($participants->count() > 0)
            <div class="container">
                <h4>{{ Lang::txt('PLG_GROUPS_FORUM_PARTICIPANTS') }}</h4>
                <ul>
                    @php $anon = false; @endphp
                    @foreach ($participants as $participant)
                        @if (!$participant->get('anonymous'))
                            <li>
                                <a class="member link link-hover"
                                    href="{{ Route::url('index.php?option=com_members&id=' . $participant->get('created_by')) }}">
                                    {{ e(stripslashes($participant->get('name'))) }}
                                </a>
                            </li>
                        @elseif (!$anon)
                            @php $anon = true; @endphp
                            <li>
                                <span class="member">{{ Lang::txt('JANONYMOUS') }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        @php
        $threadAttachments = \Components\Forum\Models\Attachment::all()
            ->whereEquals('parent', $thread->get('thread'))
            ->whereIn('state', $filters['state'])
            ->rows();
        @endphp

        @if ($threadAttachments->count() > 0)
            <div class="container">
                <h4>{{ Lang::txt('PLG_GROUPS_FORUM_ATTACHMENTS') }}</h4>
                <ul class="attachments">
                    @foreach ($threadAttachments as $attachment)
                        @if ($attachment->get('status') != $attachment::STATE_DELETED)
                            @php
                            $aCls = 'file';
                            $aTitle = trim($attachment->get('description', $attachment->get('filename')));
                            $aTitle = ($aTitle ? $aTitle : $attachment->get('filename'));
                            $aTitle = (strlen($aTitle) > 25) ? substr($aTitle, 0, 22) . '...' : $aTitle;

                            if ($attachment->isImage()) {
                                $aCls = 'img';
                            }
                            $attachUrl = Route::url(
                                $base . '/'
                                . $attachment->get('post_id')
                                . '/' . $attachment->get('filename')
                            );
                            @endphp
                            <li>
                                <a class="{{ $aCls }} attachment link link-hover"
                                    href="{{ $attachUrl }}">
                                    {{ e(stripslashes($aTitle)) }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    </aside>
</section>

@if ($config->get('access-create-thread') && !$thread->get('closed'))
<section class="below section">
    <div class="subject">
        <h3 class="post-comment-title text-lg font-bold">
            {{ Lang::txt('PLG_GROUPS_FORUM_ADD_COMMENT') }}
        </h3>
        <form action="{{ Route::url($base) }}" method="post" id="commentform" enctype="multipart/form-data">
            <p class="comment-member-photo">
                @php $anon = (!User::isGuest() ? 0 : 1); @endphp
                <img class="rounded-full" src="{{ User::picture($anon) }}" alt="" />
            </p>

            <fieldset>
            @if (User::isGuest())
                <div class="alert alert-warning">
                    <p>{{ Lang::txt('PLG_GROUPS_FORUM_LOGIN_COMMENT_NOTICE') }}</p>
                </div>
            @else
                @php
                $userUrl = Route::url('index.php?option=com_members&id=' . User::get('id'));
                $userName = e(User::get('name'));
                $localTime = Date::toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                $localDate = Date::toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                @endphp
                <p class="comment-title">
                    <strong>
                        <a href="{{ $userUrl }}">{{ $userName }}</a>
                    </strong>
                    <span class="permalink text-sm text-base-content/70">
                        <span class="comment-date-at">{{ Lang::txt('PLG_GROUPS_FORUM_AT') }}</span>
                        <span class="time">
                            <time datetime="{{ $now }}">{{ $localTime }}</time>
                        </span>
                        <span class="comment-date-on">{{ Lang::txt('PLG_GROUPS_FORUM_ON') }}</span>
                        <span class="date">
                            <time datetime="{{ $now }}">{{ $localDate }}</time>
                        </span>
                    </span>
                </p>

                <label for="field_comment" id="addNewPostAreaGroup">
                    <div>
                        {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS') }}
                        <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_FORUM_REQUIRED') }}</span>
                        <span class="text-sm text-base-content/70 float-right">
                            Use an @ sign to mention group users in the post
                        </span>
                    </div>
                    @php
                    $gid = $group->get('gidNumber');
                    $feedUrl = '/api/members/mentions/group?gid=' . $gid . '&search={encodedQuery}';
                    $itemTpl = '<li data-id="{id}">'
                        . '<img class="photo" src="{picture}" />'
                        . '<strong class="username">{username}</strong>'
                        . '<span class="fullname">{name}</span></li>';
                    $outputTpl = '<a href="/members/{id}"'
                        . ' data-user-id="{id}"'
                        . ' target="_blank">'
                        . '@{username}</a>&nbsp;&nbsp;';
                    echo $__view->editor(
                        'fields[comment]',
                        '',
                        35,
                        15,
                        'fieldcomment',
                        [
                            'class' => 'minimal no-footer',
                            'mentions' => [
                                [
                                    'minChars' => 0,
                                    'feed' => $feedUrl,
                                    'itemTemplate' => $itemTpl,
                                    'outputTemplate' => $outputTpl,
                                ]
                            ]
                        ]
                    );
                    @endphp
                </label>

                <label>
                    {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_YOUR_TAGS') }}:
                    @php
                    echo $__view->autocompleter(
                        'tags',
                        'tags',
                        e($thread->tags('string')),
                        'actags'
                    );
                    @endphp
                </label>

                <fieldset>
                    <legend>{{ Lang::txt('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS') }}</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="upload" class="label">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE') }}:</span>
                            </label>
                            <input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" />
                        </div>
                        <div class="form-group">
                            <label for="upload-description" class="label">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION') }}:</span>
                            </label>
                            <input type="text" name="description" id="upload-description" class="input input-bordered w-full" value="" />
                        </div>
                    </div>
                </fieldset>

                @if ($config->get('allow_anonymous'))
                    <label for="field-anonymous" class="label cursor-pointer justify-start gap-2">
                        <input class="checkbox checkbox-sm" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS') }}</span>
                    </label>
                @endif

                <p class="submit mt-4">
                    <input type="submit" class="btn btn-primary" value="{{ Lang::txt('PLG_GROUPS_FORUM_SUBMIT') }}" />
                </p>
            @endif

                <div class="alert alert-info mt-4">
                    <p><strong>{{ Lang::txt('PLG_GROUPS_FORUM_KEEP_POLITE') }}</strong></p>
                </div>
            </fieldset>
            <input type="hidden" name="fields[category_id]" value="{{ e($thread->get('category_id')) }}" />
            <input type="hidden" name="fields[parent]" value="{{ e($thread->get('id')) }}" />
            <input type="hidden" name="fields[thread]" value="{{ e($thread->get('id')) }}" />
            <input type="hidden" name="fields[state]" value="1" />
            <input type="hidden" name="fields[access]" value="{{ $thread->get('access', 0) }}" />
            <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
            <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />
            <input type="hidden" name="fields[id]" value="" />

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
            <input type="hidden" name="active" value="forum" />
            <input type="hidden" name="action" value="savethread" />
            <input type="hidden" name="section" value="{{ e($filters['section']) }}" />

            {!! Html::input('token') !!}
        </form>
    </div>
    <aside class="aside">
    </aside>
</section>
@endif
