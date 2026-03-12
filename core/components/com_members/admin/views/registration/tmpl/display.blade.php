{{--
  Registration Configuration — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');

  Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION'), 'users');
  if ($canDo->get('core.edit')) {
      Toolbar::preferences($option);
      Toolbar::save();
      Toolbar::cancel();
  }
@endphp

@include('com_members::admin.views.registration.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_MEMBERS_COL_AREA') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_COL_CREATE_ACCOUNT') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_COL_PROXY_CREATE_ACCOUNT') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_COL_UPDATE_ACCOUNT') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_COL_EDIT_ACCOUNT') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($params as $field => $values)
          @if(str_starts_with($field, 'registration'))
            @php
              $title  = $values->title;
              $value  = $values->value;
              $create = strtoupper(substr($value, 0, 1));
              $proxy  = strtoupper(substr($value, 1, 1));
              $update = strtoupper(substr($value, 2, 1));
              $edit   = strtoupper(substr($value, 3, 1));
              $fieldName = str_replace('registration', '', $values->name);

              $optionsList = [
                  'O' => Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'),
                  'R' => Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'),
                  'H' => Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'),
                  'U' => Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'),
              ];
            @endphp
            <tr>
              <td class="font-medium">{{ $title }}</td>
              @php
                $colLabels = [
                    'create' => Lang::txt('COM_MEMBERS_COL_CREATE_ACCOUNT'),
                    'proxy'  => Lang::txt('COM_MEMBERS_COL_PROXY_CREATE_ACCOUNT'),
                    'update' => Lang::txt('COM_MEMBERS_COL_UPDATE_ACCOUNT'),
                    'edit'   => Lang::txt('COM_MEMBERS_COL_EDIT_ACCOUNT'),
                ];
              @endphp
              @foreach(['create' => $create, 'proxy' => $proxy, 'update' => $update, 'edit' => $edit] as $col => $val)
                <td>
                  @if($val != '-')
                    <select name="settings[{{ $fieldName }}][{{ $col }}]"
                            class="select select-bordered select-sm w-full"
                            aria-label="{{ $title }} — {{ $colLabels[$col] }}">
                      @foreach($optionsList as $optVal => $optLabel)
                        <option value="{{ $optVal }}" @selected($val == $optVal)>
                          {{ $optLabel }}
                        </option>
                      @endforeach
                    </select>
                  @else
                    <span class="text-muted-foreground">{{ Lang::txt('COM_MEMBERS_NOT_APPLICABLE') }}</span>
                    <input type="hidden"
                           name="settings[{{ $fieldName }}][{{ $col }}]"
                           value="-" />
                  @endif
                </td>
              @endforeach
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />
  {!! Html::input('token') !!}
</form>
