{{--
  Points — Configuration

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_POINTS_MANAGE'), 'user');
  Toolbar::save('saveconfig', 'Save Configuration');
  Toolbar::cancel();
@endphp

@include('com_members::admin.views.points.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="w-16">#</th>
          <th class="w-32">Points</th>
          <th>Alias</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        @for($i = 0; $i < 50; $i++)
          <tr>
            <td class="text-muted-foreground">({{ $i + 1 }})</td>
            <td>
              <input type="text"
                     name="points[{{ $i }}]"
                     class="input input-bordered input-sm w-full"
                     value="{{ $params[$i]->points ?? '' }}"
                     maxlength="10" />
            </td>
            <td>
              <input type="text"
                     name="alias[{{ $i }}]"
                     class="input input-bordered input-sm w-full"
                     value="{{ $params[$i]->alias ?? '' }}"
                     maxlength="50" />
            </td>
            <td>
              <input type="text"
                     name="description[{{ $i }}]"
                     class="input input-bordered input-sm w-full"
                     value="{{ $params[$i]->description ?? '' }}"
                     maxlength="255" />
            </td>
          </tr>
        @endfor
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  {!! Html::input('token') !!}
</form>
