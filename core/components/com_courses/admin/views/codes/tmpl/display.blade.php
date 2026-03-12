{{--
  Courses — Coupon Codes admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Courses\Helpers\Permissions::getActions();
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  $courseAlias = $course->get('alias');
  $courseTitle = $course->get('title');
  $offeringTitle = $offering->get('title');
  $sectionTitle  = e($section->get('title'));

  $offeringsUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=offerings&course=' . $course->get('id'), false
  );
  $sectionsUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=sections&offering=' . $offering->get('id'), false
  );

  $generateUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&section=' . $section->get('id')
      . '&task=options&tmpl=component', false
  );
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_COUPON_CODES'),
      'courses'
  );
  if ($canDo->get('core.create')) {
      Toolbar::appendButton(
          'Popup',
          'refresh',
          'COM_COURSES_GENERATE',
          $generateUrl,
          500,
          200
      );
      Toolbar::spacer();
      Toolbar::custom('export', 'export', 'export', 'COM_COURSES_EXPORT_CODES', false);
      Toolbar::spacer();
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_COURSES_DELETE_CONFIRM', 'delete');
  }
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    @slot('search')
      <input type="text"
             name="search"
             id="filter_search"
             class="input input-bordered input-sm w-60"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('COM_COURSES_GO') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    @endslot

      <label for="filter-redeemed" class="sr-only">{{ Lang::txt('COM_COURSES_ALL_STATES') }}</label>
      <select name="redeemed" id="filter-redeemed" class="select select-bordered select-sm" data-submit-on-change>
        <option value="-1" @selected(($filters['redeemed'] ?? -1) == -1)>
          {{ Lang::txt('COM_COURSES_ALL_STATES') }}
        </option>
        <option value="1" @selected(($filters['redeemed'] ?? -1) == 1)>
          {{ Lang::txt('COM_COURSES_FILTER_REDEEMED') }}
        </option>
        <option value="0" @selected(($filters['redeemed'] ?? -1) === 0 || ($filters['redeemed'] ?? -1) === '0')>
          {{ Lang::txt('COM_COURSES_FILTER_UNREDEEMED') }}
        </option>
      </select>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <caption class="text-left px-4 py-2 text-sm">
        (<a href="{{ $offeringsUrl }}" class="link link-primary">{{ $courseAlias }}</a>)
        <a href="{{ $offeringsUrl }}" class="link link-primary">{{ $courseTitle }}</a>:
        <a href="{{ $sectionsUrl }}" class="link link-primary">{{ $offeringTitle }}</a>:
        {{ $sectionTitle }}
      </caption>
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_COURSES_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_CODE', 'code', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_COURSES_COL_CREATED', 'created', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_EXPIRES', 'expires', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_REDEEMED', 'redeemed', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {{ Lang::txt('COM_COURSES_COL_REDEEMED_BY') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );

            $hasExpiry = $row->get('expires')
                && $row->get('expires') != '0000-00-00 00:00:00';
            $expiresDisplay = $hasExpiry
                ? Date::of($row->get('expires'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : Lang::txt('COM_COURSES_NEVER');

            $hasRedeemed = $row->get('redeemed')
                && $row->get('redeemed') != '0000-00-00 00:00:00';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     aria-label="{{ $row->get('code') }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
            </td>
            <td class="priority-5">
              {{ $row->get('id') }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('code') }}
                </a>
              @else
                {{ $row->get('code') }}
              @endif
            </td>
            <td class="priority-4">
              <time datetime="{{ $row->get('created') }}">
                {{ Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
              </time>
            </td>
            <td>
              {{ $expiresDisplay }}
            </td>
            @if($row->get('redeemed_by') || $hasRedeemed)
              <td>
                @if($hasRedeemed)
                  <span class="badge badge-sm badge-success">
                    <time datetime="{{ $row->get('redeemed') }}">
                      {{ Date::of($row->get('redeemed'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                    </time>
                  </span>
                @else
                  <span class="badge badge-sm badge-ghost">
                    {{ Lang::txt('JNO') }}
                  </span>
                @endif
              </td>
              <td class="priority-3">
                @php
                  $studentUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=students&task=edit&section=' . $row->get('section_id')
                      . '&id=' . $row->get('redeemed_by'), false
                  );
                @endphp
                <a href="{{ $studentUrl }}" class="link link-hover text-primary text-sm">
                  {{ $row->redeemer()->get('name') }}
                </a>
              </td>
            @else
              <td colspan="2">
                <span class="badge badge-sm badge-ghost">
                  {{ Lang::txt('COM_COURSES_UNREDEEMED') }}
                </span>
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="section" value="{{ $section->get('id') }}" />
</x-admin-form>
