{{--
 * Wiki comments — single comment with actions and nested replies
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $cls = $cls ?? 'odd';
    if ($page->get('created_by') == $comment->get('created_by')) {
        $cls .= ' author';
    }
    $cls .= $comment->isReported() ? ' abusive' : '';

    $lnk = $page->link();
    $d = strstr($lnk, '?') ? '&' : '?';
    $comment->base = $lnk . $d . ($page->get('scope_id') ? 'action' : 'task');

    $name = Lang::txt('JANONYMOUS');
    if (!$comment->get('anonymous')) {
        $name = e(stripslashes($comment->creator->get('name', $name)));
        if (in_array($comment->creator->get('access'), User::getAuthorisedViewLevels())) {
            $name = '<a href="' . Route::url($comment->creator->link(), false) . '">' . $name . '</a>';
        }
    }

    $commentBody = $comment->isReported()
        ? '<p class="text-warning">' . Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE') . '</p>'
        : $comment->content('parsed');

    $comment->set('category', 'answercomment');
@endphp

<li class="card bg-base-100 shadow-sm {{ $cls }}" id="c{{ $comment->get('id') }}">
    <div class="card-body p-4">
        <div class="flex gap-3">
            <div class="avatar">
                <div class="w-10 h-10 rounded-full">
                    <img src="{{ $comment->creator->picture($comment->get('anonymous')) }}"
                         alt="" />
                </div>
            </div>

            <div class="flex-1 min-w-0">
                {{-- Rating --}}
                @if($page->config('comment_ratings') && $comment->get('rating'))
                    @php
                        $stars = (int) $comment->get('rating');
                    @endphp
                    <div class="rating rating-sm mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="mask mask-star-2 {{ $i <= $stars ? 'bg-warning' : 'bg-base-300' }}"></span>
                        @endfor
                    </div>
                @endif

                {{-- Comment header --}}
                <p class="text-sm">
                    <strong>{!! $name !!}</strong>
                    @php
                        $permalinkUrl = Route::url(
                            $page->link('comments') . '#c' . $comment->get('id'),
                            false
                        );
                    @endphp
                    <a class="link link-hover text-base-content/50 ml-2" href="{{ $permalinkUrl }}">
                        <time datetime="{{ $comment->created() }}">
                            {{ $comment->created('date') }}
                            {{ $comment->created('time') }}
                        </time>
                    </a>
                </p>

                {{-- Comment body --}}
                <div class="prose prose-sm max-w-none mt-2">
                    {!! $commentBody !!}
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($page->access('delete', 'comment'))
                        <a class="btn btn-xs btn-ghost text-error"
                           href="{{ Route::url($comment->link('delete'), false) }}">
                            {{ Lang::txt('JACTION_DELETE') }}
                        </a>
                    @endif
                    @if($page->access('edit', 'comment'))
                        <a class="btn btn-xs btn-ghost"
                           href="{{ Route::url($comment->link('edit'), false) }}">
                            {{ Lang::txt('JACTION_EDIT') }}
                        </a>
                    @endif

                    @if(!$comment->isReported())
                        @if($depth < $config->get('comments_depth', 3))
                            @php
                                $cid = $comment->get('id');
                                $isActive = (Request::getInt('reply', 0) == $cid);
                            @endphp
                            <a class="btn btn-xs btn-ghost"
                               data-txt-active="{{ Lang::txt('JCANCEL') }}"
                               data-txt-inactive="{{ Lang::txt('COM_WIKI_REPLY') }}"
                               href="{{ Route::url($isActive ? $comment->link() : $comment->link('reply'), false) }}"
                               data-rel="comment-form{{ $cid }}">
                                {{ $isActive ? Lang::txt('JCANCEL') : Lang::txt('COM_WIKI_REPLY') }}
                            </a>
                        @endif
                        <a class="btn btn-xs btn-ghost text-warning"
                           data-txt-flagged="{{ Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE') }}"
                           href="{{ Route::url($comment->link('report'), false) }}">
                            {{ Lang::txt('COM_WIKI_REPORT_ABUSE') }}
                        </a>
                    @endif
                </div>

                {{-- Reply form --}}
                @if($depth < $config->get('comments_depth', 3))
                    <div class="{{ Request::getInt('reply', 0) != $comment->get('id') ? 'hidden' : '' }} mt-4"
                         id="comment-form{{ $comment->get('id') }}">
                        @if(User::isGuest())
                            <div role="alert" class="alert alert-warning">
                                @php
                                    $loginReturn = base64_encode(
                                        Route::url($page->link('comments'), false, true)
                                    );
                                    $loginUrl = Route::url(
                                        'index.php?option=com_users&view=login&return=' . $loginReturn,
                                        false
                                    );
                                    $loginLink = '<a href="' . $loginUrl . '">'
                                        . Lang::txt('COM_WIKI_LOGIN') . '</a>';
                                @endphp
                                <span>{!! Lang::txt('COM_WIKI_WARNING_LOGIN_REQUIRED', $loginLink) !!}</span>
                            </div>
                        @else
                            <form id="cform{{ $comment->get('id') }}"
                                  action="{{ Route::url($page->link('comments'), false) }}"
                                  method="post" enctype="multipart/form-data">
                                @php
                                    $replyTo = !$comment->get('anonymous')
                                        ? $name : Lang::txt('JANONYMOUS');
                                @endphp
                                <p class="text-sm font-medium mb-2">
                                    {!! Lang::txt('COM_WIKI_REPLYING_TO', $replyTo) !!}
                                </p>

                                <input type="hidden" name="comment[id]" value="0" />
                                <input type="hidden" name="comment[parent]"
                                       value="{{ $comment->get('id') }}" />
                                <input type="hidden" name="comment[page_id]"
                                       value="{{ $page->get('id') }}" />
                                <input type="hidden" name="comment[created]" value="" />
                                <input type="hidden" name="comment[created_by]"
                                       value="{{ User::get('id') }}" />
                                <input type="hidden" name="comment[version]"
                                       value="{{ $page->version->get('version') }}" />
                                <input type="hidden" name="comment[state]" value="1" />
                                <input type="hidden" name="pagename"
                                       value="{{ $page->pagename }}" />

                                @foreach($page->adapter()->routing('savecomment') as $rname => $val)
                                    <input type="hidden" name="{{ e($rname) }}"
                                           value="{{ e($val) }}" />
                                @endforeach

                                <x-form-field name="comment[ctext]"
                                              inputId="comment_{{ $comment->get('id') }}_content"
                                              :label="Lang::txt('COM_WIKI_ENTER_COMMENTS')">
                                    {!! \Components\Wiki\Helpers\Editor::getInstance()->display(
                                        'comment[ctext]',
                                        'comment_' . $comment->get('id') . '_content',
                                        '',
                                        'textarea textarea-bordered w-full',
                                        '35',
                                        '4'
                                    ) !!}
                                </x-form-field>

                                <x-form-field name="comment[anonymous]"
                                              inputId="comment-anonymous-{{ $comment->get('id') }}"
                                              :label="Lang::txt('COM_WIKI_POST_COMMENT_ANONYMOUSLY')"
                                              type="checkbox">
                                    <input type="checkbox" name="comment[anonymous]"
                                           id="comment-anonymous-{{ $comment->get('id') }}"
                                           class="checkbox checkbox-sm" value="1" />
                                </x-form-field>

                                {!! Html::input('token') !!}

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        {{ Lang::txt('COM_WIKI_SUBMIT') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Nested replies --}}
        @if($depth < $config->get('comments_depth', 3))
            @php
                $replies = $comment->replies()
                    ->whereIn('state', [
                        \Components\Wiki\Models\Comment::STATE_PUBLISHED,
                        \Components\Wiki\Models\Comment::STATE_FLAGGED,
                    ]);
                if ($version) {
                    $replies->whereEquals('version', $version);
                }
                $childComments = $replies->ordered()->rows();
            @endphp

            {!! $__view->view('_list')
                ->set('parent', $comment->get('id'))
                ->set('page', $page)
                ->set('option', $option)
                ->set('comments', $childComments)
                ->set('config', $config)
                ->set('depth', $depth)
                ->set('version', $version)
                ->set('cls_passed', $cls)
                ->loadTemplate() !!}
        @endif
    </div>
</li>
