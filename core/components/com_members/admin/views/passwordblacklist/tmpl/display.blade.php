{{--
  Password Blacklist — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=passwordrules', false) !!}">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}
      </a>
    </li>
    <li>
      <a class="active"
         href="{!! Route::url('index.php?option=' . $option . '&controller=passwordblacklist', false) !!}">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}
      </a>
    </li>
  </ul>
</nav>

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
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_WORD', 'word', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
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
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('word')) }}" />
              @endif
            </td>
            <td class="priority-4">{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->get('word') }}
                </a>
              @else
                {{ $row->get('word') }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
