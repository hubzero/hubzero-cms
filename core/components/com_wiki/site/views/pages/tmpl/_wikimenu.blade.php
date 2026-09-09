{{--
 * Wiki sidebar navigation menu (non-sub mode only)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
@endphp

<x-sidebar-card :title="Lang::txt('COM_WIKI_SEARCH')">
    <form action="{{ Route::url($page->link('base') . '&pagename=Special:Search', false) }}"
          method="get">
        <div class="join w-full">
            <input type="text" name="q"
                   class="input input-bordered join-item w-full input-sm"
                   placeholder="{{ Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER') }}" />
            <button type="submit" class="btn btn-sm join-item">
                {{ Lang::txt('COM_WIKI_GO') }}
            </button>
        </div>
    </form>
</x-sidebar-card>

<x-sidebar-card :title="Lang::txt('COM_WIKI')">
    <ul class="menu menu-sm p-0">
        <li>
            <a href="{{ Route::url($page->link('base'), false) }}">
                {{ Lang::txt('COM_WIKI_MAIN_PAGE') }}
            </a>
        </li>
        <li>
            <a href="{{ Route::url($page->link('base') . '&pagename=Help:Index', false) }}">
                {{ Lang::txt('COM_WIKI_HELP') }}
            </a>
        </li>
        <li>
            <a href="{{ Route::url($page->link('base') . '&pagename=Special:AllPages', false) }}">
                {{ Lang::txt('COM_WIKI_PAGE_INDEX') }}
            </a>
        </li>
        <li>
            <a href="{{ Route::url($page->link('base') . '&pagename=Special:RecentChanges', false) }}">
                {{ Lang::txt('COM_WIKI_SPECIAL_RECENT_CHANGES') }}
            </a>
        </li>
    </ul>
</x-sidebar-card>

@if($page->getNamespace() != 'special')
    <x-sidebar-card :title="Lang::txt('COM_WIKI_TOOLS')">
        <ul class="menu menu-sm p-0">
            <li>
                @php
                    $linksUrl = Route::url(
                        $page->link('base')
                        . '&pagename=Special:Links&page=' . $page->get('pagename')
                        . '&version=' . $page->get('version_id'),
                        false
                    );
                @endphp
                <a href="{{ $linksUrl }}">
                    {{ Lang::txt('COM_WIKI_SPECIAL_LINKS') }}
                </a>
            </li>
            <li>
                @php
                    $citeUrl = Route::url(
                        $page->link('base')
                        . '&pagename=Special:Cite&page=' . $page->get('pagename')
                        . '&version=' . $page->get('version_id'),
                        false
                    );
                @endphp
                <a href="{{ $citeUrl }}">
                    {{ Lang::txt('COM_WIKI_SPECIAL_CITE') }}
                </a>
            </li>
            <li>
                <a href="{{ Route::url($page->link('pdf'), false) }}">
                    {{ Lang::txt('COM_WIKI_TAB_PDF') }}
                </a>
            </li>
            @if(!User::isGuest() && $page->access('create'))
                <li>
                    @php
                        $newParam = ($page->get('scope') != 'site') ? 'action' : 'task';
                    @endphp
                    <a href="{{ Route::url($page->link('base') . '&' . $newParam . '=new', false) }}">
                        {{ Lang::txt('COM_WIKI_NEW_PAGE') }}
                    </a>
                </li>
            @endif
        </ul>
    </x-sidebar-card>
@elseif(!User::isGuest() && $page->access('create'))
    <x-sidebar-card :title="Lang::txt('COM_WIKI_TOOLS')">
        <ul class="menu menu-sm p-0">
            <li>
                @php
                    $newParam = ($page->get('scope') != 'site') ? 'action' : 'task';
                @endphp
                <a href="{{ Route::url($page->link('base') . '&' . $newParam . '=new', false) }}">
                    {{ Lang::txt('COM_WIKI_NEW_PAGE') }}
                </a>
            </li>
        </ul>
    </x-sidebar-card>
@endif
