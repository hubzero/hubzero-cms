{{--
  Points — Find user

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_POINTS_MANAGE'), 'user');
@endphp

@include('com_members::admin.views.points.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-admin-fieldset legend="Find User Details">
      <div class="admin-field">
        <label for="uid" class="label">UID:</label>
        <div class="flex gap-2">
          <input type="text"
                 name="uid"
                 id="uid"
                 class="input input-bordered w-full"
                 value="" />
          <button type="submit" class="btn btn-primary">Go</button>
        </div>
      </div>
    </x-admin-fieldset>

    <div class="alert alert-info h-fit">
      Enter a user ID to view their point history and balance.
    </div>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="edit" />
  {!! Html::input('token') !!}
</form>
