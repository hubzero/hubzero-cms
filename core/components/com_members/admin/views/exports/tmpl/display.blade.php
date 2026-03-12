{{--
  Export Members — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_EXPORT'), 'user');
@endphp

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="bg-base-100 rounded-box border border-base-300 p-6 max-w-lg">
    <p class="text-muted-foreground mb-4">
      {{ Lang::txt('COM_MEMBERS_EXPORT_DESC') }}
    </p>
    @php
      $href = Route::url(
          'index.php?option=' . $option
          . '&controller=' . $controller
          . '&task=run&delimiter=,', false
      );
    @endphp
    <a class="btn btn-primary" href="{!! $href !!}">
      Download CSV of all users
    </a>
  </div>

  {!! Html::input('token') !!}
</form>
