{{--
  Member Courses — course list with role filter and sort.

  Variables from plugin (onMembers):
    $member   — member profile object
    $roles    — array of course role objects (total, alias, title)
    $hasRoles — bool/count of user roles
    $filters  — array with task, sort, start, limit
    $total    — total course count
    $results  — array of course row objects
    $active   — active role object
    $option   — component option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();

  $base = $member->link() . '&active=courses';

  // Pagination range
  $rangeStart = ($total > 0) ? $filters['start'] + 1 : 0;
  $rangeEnd   = ($total > ($filters['start'] + $filters['limit']))
      ? ($filters['start'] + $filters['limit'])
      : $total;

  $rtrn = base64_encode(Request::getString('REQUEST_URI', $base, 'server'));
@endphp

<h3 class="text-lg font-semibold mb-4">
  {{ Lang::txt('PLG_MEMBERS_COURSES') }}
</h3>

@if ($hasRoles)
  <div id="courses-container">
    <form method="get" action="{{ Route::url($base) }}">
      {{-- Filters --}}
      <nav class="mb-4 flex flex-wrap gap-4 justify-between"
           aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">

        {{-- Role filter --}}
        @if ($roles && $hasRoles > 1)
          <div role="tablist" class="tabs tabs-border">
            @foreach ($roles as $s)
              @if ($s->total <= 0)
                @continue
              @endif
              @php
                $isActive = ($filters['task'] === $s->alias);
                $roleUrl  = Route::url($base . '&task=' . $s->alias . '&sort=' . $filters['sort']);
              @endphp
              <a role="tab" class="tab {{ $isActive ? 'tab-active' : '' }}"
                 href="{{ $roleUrl }}"
                 title="{{ e(stripslashes($s->title)) }}"
                 @if($isActive) aria-selected="true" @endif>
                {{ e(stripslashes($s->title)) }}
                ({{ $s->total }})
              </a>
            @endforeach
          </div>
        @endif

        {{-- Sort --}}
        <div role="tablist" class="tabs tabs-border">
          @php
            $taskParam = urlencode($filters['task']);
            $sortOptions = [
                'title'    => Lang::txt('PLG_MEMBERS_COURSES_SORT_TITLE'),
                'enrolled' => Lang::txt('PLG_MEMBERS_COURSES_SORT_ENROLLED'),
            ];
          @endphp
          @foreach ($sortOptions as $sortKey => $sortLabel)
            @php
              $isSortActive = ($filters['sort'] === $sortKey);
              $sortUrl = Route::url($base . '&task=' . $taskParam . '&sort=' . $sortKey);
            @endphp
            <a role="tab" class="tab {{ $isSortActive ? 'tab-active' : '' }}"
               href="{{ $sortUrl }}"
               @if($isSortActive) aria-selected="true" @endif>
              {{ $sortLabel }}
            </a>
          @endforeach
        </div>
      </nav>

      {{-- Results table --}}
      <div class="overflow-x-auto">
        <table class="table w-full">
          <caption class="text-left text-sm text-base-content/60 mb-2">
            {{ e(stripslashes($active->title)) }}
            <span>({{ Lang::txt('PLG_MEMBERS_COURSES_RESULTS_TOTAL', $rangeStart, $rangeEnd, $total) }})</span>
          </caption>
          <tbody>
            @if (count($results) > 0)
              @foreach ($results as $row)
                @php
                  $sfx = '';
                  if (isset($row->offering_alias)) {
                      $sfx .= '&offering=' . $row->offering_alias;
                  }
                  if (isset($row->section_alias) && !$row->is_default) {
                      $sfx .= ':' . $row->section_alias;
                  }

                  $cls = ($filters['task'] === 'student') ? 'student' : 'manager';
                  $dateText = ($filters['task'] === 'student')
                      ? Lang::txt('PLG_MEMBERS_COURSES_ENROLLED')
                      : Lang::txt('PLG_MEMBERS_COURSES_EMPOWERED');

                  $courseUrl     = Route::url('index.php?option=com_courses&gid=' . $row->alias . $sfx);
                  $enrolledDate = Date::of($row->enrolled)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                @endphp
                <tr>
                  <td class="w-12 text-base-content/40">{{ $row->id }}</td>
                  <td>
                    <a class="link link-hover font-medium" href="{{ $courseUrl }}">
                      {{ e(stripslashes($row->title)) }}
                    </a>
                    <div class="text-xs text-base-content/60 mt-0.5">
                      {{ $dateText }}
                      {{ Lang::txt('PLG_MEMBERS_COURSES_ON') }}
                      <time datetime="{{ $row->enrolled }}">{{ $enrolledDate }}</time>
                      @if ($row->section_title)
                        &mdash;
                        <strong>{{ Lang::txt('PLG_MEMBERS_COURSES_SECTION') }}</strong>
                        {{ e(stripslashes($row->section_title)) }}
                      @endif
                    </div>
                  </td>
                  <td class="w-24">
                    @if ($row->state == 3)
                      <span class="badge badge-warning badge-sm">
                        {{ Lang::txt('PLG_MEMBERS_COURSES_STATE_DRAFT') }}
                      </span>
                    @endif
                  </td>
                  <td class="w-36 text-xs text-base-content/60">
                    @if ($row->starts && $row->starts !== '0000-00-00 00:00:00')
                      {{ Lang::txt('PLG_MEMBERS_COURSES_STARTS') }}<br />
                      <time datetime="{{ $row->starts }}">
                        {{ Date::of($row->starts)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                      </time>
                    @endif
                  </td>
                  <td class="w-36 text-xs text-base-content/60">
                    @if ($row->ends && $row->ends !== '0000-00-00 00:00:00')
                      {{ Lang::txt('PLG_MEMBERS_COURSES_ENDS') }}<br />
                      <time datetime="{{ $row->ends }}">
                        {{ Date::of($row->ends)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                      </time>
                    @endif
                  </td>
                  <td class="w-20">
                    @if ($filters['task'] === 'manager' || $filters['task'] === 'instructor')
                      <a class="btn btn-ghost btn-xs"
                         href="{{ Route::url('index.php?option=com_courses&gid=' . $row->alias . '&task=copy&return=' . $rtrn) }}">
                        {{ Lang::txt('PLG_MEMBERS_COURSES_ACTION_COPY') }}
                      </a>
                    @endif
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="6" class="text-center text-base-content/60">
                  {{ Lang::txt('PLG_MEMBERS_COURSES_NO_RESULTS') }}
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @php
        $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
        $pageNav->setAdditionalUrlParam('id', $member->get('id'));
        $pageNav->setAdditionalUrlParam('active', 'courses');
        $pageNav->setAdditionalUrlParam('task', $filters['task']);
        $pageNav->setAdditionalUrlParam('action', '');
        $pageNav->setAdditionalUrlParam('sort', $filters['sort']);
      @endphp
      {!! $pageNav->render() !!}
    </form>
  </div>
@else
  {{-- Empty state: no course roles --}}
  <div class="text-center py-8">
    <ol class="list-decimal list-inside text-left max-w-md mx-auto mb-6 space-y-2">
      <li>{!! Lang::txt('PLG_MEMBERS_COURSES_FIND_COURSE', Route::url('index.php?option=com_courses')) !!}</li>
      <li>{{ Lang::txt('PLG_MEMBERS_COURSES_ENROLL') }}</li>
      <li>{{ Lang::txt('PLG_MEMBERS_COURSES_GET_LEARNING') }}</li>
    </ol>
    <p class="mb-2"><strong>{{ Lang::txt('PLG_MEMBERS_COURSES_WHAT_ARE_COURSES') }}</strong></p>
    <p class="text-base-content/70">{{ Lang::txt('PLG_MEMBERS_COURSES_EXPLANATION') }}</p>
  </div>
@endif
