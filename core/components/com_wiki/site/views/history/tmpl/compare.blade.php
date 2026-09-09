{{--
 * Wiki history — side-by-side diff comparison
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;

    $orauthor = $or->creator()->get('name', Lang::txt('COM_WIKI_UNKNOWN'));
    $drauthor = $dr->creator()->get('name', Lang::txt('COM_WIKI_UNKNOWN'));
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <dl class="space-y-2">
                <dt class="font-semibold">
                    {{ Lang::txt('COM_WIKI_VERSION') }} {{ $or->get('version') }}
                </dt>
                <dd class="text-sm text-base-content/60">
                    {!! Lang::txt(
                        'COM_WIKI_HISTORY_CREATED_BY',
                        '<time datetime="' . $or->get('created') . '">' . $or->get('created') . '</time>',
                        e($orauthor)
                    ) !!}
                </dd>

                <dt class="font-semibold">
                    {{ Lang::txt('COM_WIKI_VERSION') }} {{ $dr->get('version') }}
                </dt>
                <dd class="text-sm text-base-content/60">
                    {!! Lang::txt(
                        'COM_WIKI_HISTORY_CREATED_BY',
                        '<time datetime="' . $dr->get('created') . '">' . $dr->get('created') . '</time>',
                        e($drauthor)
                    ) !!}
                </dd>
            </dl>
        </div>
        <div>
            <p class="text-error">{{ Lang::txt('COM_WIKI_HISTORY_DELETIONS') }}</p>
            <p class="text-success">{{ Lang::txt('COM_WIKI_HISTORY_ADDITIONS') }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        {!! $content !!}
    </div>
</x-page-container>
