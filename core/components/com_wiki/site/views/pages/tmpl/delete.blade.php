{{--
 * Wiki page — delete confirmation
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
@endphp

<x-page-container :title="e($page->title)">
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

    @if($page->isLocked() && !$page->access('manage'))
        <div role="alert" class="alert alert-warning">
            <span>{{ Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR') }}</span>
        </div>
    @else
        <form action="{{ Route::url($page->link('base'), false) }}"
              method="post" id="hubForm" class="max-w-2xl">
            <x-form-section :heading="Lang::txt('COM_WIKI_DELETE_PAGE')">
                <x-form-field name="confirm" inputId="confirm-delete"
                              :label="Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE')"
                              type="checkbox">
                    <input type="checkbox" name="confirm" id="confirm-delete"
                           class="checkbox" value="1" />
                </x-form-field>

                <div role="alert" class="alert alert-warning">
                    <span>{{ Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE_HINT') }}</span>
                </div>
            </x-form-section>

            <div class="form-actions">
                <button type="submit" class="btn btn-error">
                    {{ Lang::txt('COM_WIKI_DELETE') }}
                </button>
            </div>

            @php
                $pagenameVal = ($page->get('path') ? $page->get('path') . '/' : '')
                    . $page->get('pagename');
            @endphp
            <input type="hidden" name="pagename" value="{{ e($pagenameVal) }}" />
            <input type="hidden" name="page_id" value="{{ e($page->get('id')) }}" />
            @foreach($page->adapter()->routing('delete') as $name => $val)
                <input type="hidden" name="{{ e($name) }}" value="{{ e($val) }}" />
            @endforeach
            {!! Html::input('token') !!}
        </form>
    @endif
</x-page-container>
