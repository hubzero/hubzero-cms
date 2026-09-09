{{--
  Move folder modal form.

  Variables:
    $group      — Group object
    $folder     — string: folder path
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
      . '&controller=media&task=domovefolder&no_html=1');
@endphp

<form action="{{ $actionUrl }}" method="post" class="hubForm">
  <fieldset>
    <legend>{{ Lang::txt('COM_GROUPS_MEDIA_MOVE_FOLDER') }}</legend>
    <label>
      {{ Lang::txt('COM_GROUPS_MEDIA_MOVE_CURRENT_FOLDER') }}:
      <input type="text" name="current" value="{{ e($folder) }}" readonly />
    </label>
    <label>
      {{ Lang::txt('COM_GROUPS_MEDIA_MOVE_MOVE_TO') }}:
      {!! $folderList !!}
    </label>
    <p class="controls">
      {!! Html::input('token') !!}
      <button type="submit" class="btn icon-move">{{ Lang::txt('COM_GROUPS_MEDIA_MOVE') }}</button>
    </p>
  </fieldset>
</form>
