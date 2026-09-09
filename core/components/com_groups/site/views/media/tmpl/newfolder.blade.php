{{--
  New folder modal form.

  Variables:
    $group      — Group object
    $folderList — string: HTML folder select

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $actionUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
      . '&controller=media&task=savefolder&no_html=1');
@endphp

<form action="{{ $actionUrl }}" method="post" class="hubForm">
  <fieldset>
    <legend>{{ Lang::txt('Add Folder') }}</legend>
    <label>
      {{ Lang::txt('Folder Name: ') }}
      <input type="text" name="name" />
    </label>
    <label>
      {{ Lang::txt('Create in: ') }}
      {!! $folderList !!}
    </label>
    <p class="controls">
      {!! Html::input('token') !!}
      <button type="submit" class="btn icon-save">{{ Lang::txt('Create') }}</button>
    </p>
  </fieldset>
</form>
