{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();
@endphp

@if ($group->published == 1 && $authorized == 'manager')
    <ul id="page_options">
        <li>
            @php
            $newUrl = Route::url(
                'index.php?option=' . $option
                . '&cn=' . $group->cn
                . '&active=announcements&action=new'
            );
            @endphp
            <a class="btn btn-primary gap-2" href="{{ $newUrl }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NEW') }}
            </a>
        </li>
    </ul>
@endif

<section class="main section">
    @if ($__view->getError())
        <div class="alert alert-error">
            <p>{{ $__view->getError() }}</p>
        </div>
    @endif

    @php
    $formAction = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->get('cn')
        . '&active=announcements'
    );
    @endphp
    <form action="{{ $formAction }}" method="get">
        <div class="container data-entry">
            <fieldset class="entry-search">
                <legend>{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LEGEND') }}</legend>
                <label for="entry-search-field" class="sr-only">
                    {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LABEL') }}
                </label>
                <div class="join w-full">
                    <input type="text"
                        name="q"
                        id="entry-search-field"
                        class="input input-bordered join-item flex-1"
                        value="{{ e($filters['search']) }}"
                        placeholder="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_PLACEHOLDER') }}" />
                    <button type="submit" class="btn btn-neutral join-item">
                        {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH') }}
                    </button>
                </div>
            </fieldset>
        </div>

        <div class="acontainer">
            @if ($rows->count() > 0)
                @foreach ($rows as $row)
                    @php
                        $__view->view('item')
                            ->set('option', $option)
                            ->set('group', $group)
                            ->set('authorized', $authorized)
                            ->set('announcement', $row)
                            ->set('showClose', false)
                            ->display();
                    @endphp
                @endforeach
            @else
                <div class="alert alert-warning">
                    <p>{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NO_RESULTS') }}</p>
                </div>
            @endif

            @php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'announcements');
            echo $pageNav;
            @endphp
            <div class="clearfix"></div>
        </div>
    </form>
</section>
