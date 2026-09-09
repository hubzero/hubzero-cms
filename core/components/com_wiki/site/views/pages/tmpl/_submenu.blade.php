{{--
 * Wiki page sub-navigation tabs
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $tmpl = Request::getWord('tmpl');
    $controller = $controller ?? Request::getWord('controller', 'page');
@endphp

@if($tmpl != 'component' && $sub)
    <div class="flex flex-wrap gap-2 mb-4">
        @if(!User::isGuest() && $page->access('create'))
            <a class="btn btn-sm btn-primary"
               href="{{ Route::url($page->link('base') . '&action=new', false) }}">
                {{ Lang::txt('COM_WIKI_NEW_PAGE') }}
            </a>
        @endif
        <a class="btn btn-sm"
           href="{{ Route::url($page->link('base') . '&pagename=Special:AllPages', false) }}">
            {{ Lang::txt('COM_WIKI_INDEX') }}
        </a>
        <a class="btn btn-sm"
           href="{{ Route::url($page->link('base') . '&pagename=Special:Search', false) }}">
            {{ Lang::txt('COM_WIKI_SEARCH') }}
        </a>
    </div>
@endif

<div role="tablist" class="tabs tabs-border mb-4">
    @php
        $articleActive = ($controller == 'pages' && ($task == 'display' || !$task));
    @endphp
    <a role="tab"
       class="tab{{ $articleActive ? ' tab-active' : '' }}"
       href="{{ Route::url($page->link(), false) }}">
        {{ Lang::txt('COM_WIKI_TAB_ARTICLE') }}
    </a>

    @if($tmpl != 'component')
        @if($page->exists() && !$page->isDeleted() && $page->getNamespace() != 'special')
            @php
                $canEdit = ($page->isLocked() && $page->access('manage'))
                    || ((!$page->isLocked() && $page->access('edit')
                        && $page->created_by == User::get('id'))
                        || $page->access('manage'));
            @endphp

            @if($canEdit && $page->getNamespace() != 'help')
                @php
                    $editActive = ($controller == 'pages'
                        && in_array($task, ['edit', 'preview', 'save']));
                @endphp
                <a role="tab"
                   class="tab{{ $editActive ? ' tab-active' : '' }}"
                   href="{{ Route::url($page->link('edit'), false) }}">
                    {{ Lang::txt('COM_WIKI_TAB_EDIT') }}
                </a>
            @endif

            @if($page->config('comments', 1) && $page->access('view', 'comment'))
                <a role="tab"
                   class="tab{{ $controller == 'comments' ? ' tab-active' : '' }}"
                   href="{{ Route::url($page->link('comments'), false) }}">
                    {{ Lang::txt('COM_WIKI_TAB_COMMENTS') }}
                </a>
            @endif

            <a role="tab"
               class="tab{{ $controller == 'history' ? ' tab-active' : '' }}"
               href="{{ Route::url($page->link('history'), false) }}">
                {{ Lang::txt('COM_WIKI_TAB_HISTORY') }}
            </a>

            @if($page->get('scope') != 'site')
                <a role="tab" class="tab"
                   href="{{ Route::url($page->link('pdf'), false) }}">
                    {{ Lang::txt('COM_WIKI_TAB_PDF') }}
                </a>
            @endif

            @php
                $canDelete = ($page->isLocked() && $page->access('manage', 'page'))
                    || ((!$page->isLocked() && $page->access('delete', 'page')
                        && $page->created_by == User::get('id'))
                        || $page->access('manage'));
            @endphp
            @if($canDelete)
                <a role="tab"
                   class="tab{{ ($controller == 'pages' && $task == 'delete') ? ' tab-active' : '' }}"
                   href="{{ Route::url($page->link('delete'), false) }}">
                    {{ Lang::txt('COM_WIKI_DELETE_PAGE') }}
                </a>
            @endif
        @endif
    @endif
</div>
