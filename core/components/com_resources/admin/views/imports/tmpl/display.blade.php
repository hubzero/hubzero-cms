{{--
  Resource Imports — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $__view->css('import');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES_IMPORT_TITLE_IMPORTS') }}"
    icon="import"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_NUMRECORDS', 'count', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_CREATED', 'created_at', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_LASTRUN', 'ran_at', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {{ Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_RUNCOUNT') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $imports->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($imports as $i => $import)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $import->get('id'), false
            );
            $runUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=run&id=' . $import->get('id'), false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $import->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $import->get('name')) }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                {{ $import->get('name') }}
              </a>
              @if($import->get('notes'))
                <br />
                <span class="text-xs text-muted-foreground">
                  {!! nl2br(e($import->get('notes'))) !!}
                </span>
              @endif
            </td>
            <td class="priority-3">
              {{ $import->get('count', 0) }}
            </td>
            <td class="priority-4">
              @if($import->get('created_at'))
                <span class="text-sm">
                  {{ Date::of($import->get('created_at'))->toLocal('m/d/Y') }}
                </span>
                @if($import->get('created_by'))
                  <br />
                  @php $createdBy = User::getInstance($import->get('created_by')); @endphp
                  <span class="text-xs text-muted-foreground">
                    {{ $createdBy ? e($createdBy->get('name')) : '' }}
                  </span>
                @endif
              @endif
            </td>
            <td class="priority-3">
              @if($import->get('ran_at'))
                <span class="text-sm">
                  {{ Date::of($import->get('ran_at'))->toLocal('m/d/Y') }}
                </span>
                @if($import->get('ran_by'))
                  <br />
                  @php $ranBy = User::getInstance($import->get('ran_by')); @endphp
                  <span class="text-xs text-muted-foreground">
                    {{ $ranBy ? e($ranBy->get('name')) : '' }}
                  </span>
                @endif
              @else
                <span class="text-muted-foreground">—</span>
              @endif
            </td>
            <td class="priority-2">
              @php
                $runs = $import->runs()->count();
              @endphp
              @if($runs > 0)
                <a href="{{ $runUrl }}" class="link link-primary text-sm">
                  {{ $runs }}
                </a>
              @else
                0
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_RESOURCES_IMPORT_NONE') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
