{{--
 * Wiki page display — default (non-static) layout
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
@endphp

<x-page-container :title="$page->title">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu')
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
        {!! $__view->view('_authors')
            ->set('page', $page)
            ->loadTemplate() !!}
    @endif

    {!! $page->event->afterDisplayTitle !!}

    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $__view->getError() }}</span>
        </div>
    @endif

    {!! $__view->view('_submenu')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    {!! $page->event->beforeDisplayContent !!}

    <article class="prose max-w-none">
        {!! $revision->get('pagehtml') !!}

        @php
            $revCreatedTime = '<time datetime="' . $revision->created() . '">'
                . $revision->created('date') . '</time>';
        @endphp
        <p class="text-sm text-base-content/50 mt-6">
            {!! Lang::txt('COM_WIKI_PAGE_CREATED')
                . ' <time datetime="' . $page->created() . '">'
                . $page->created('date') . '</time>, '
                . Lang::txt('COM_WIKI_PAGE_LAST_MODIFIED')
                . ' ' . $revCreatedTime !!}
        </p>

        @if($page->tags('cloud'))
            <div class="mt-4">
                <h3 class="text-base font-semibold mb-2">
                    {{ Lang::txt('COM_WIKI_PAGE_TAGS') }}
                </h3>
                {!! $page->tags('cloud') !!}
            </div>
        @endif
    </article>

    {!! $page->event->afterDisplayContent !!}
</x-page-container>
