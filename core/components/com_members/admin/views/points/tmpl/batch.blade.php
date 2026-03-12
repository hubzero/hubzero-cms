{{--
  Points — Batch transaction

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_POINTS_MANAGE'), 'user');
  Toolbar::save('process_batch', 'Process Batch');
  Toolbar::cancel();
@endphp

@include('com_members::admin.views.points.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-7">
      <x-admin-fieldset legend="Process batch transaction">
        <div class="grid grid-cols-2 gap-4">
          <div class="admin-field">
            <label for="type" class="label">Transaction Type:</label>
            <select name="transaction[type]" id="type" class="select select-bordered w-full">
              <option>deposit</option>
              <option>withdraw</option>
            </select>
          </div>
          <div class="admin-field">
            <label for="amount" class="label">Amount:</label>
            <input type="text"
                   name="transaction[amount]"
                   id="amount"
                   class="input input-bordered w-full"
                   maxlength="11"
                   value="" />
          </div>
        </div>

        <div class="admin-field">
          <label for="description" class="label">Description:</label>
          <input type="text"
                 name="transaction[description]"
                 id="description"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="" />
        </div>

        <div class="admin-field">
          <label for="users" class="label">User list</label>
          <textarea name="transaction[users]"
                    id="users"
                    class="textarea textarea-bordered w-full"
                    rows="10"></textarea>
          <p class="text-xs text-muted-foreground mt-1">Enter a comma-separated list of userids.</p>
        </div>
      </x-admin-fieldset>
    </div>

    <div class="lg:col-span-5">
      <x-admin-fieldset legend="Transaction log details">
        <div class="admin-field">
          <label for="com" class="label">Category / Component</label>
          <input type="text"
                 name="log[com]"
                 id="com"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="" />
          <p class="text-xs text-muted-foreground mt-1">E.g. answers, survey, etc.</p>
        </div>

        <div class="admin-field">
          <label for="action" class="label">Action type</label>
          <input type="text"
                 name="log[action]"
                 id="action"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="" />
          <p class="text-xs text-muted-foreground mt-1">E.g. royalty, setup, etc.</p>
        </div>

        <div class="admin-field">
          <label for="ref" class="label">Reference id (optional)</label>
          <input type="text"
                 name="log[ref]"
                 id="ref"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="" />
        </div>
      </x-admin-fieldset>
    </div>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="process_batch" />
  {!! Html::input('token') !!}
</form>
