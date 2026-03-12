{{--
  Resource Import Hooks — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_HOOKS') }}"
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
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_TYPE', 'type', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_FILE', 'file', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $hooks->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($hooks as $i => $hook)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $hook->get('id'), false
            );
            $rawUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=raw&id=' . $hook->get('id'), false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $hook->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $hook->get('name')) }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                {{ $hook->get('name') }}
              </a>
              @if($hook->get('notes'))
                <br />
                <span class="text-xs text-muted-foreground">
                  {!! nl2br(e($hook->get('notes'))) !!}
                </span>
              @endif
            </td>
            <td>
              @switch($hook->get('type'))
                @case('postconvert')
                  {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT') }}
                  @break
                @case('postmap')
                  {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTMAP') }}
                  @break
                @default
                  {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE') }}
              @endswitch
            </td>
            <td class="priority-2">
              {{ $hook->get('file') }}
              &mdash;
              <a rel="noopener" target="_blank" href="{{ $rawUrl }}" class="link link-primary text-sm">
                {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FILE_VIEWRAW') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_RESOURCES_IMPORTHOOK_NONE_FOUND') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
