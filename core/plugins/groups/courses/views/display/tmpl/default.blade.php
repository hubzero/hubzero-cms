{{--
  Group Courses — course listing with sort tabs.

  Variables from plugin:
    $option   — component option
    $group    — group object
    $filters  — array with search, sortby, start, limit
    $results  — collection/array of course objects
    $total    — total course count

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=courses';
  $qs   = $filters['search'] ? '&search=' . e($filters['search']) : '';

  $rangeStart = ($total > 0) ? $filters['start'] + 1 : 0;
  $rangeEnd   = ($total > ($filters['start'] + $filters['limit']))
      ? ($filters['start'] + $filters['limit'])
      : $total;
@endphp

<h3 class="text-lg font-semibold mb-4">
  {{ Lang::txt('PLG_GROUPS_COURSES') }}
</h3>

<section>
  @if (count($results) > 0)
    <div id="courses-container">
      <form method="get" action="{{ Route::url($base) }}">
        {{-- Sort tabs --}}
        <nav class="mb-4" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
          <div role="tablist" class="tabs tabs-border">
            @php
              $sortOptions = [
                  'title'      => [
                      'label' => Lang::txt('PLG_GROUPS_COURSES_SORT_TITLE'),
                      'title' => Lang::txt('PLG_GROUPS_COURSES_SORT_BY_TITLE'),
                  ],
                  'popularity' => [
                      'label' => Lang::txt('PLG_GROUPS_COURSES_SORT_ENROLLED'),
                      'title' => Lang::txt('PLG_GROUPS_COURSES_SORT_BY_ENROLLED'),
                  ],
              ];
            @endphp
            @foreach ($sortOptions as $sortKey => $sortData)
              @php
                $isActive = ($filters['sortby'] === $sortKey);
                $sortUrl  = Route::url($base . '&sortby=' . $sortKey . $qs);
              @endphp
              <a role="tab" class="tab {{ $isActive ? 'tab-active' : '' }}"
                 href="{{ $sortUrl }}"
                 title="{{ $sortData['title'] }}"
                 @if($isActive) aria-selected="true" @endif>
                {{ $sortData['label'] }}
              </a>
            @endforeach
          </div>
        </nav>

        {{-- Results table --}}
        <div class="overflow-x-auto">
          <table class="table table-zebra w-full">
            <caption class="text-left text-sm text-base-content/60 mb-2">
              {{ e(Lang::txt('PLG_GROUPS_COURSES')) }}
              <span>({{ Lang::txt('PLG_GROUPS_COURSES_RESULTS_TOTAL', $rangeStart, $rangeEnd, $total) }})</span>
            </caption>
            <tbody>
              @foreach ($results as $course)
                @php
                  $instructors = $course->instructors();
                  $courseUrl   = Route::url($course->link());
                  $blurb      = \Hubzero\Utility\Str::truncate(stripslashes($course->get('blurb')), 200);

                  $stateBadge = match ((int) $course->get('state')) {
                      1 => ['class' => 'badge-success', 'text' => Lang::txt('PLG_GROUPS_COURSES_STATE_PUBLISHED')],
                      3 => ['class' => 'badge-warning', 'text' => Lang::txt('PLG_GROUPS_COURSES_STATE_DRAFT')],
                      2 => ['class' => 'badge-error',   'text' => Lang::txt('PLG_GROUPS_COURSES_STATE_DELETED')],
                      0 => ['class' => 'badge-ghost',   'text' => Lang::txt('PLG_GROUPS_COURSES_STATE_UNPUBLISHED')],
                      default => ['class' => 'badge-ghost', 'text' => ''],
                  };
                @endphp
                <tr>
                  <td class="w-12 text-base-content/40">
                    {{ $course->get('id') }}
                  </td>
                  <td>
                    <a class="link link-hover font-medium" href="{{ $courseUrl }}">
                      {{ e(stripslashes($course->get('title'))) }}
                    </a>
                    @if (count($instructors) > 0)
                      <div class="text-xs text-base-content/60 mt-0.5">
                        Instructors:
                        @foreach ($instructors as $i)
                          @php
                            $instructor    = User::getInstance($i->get('user_id'));
                            $instructorUrl = Route::url('index.php?option=com_members&id=' . $i->get('user_id'));
                          @endphp
                          @if (!$loop->first), @endif
                          <a class="link link-hover" href="{{ $instructorUrl }}">
                            {{ e(stripslashes($instructor->get('name'))) }}
                          </a>
                        @endforeach
                      </div>
                    @endif
                    @if ($blurb)
                      <div class="text-sm text-base-content/70 mt-1">
                        {{ $blurb }}
                      </div>
                    @endif
                  </td>
                  <td class="w-28">
                    <span class="badge badge-sm {{ $stateBadge['class'] }}">
                      {{ $stateBadge['text'] }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        @php
          $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
          $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
          $pageNav->setAdditionalUrlParam('active', 'courses');
          $pageNav->setAdditionalUrlParam('action', '');
          $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
        @endphp
        {!! $pageNav->render() !!}
      </form>
    </div>
  @else
    {{-- Empty state --}}
    <div class="text-center py-8">
      <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_GROUPS_COURSES_NONE') }}</p>
      <p class="mb-2"><strong>{{ Lang::txt('PLG_GROUPS_COURSES_WHAT_IS_THIS') }}</strong></p>
      <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_GROUPS_COURSES_ABOUT_PLUGIN') }}</p>
      <p class="mb-2"><strong>{{ Lang::txt('PLG_GROUPS_COURSES_WHAT_ARE_COURSES') }}</strong></p>
      <p class="text-base-content/70">{{ Lang::txt('PLG_GROUPS_COURSES_EXPLANATION') }}</p>
    </div>
  @endif
</section>
