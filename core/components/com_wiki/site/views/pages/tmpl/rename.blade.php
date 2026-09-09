{{--
 * Wiki page — rename form
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

    @if($page->exists())
        {!! $__view->view('_submenu')
            ->set('option', $option)
            ->set('controller', $controller)
            ->set('page', $page)
            ->set('task', $task)
            ->set('sub', $sub)
            ->loadTemplate() !!}
    @endif

    @if($page->isLocked() && !$page->access('manage'))
        <div role="alert" class="alert alert-warning">
            <span>{{ Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR') }}</span>
        </div>
    @else
        <form action="{{ Route::url($page->link('base'), false) }}"
              method="post" id="hubForm" class="max-w-2xl">
            <x-form-section :heading="Lang::txt('COM_WIKI_CHANGE_PAGENAME')">
                <p class="text-sm text-base-content/60 mb-3">
                    {{ Lang::txt('COM_WIKI_PAGENAME_EXPLANATION') }}
                </p>

                <x-form-field name="newpagename" inputId="newpagename"
                              :label="Lang::txt('COM_WIKI_FIELD_PAGENAME')"
                              :hint="Lang::txt('COM_WIKI_FIELD_PAGENAME_HINT')">
                    <input type="text" name="newpagename" id="newpagename"
                           class="input input-bordered w-full"
                           value="{{ e($page->get('pagename')) }}" />
                </x-form-field>
            </x-form-section>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    {{ Lang::txt('COM_WIKI_SUBMIT') }}
                </button>
            </div>

            <input type="hidden" name="oldpagename"
                   value="{{ e($page->get('pagename')) }}" />
            <input type="hidden" name="page_id"
                   value="{{ e($page->get('id')) }}" />
            @foreach($page->adapter()->routing('saverename') as $name => $val)
                <input type="hidden" name="{{ e($name) }}" value="{{ e($val) }}" />
            @endforeach
            {!! Html::input('token') !!}
        </form>
    @endif
</x-page-container>
