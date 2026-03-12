{{--
  Cache Manager — Purge Expired Cache view

  No extra variables; toolbar button submits the purge task.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(Lang::txt('COM_CACHE_PURGE_EXPIRED_CACHE'), 'cache');
  Toolbar::custom('purge', 'delete', '', 'COM_CACHE_PURGE_EXPIRED', false);
  Toolbar::divider();
  if (User::authorise('core.admin', 'com_cache')) {
      Toolbar::preferences('com_cache');
      Toolbar::divider();
  }
  Toolbar::help('purge_expired');
@endphp

<form action="{{ Route::url('index.php?option=com_cache', false) }}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="admin-fieldset">
    <div class="admin-fieldset-body">
      <p>{{ Lang::txt('COM_CACHE_PURGE_INSTRUCTIONS') }}</p>
      <div role="alert" class="alert alert-warning mt-2">
        {{ Lang::txt('COM_CACHE_RESOURCE_INTENSIVE_WARNING') }}
      </div>
    </div>
  </div>

  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="option" value="com_cache" />
  {!! Html::input('token') !!}
</form>
