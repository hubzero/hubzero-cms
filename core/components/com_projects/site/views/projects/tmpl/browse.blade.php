{{--
 * Project browse page with search, sort, and filter
 *
 * Variables:
 *   $title   - Page title
 *   $option  - Component option string
 *   $model   - Projects model
 *   $filters - Active filters array
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->css()->js();

    if (in_array($filters['reviewer'], ['sponsored', 'sensitive'])) {
        $__view->css('reviewers')
             ->css('jquery.fancybox.css', 'system');
    }

    $sortbyDir = $filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
    $total = $model->entries('count', $filters);
    $rows = $model->entries('list', $filters);

    // Query string for sort links
    $qs  = $filters['search'] ? '&search=' . e($filters['search']) : '';
    $qs .= $filters['start']  ? '&limitstart=' . e($filters['start']) : '';
    $qs .= $filters['limit']  ? '&limit=' . e($filters['limit']) : '';
    $qs .= '&sortdir=' . $sortbyDir;
    if ($filters['reviewer']) {
        $qs .= '&reviewer=' . $filters['reviewer'];
    }

    $startUrl = Route::url('index.php?option=' . $option . '&task=start');
    $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
@endphp

<x-page-container :title="$title">
    @if (User::authorise('core.create', $option))
        @slot('actions')
            <a class="btn btn-primary" href="{{ $startUrl }}">
                {{ Lang::txt('COM_PROJECTS_START_NEW') }}
            </a>
        @endslot
    @endif

    {{-- Search --}}
    <x-search-bar
        :action="$browseUrl"
        :query="$filters['search']"
        :placeholder="Lang::txt('COM_PROJECTS_ENTER_PHRASE')"
        :label="Lang::txt('COM_PROJECTS_SEARCH')"
        :buttonLabel="Lang::txt('COM_PROJECTS_SEARCH')"
        :clearUrl="$browseUrl"
        :clearLabel="Lang::txt('JCLEAR')"
        name="search">
        <input type="hidden" name="sortby" value="{{ e($filters['sortby']) }}" />
        @if (!empty($filters['reviewer']))
            <input type="hidden" name="reviewer" value="{{ $filters['reviewer'] }}" />
        @endif
    </x-search-bar>

    {{-- Sort and filter controls --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        {{-- Sort tabs --}}
        <nav
            class="flex items-center gap-1"
            aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}"
        >
            <span class="text-sm text-muted-foreground mr-1">
                {{ Lang::txt('COM_PROJECTS_SORT_BY') }}:
            </span>
            @if (!empty($filters['reviewer']) && strtolower($filters['reviewer']) == 'sponsored')
                @php
                    $grantUrl = Route::url(
                        'index.php?option=' . $option . '&task=browse&sortby=grant_status' . $qs
                    );
                @endphp
                <a href="{{ $grantUrl }}"
                   class="btn btn-sm {{ $filters['sortby'] == 'grant_status' ? 'btn-active' : 'btn-ghost' }}"
                   title="{{ Lang::txt('COM_PROJECTS_SORT_BY') }} {{ Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS') }}"
                >&darr; {{ Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS') }}</a>
            @endif

            @php
                $ownerUrl = Route::url(
                    'index.php?option=' . $option . '&task=browse&sortby=owner' . $qs
                );
                $titleUrl = Route::url(
                    'index.php?option=' . $option . '&task=browse&sortby=title' . $qs
                );
            @endphp
            <a href="{{ $ownerUrl }}"
               class="btn btn-sm {{ $filters['sortby'] == 'owner' ? 'btn-active' : 'btn-ghost' }}"
               title="{{ Lang::txt('COM_PROJECTS_SORT_BY') }} {{ Lang::txt('COM_PROJECTS_OWNER') }}"
            >&darr; {{ Lang::txt('COM_PROJECTS_OWNER') }}</a>
            <a href="{{ $titleUrl }}"
               class="btn btn-sm {{ $filters['sortby'] == 'title' ? 'btn-active' : 'btn-ghost' }}"
               title="{{ Lang::txt('COM_PROJECTS_SORT_BY') }} {{ Lang::txt('COM_PROJECTS_TITLE') }}"
            >&darr; {{ Lang::txt('COM_PROJECTS_TITLE') }}</a>
        </nav>

        {{-- Filter select --}}
        <div class="flex items-center gap-2">
            <label for="filterby" class="text-sm text-muted-foreground">
                {{ Lang::txt('COM_PROJECTS_BROWSE_SHOW') }}
            </label>
            <select
                name="filterby"
                id="filterby"
                class="select select-bordered select-sm"
            >
                <option
                    value="all"
                    @selected($filters['filterby'] == 'all')
                >{{ Lang::txt('COM_PROJECTS_FILTER_ALL') }}</option>
                <option
                    value="archived"
                    @selected($filters['filterby'] == 'archived')
                >{{ Lang::txt('COM_PROJECTS_FILTER_ARCHIVED') }}</option>
                @if (in_array($filters['reviewer'], ['sponsored', 'sensitive']))
                    <option
                        value="pending"
                        @selected($filters['filterby'] == 'pending')
                    >{{ Lang::txt('COM_PROJECTS_FILTER_PENDING') }}</option>
                @endif
            </select>
        </div>
    </div>

    {{-- Results --}}
    @if (count($rows))
        @include('projects::_list', [
            'rows'    => $rows,
            'filters' => $filters,
            'model'   => $model,
            'option'  => $option,
        ])

        {{-- Pagination --}}
        @php
            $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
            $pagenavhtml = $pageNav->render();
            $pagenavhtml = str_replace('projects/?', 'projects/browse/?', $pagenavhtml);
        @endphp
        <nav aria-label="Page navigation" class="flex justify-center mt-8">
            {!! $pagenavhtml !!}
        </nav>
    @else
        {{-- Empty state --}}
        <div class="text-center py-12" role="status">
            @if (User::isGuest())
                @php
                    $loginUrl = Route::url(
                        'index.php?option=' . $option . '&task=browse&action=login'
                    );
                @endphp
                <p class="text-muted-foreground mb-4">
                    {{ Lang::txt('COM_PROJECTS_NO_PROJECTS_FOUND') }}
                    {{ Lang::txt('COM_PROJECTS_PLEASE') }}
                    <a class="link link-primary" href="{{ $loginUrl }}">
                        {{ Lang::txt('COM_PROJECTS_LOGIN') }}
                    </a>
                    {{ Lang::txt('COM_PROJECTS_TO_VIEW_PRIVATE_PROJECTS') }}
                </p>
            @elseif (in_array($filters['reviewer'], ['sponsored', 'sensitive']))
                <p class="text-muted-foreground">
                    @if ($filters['filterby'] == 'pending')
                        {{ Lang::txt('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_PENDING') }}
                    @else
                        {{ Lang::txt('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_ALL') }}
                    @endif
                </p>
            @else
                <p class="text-muted-foreground">
                    {{ Lang::txt('COM_PROJECTS_NO_AUTHOROZED_PROJECTS_FOUND') }}
                </p>
            @endif
        </div>
    @endif

</x-page-container>
