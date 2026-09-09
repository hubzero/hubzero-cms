{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css();
@endphp

<h3 class="text-xl font-bold mb-4">{{ Lang::txt('PLG_GROUPS_PROJECTS') }}</h3>

@if ($__view->getError())
    <div class="alert alert-error" role="alert">{{ $__view->getError() }}</div>
@endif

@if ($group->published == 1 && User::authorise('core.create', 'com_projects'))
    @php
    $startUrl = Route::url(
        'index.php?option=com_projects&task=start&gid='
        . $group->get('gidNumber')
    );
    @endphp
    <ul id="page_options" class="pluginOptions mb-4">
        <li>
            <a class="btn btn-primary showinbox"
                href="{{ $startUrl }}">
                {{ Lang::txt('PLG_GROUPS_PROJECTS_ADD') }}
            </a>
        </li>
    </ul>
@endif

@php
$__view->view('submenu', 'partials')
    ->set('group', $group)
    ->set('projectcount', $projectcount)
    ->set('newcount', $newcount)
    ->set('tab', 'all')
    ->display();
@endphp

<section class="main section" id="s-projects">
    <div class="container">
        <nav class="entries-filters" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
            <ul class="entries-menu filter-options flex gap-2 mb-4">
                @php
                $isActive = (!$filters['filterby'] || $filters['filterby'] == 'active');
                $allUrl = Route::url(
                    'index.php?option=com_groups&cn='
                    . $group->get('cn')
                    . '&active=projects&action=all'
                );
                @endphp
                <li>
                    <a class="btn btn-sm {{ $isActive ? 'btn-active' : 'btn-ghost' }}"
                        data-status="all"
                        href="{{ $allUrl }}">
                        {{ Lang::txt('PLG_GROUPS_PROJECTS_FILTER_STATUS_ACTIVE') }}
                    </a>
                </li>
                @php
                $isArchived = ($filters['filterby'] == 'archived');
                $archivedUrl = Route::url(
                    'index.php?option=com_groups&cn='
                    . $group->get('cn')
                    . '&active=projects&action=all&filterby=archived'
                );
                @endphp
                <li>
                    <a class="btn btn-sm {{ $isArchived ? 'btn-active' : 'btn-ghost' }}"
                        data-status="manager"
                        href="{{ $archivedUrl }}">
                        {{ Lang::txt('PLG_GROUPS_PROJECTS_FILTER_STATUS_ARCHIVED') }}
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Placeholder for Group Dashboard --}}
        @php
        $dashboards = Event::trigger('groups.onGroupClassroomprojects', array($group));
        foreach ($dashboards as $dashboard) {
            echo $dashboard;
        }
        @endphp
        {{-- End placeholder for Group Dashboard --}}

        @if ($which == 'all')
            @php
            $__view->view('list')
                 ->set('option', $option)
                 ->set('rows', $owned)
                 ->set('config', $config)
                 ->set('which', 'owned')
                 ->display();
            @endphp

            </div><div class="container">
        @endif

        @php
        $__view->view('list')
             ->set('option', $option)
             ->set('rows', $rows)
             ->set('config', $config)
             ->set('which', $filters['which'])
             ->display();
        @endphp
    </div>
</section>{{-- /.main section --}}
