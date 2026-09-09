{{--
 * Wiki comments — display list with comment form
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
@endphp

<x-page-container :title="e($page->title)">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu', 'pages')
                ->set('option', $option)
                ->set('controller', $controller)
                ->set('page', $page)
                ->set('task', $task)
                ->set('sub', $sub)
                ->loadTemplate() !!}
        @endslot
    @endif

    @if(count($parents))
        <p class="text-sm breadcrumbs mb-2">
            @foreach($parents as $parent)
                <a class="link link-hover"
                   href="{{ Route::url($parent->link(), false) }}">{{ $parent->title }}</a>
                <span class="mx-1">/</span>
            @endforeach
        </p>
    @endif

    @if(!$page->isStatic())
        {!! $__view->view('_authors', 'pages')
            ->set('page', $page)
            ->loadTemplate() !!}
    @endif

    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $__view->getError() }}</span>
        </div>
    @endif

    {!! $__view->view('_submenu', 'pages')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    <p class="text-sm text-base-content/60 mb-4">
        {{ Lang::txt('COM_WIKI_COMMENTS_EXPLANATION') }}
    </p>

    <h3 class="text-lg font-semibold mb-4" id="commentlist-title">
        {{ Lang::txt('COM_WIKI_COMMENTS') }}
    </h3>

    {{-- Version filter dropdown --}}
    <div class="mb-4">
        @php
            $url = $page->link('comments');
            $txt = Lang::txt('COM_WIKI_ALL');
            $versions = $page->versions()
                ->whereEquals('approved', 1)
                ->order('version', 'asc')
                ->rows();
            foreach ($versions as $ver) {
                if ($version == $ver->get('version')) {
                    $url = $page->link('comments') . '&version=' . $ver->get('version');
                    $txt = Lang::txt('COM_WIKI_VERSION_NUM', $ver->get('version'));
                }
            }
        @endphp
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-sm m-1">
                {{ e($txt) }}
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <ul tabindex="0"
                class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">
                @if($version)
                    <li>
                        <a href="{{ Route::url($page->link('comments'), false) }}">
                            {{ Lang::txt('COM_WIKI_ALL') }}
                        </a>
                    </li>
                @endif
                @foreach($versions as $ver)
                    @if($version == $ver->get('version'))
                        @continue
                    @endif
                    <li>
                        <a href="{{ Route::url($page->link('comments') . '&version=' . $ver->get('version'), false) }}">
                            {{ Lang::txt('COM_WIKI_VERSION_NUM', $ver->get('version')) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Comments list --}}
    @php
        $model = $page->comments()
            ->including(['creator', function ($creator) {
                $creator->select('*');
            }])
            ->whereIn('state', [
                \Components\Wiki\Models\Comment::STATE_PUBLISHED,
                \Components\Wiki\Models\Comment::STATE_FLAGGED,
            ]);
        if ($version) {
            $model->whereEquals('version', $version);
        }
        $comments = $model->ordered()->rows();
    @endphp

    @if($comments->count())
        {!! $__view->view('_list')
            ->set('parent', 0)
            ->set('page', $page)
            ->set('option', $option)
            ->set('comments', $comments)
            ->set('config', $config)
            ->set('depth', 0)
            ->set('version', $version)
            ->set('cls_passed', 'odd')
            ->loadTemplate() !!}
    @else
        <p class="text-base-content/60">
            {{ Lang::txt('COM_WIKI_NO_COMMENTS' . ($version ? '_FOR_VERSION' : '')) }}
        </p>
    @endif

    {{-- Add comment form --}}
    @if(isset($mycomment) && $mycomment instanceof \Components\Wiki\Models\Comment)
        <form action="{{ Route::url($page->link('comments'), false) }}"
              method="post" id="commentform" class="mt-8">
            <h3 class="text-lg font-semibold mb-4" id="commentform-title">
                {{ Lang::txt('COM_WIKI_ADD_COMMENT') }}
            </h3>

            <div class="flex gap-4">
                <div class="avatar">
                    <div class="w-10 h-10 rounded-full">
                        @php $anon = User::isGuest() ? 1 : 0; @endphp
                        <img src="{{ User::picture($anon) }}"
                             alt="{{ Lang::txt('COM_WIKI_MEMBER_PICTURE') }}" />
                    </div>
                </div>

                <div class="flex-1">
                    @if($page->config('comment_ratings') && !$mycomment->get('parent'))
                        <fieldset class="mb-4">
                            <legend class="font-medium text-sm mb-2">
                                {{ Lang::txt('COM_WIKI_FIELD_RATING') }}:
                            </legend>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="comment[rating]"
                                           class="mask mask-star-2 bg-warning"
                                           value="{{ $i }}"
                                           {{ $mycomment->get('rating') == $i ? 'checked' : '' }} />
                                @endfor
                            </div>
                        </fieldset>
                    @endif

                    <x-form-field name="comment[ctext]" inputId="ctext"
                                  :label="Lang::txt('COM_WIKI_FIELD_COMMENTS')">
                        {!! \Components\Wiki\Helpers\Editor::getInstance()->display(
                            'comment[ctext]',
                            'ctext',
                            $mycomment->get('ctext'),
                            'textarea textarea-bordered w-full',
                            '35',
                            '15'
                        ) !!}
                    </x-form-field>

                    <input type="hidden" name="comment[created]"
                           value="{{ e($mycomment->get('created')) }}" />
                    <input type="hidden" name="comment[id]"
                           value="{{ e($mycomment->get('id')) }}" />
                    <input type="hidden" name="comment[created_by]"
                           value="{{ e($mycomment->get('created_by')) }}" />
                    <input type="hidden" name="comment[state]"
                           value="{{ e($mycomment->get('state', 1)) }}" />
                    <input type="hidden" name="comment[version]"
                           value="{{ e($mycomment->get('version')) }}" />
                    <input type="hidden" name="comment[parent]"
                           value="{{ e($mycomment->get('parent')) }}" />
                    <input type="hidden" name="comment[page_id]"
                           value="{{ e($mycomment->get('page_id', $page->get('id'))) }}" />
                    <input type="hidden" name="pagename"
                           value="{{ e($page->pagename) }}" />

                    @foreach($page->adapter()->routing('savecomment') as $rname => $val)
                        <input type="hidden" name="{{ e($rname) }}"
                               value="{{ e($val) }}" />
                    @endforeach

                    <x-form-field name="comment[anonymous]"
                                  inputId="comment-anonymous"
                                  :label="Lang::txt('COM_WIKI_FIELD_ANONYMOUS')"
                                  type="checkbox">
                        <input type="checkbox" name="comment[anonymous]"
                               id="comment-anonymous" class="checkbox checkbox-sm"
                               value="1"
                               {{ $mycomment->get('anonymous') ? 'checked' : '' }} />
                    </x-form-field>

                    {!! Html::input('token') !!}

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            {{ Lang::txt('COM_WIKI_SUBMIT') }}
                        </button>
                    </div>

                    <div class="mt-4 p-3 bg-base-200 rounded-lg text-sm">
                        <p><strong>{{ Lang::txt('COM_WIKI_COMMENT_KEEP_RELEVANT') }}</strong></p>
                        <p class="mt-1">{{ Lang::txt('COM_WIKI_COMMENT_FORMATTING_HINT') }}</p>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-page-container>
