{{--
  Curation queue — filterable, sortable list of publications pending curation.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $filters    — array: sortby, sortdir, curator, start, limit, master_type, tag, category
    $rows       — result set
    $total      — total result count
    $pageNav    — Paginator instance
    $database   — database driver
    $authorized — user authorization level (curator, admin, limited)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Sorting and paging
  $sortbyDir  = $filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
  $whatsleft  = $total - $filters['start'] - $filters['limit'];
  $prev_start = $filters['start'] - $filters['limit'];
  $prev_start = $prev_start < 0 ? 0 : $prev_start;
  $next_start = $filters['start'] + $filters['limit'];

  // URL
  $route = 'index.php?option=' . $option . '&controller=curation';

  $pa = new \Components\Publications\Tables\Author($database);

  $__view->css()
      ->js()
      ->css('jquery.fancybox.css', 'system')
      ->css('curation.css')
      ->js('curation.js');

  // Sort options for tabs
  $sortOptions = [
      'id'        => Lang::txt('COM_PUBLICATIONS_CURATION_ID'),
      'title'     => Lang::txt('COM_PUBLICATIONS_CURATION_TITLE'),
      'submitted' => Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'),
      'status'    => Lang::txt('COM_PUBLICATIONS_CURATION_STATUS'),
      'type'      => Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'),
  ];
@endphp

<x-page-container :title="$title">

  <p>{{ Lang::txt('COM_PUBLICATIONS_CURATION_LIST_INSTRUCT') }}</p>

  <div class="space-y-4">
    {{-- Filter and sort tabs --}}
    <nav aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}"
         class="flex flex-wrap items-center justify-between gap-4">

      {{-- Filter tabs: All / Assigned to me --}}
      <div role="tablist" class="tabs tabs-border">
        @php
          $allUrl = Route::url($route);
          $assignedUrl = Route::url($route . '&assigned=1');
          $isOwner = ($filters['curator'] == 'owner');
        @endphp
        <a role="tab"
           class="tab {{ !$isOwner ? 'tab-active' : '' }}"
           href="{{ $allUrl }}"
           aria-selected="{{ !$isOwner ? 'true' : 'false' }}">
          {{ Lang::txt('All') }}
        </a>
        <a role="tab"
           class="tab {{ $isOwner ? 'tab-active' : '' }}"
           href="{{ $assignedUrl }}"
           aria-selected="{{ $isOwner ? 'true' : 'false' }}">
          {{ Lang::txt('Assigned to me') }}
        </a>
      </div>

      {{-- Sort tabs --}}
      <div role="tablist" class="tabs tabs-border">
        @foreach ($sortOptions as $sortKey => $sortLabel)
          @php
            $sortUrl = Route::url($route . '&t_sortby=' . $sortKey . '&t_sortdir=' . $sortbyDir);
            $isActive = ($filters['sortby'] == $sortKey);
          @endphp
          <a role="tab"
             class="tab {{ $isActive ? 'tab-active' : '' }}"
             href="{{ $sortUrl }}"
             title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') }} {{ $sortLabel }}"
             aria-selected="{{ $isActive ? 'true' : 'false' }}">
            {{ $sortLabel }}
          </a>
        @endforeach
      </div>
    </nav>

    {{-- Results table --}}
    @if (count($rows) > 0)
      <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
          <thead>
            <tr>
              <th>#</th>
              <th></th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_TITLE') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_STATUS') }}</th>
              <th>{{ Lang::txt('COM_PUBLICATIONS_CURATION_CURATOR') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rows as $row)
              @php
                $submitted  = $row->reviewed && $row->state == 5
                    ? strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_RESUBMITTED'))
                    : strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'));
                $submittedDate = Date::of($row->submitted)->toLocal('M d, Y');

                // Get submitter
                $submitter  = $pa->getSubmitter($row->version_id, $row->created_by);
                $submitter->name = $submitter->name ?: Lang::txt('JUNKNOWN');

                // Reviewed info (for state 7)
                $reviewed = '';
                if ($row->state == 7 && !empty($row->reviewed_by)) {
                    $reviewed = strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_REVIEWED'))
                        . ' ' . Date::of($row->reviewed)->toLocal('M d, Y');
                    $reviewer = User::getInstance($row->reviewed_by);
                    $reviewerName = $reviewer->get('name') ?: Lang::txt('JUNKNOWN');
                    $reviewed .= ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $reviewerName);
                }

                // Status
                $statusClass = $row->state == 5 ? 'badge-warning' : 'badge-info';
                $statusTxt = $row->state == 5
                    ? Lang::txt('COM_PUBLICATIONS_CURATION_STATUS_PENDING')
                    : Lang::txt('COM_PUBLICATIONS_CURATION_PENDING_AUTHOR_CHANGES');

                $abstract = $row->abstract ? stripslashes($row->abstract) : '';

                // Is user authorized to edit assignment?
                $assign = (
                    $authorized == 'curator'
                    || $authorized == 'admin'
                    || (
                        $authorized == 'limited'
                        && in_array($row->master_type, $filters['master_type'])
                    )
                );

                $thumbUrl = Route::url(
                    'index.php?option=com_publications&id='
                    . $row->id . '&v=' . $row->version_id
                ) . '/Image:thumb';

                $assignUrl = Route::url(
                    $route . '&id=' . $row->id
                    . '&task=assign&vid=' . $row->version_id
                    . '&ajax=1&no_html=1'
                );
              @endphp

              <tr id="tr_{{ $row->id }}">
                <td>{{ $row->id }}</td>
                <td>
                  <img class="size-8 rounded" src="{{ $thumbUrl }}" alt="" />
                </td>
                <td>
                  @if ($row->state == 5)
                    <a href="{{ Route::url($route . '&id=' . $row->id) }}"
                       @if ($abstract)
                         title="{{ e($abstract) }}"
                       @endif
                       class="link link-hover">
                      {{ e($row->title) }}
                    </a>
                  @else
                    {{ e($row->title) }}
                  @endif
                  <br />
                  <span class="text-sm text-base-content/70">
                    {!! $submitted !!}
                    <span class="font-semibold">{{ $submittedDate }}</span>
                    {{ Lang::txt('COM_PUBLICATIONS_CURATION_BY', $submitter->name) }}
                  </span>
                </td>
                <td>v.{{ $row->version_label }}</td>
                <td>
                  <span class="icon {{ $row->base }}"></span>
                  {{ $row->base }}
                </td>
                <td>
                  <time>{{ $submittedDate }}</time>
                  @if ($row->reviewed && $row->state == 5)
                    <span class="item-updated"></span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-sm {{ $statusClass }}">{{ $statusTxt }}</span>
                </td>
                <td>
                  @php
                    $owner = $row->curator ? User::getInstance($row->curator) : null;
                  @endphp
                  @if ($owner)
                    {{ Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGNED_TO') }}
                    @if ($assign)
                      <a href="{{ $assignUrl }}"
                         class="fancybox link"
                         title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_CHANGE_ASSIGNMENT') }}">
                        {{ e($owner->get('name')) }}
                      </a>
                    @else
                      {{ e($owner->get('name')) }}
                    @endif
                  @elseif ($assign)
                    <a href="{{ $assignUrl }}"
                       class="btn btn-sm btn-ghost fancybox"
                       title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN') }}">
                      {{ Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN') }}
                    </a>
                  @endif
                </td>
                <td class="whitespace-nowrap">
                  @if ($row->state == 5)
                    @php
                      $reviewUrl = Route::url(
                          $route . '&id=' . $row->id . '&vid=' . $row->version_id
                      );
                    @endphp
                    <a href="{{ $reviewUrl }}"
                       class="btn btn-sm btn-primary"
                       title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_OVER_REVIEW') }}">
                      {{ Lang::txt('COM_PUBLICATIONS_CURATION_REVIEW') }}
                    </a>
                  @endif

                  @if ($row->state == 7)
                    {!! $reviewed !!}
                  @endif

                  @php
                    $histUrl = Route::url(
                        $route . '&id=' . $row->id
                        . '&task=history&ajax=1&no_html=1'
                    );
                    $pubUrl = Route::url(
                        'index.php?option=com_publications&id='
                        . $row->id . '&v=' . $row->version_number
                    );
                  @endphp
                  <a href="{{ $histUrl }}"
                     class="btn btn-sm btn-ghost fancybox"
                     title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_OVER_HISTORY') }}">
                    {{ Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY') }}
                  </a>
                  <a href="{{ $pubUrl }}"
                     class="public-page"
                     title="{{ Lang::txt('COM_PUBLICATIONS_CURATION_VIEW_PUB_PAGE') }}">
                    &nbsp;
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <nav aria-label="{{ Lang::txt('JGLOBAL_PAGINATION') }}" class="flex justify-center mt-8">
        @php
          $pn = $pageNav->render();
          $pn = str_replace('/?/&amp;', '/?', $pn);
          $f = 'task=display';
          foreach ($filters as $k => $v) {
              $f .= ($v && ($k == 'tag' || $k == 'category')) ? '&amp;' . $k . '=' . $v : '';
          }
          $pn = str_replace('?', '?' . $f . '&amp;', $pn);
        @endphp
        {!! $pn !!}
      </nav>
    @else
      <x-empty-state
          :title="Lang::txt('COM_PUBLICATIONS_CURATION_NO_RESULTS')"
          :message="Lang::txt('COM_PUBLICATIONS_CURATION_NO_RESULTS')" />
    @endif
  </div>

</x-page-container>
