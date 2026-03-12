{{--
  Page Break — Editor popup for inserting page breaks

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Request;

  $editorName = Request::getCmd('e_name');
@endphp

<form class="p-4 space-y-4"
      id="pagebreak-form"
      data-pagebreak-editor="{{ $editorName }}">
  <div class="admin-field">
    <label for="title" class="label">{{ Lang::txt('COM_CONTENT_PAGEBREAK_TITLE') }}</label>
    <input type="text" id="title" name="title" class="input input-bordered w-full" />
  </div>
  <div class="admin-field">
    <label for="alt" class="label">{{ Lang::txt('COM_CONTENT_PAGEBREAK_TOC') }}</label>
    <input type="text" id="alt" name="alt" class="input input-bordered w-full" />
  </div>
  <button type="submit" class="btn btn-sm btn-primary">
    {{ Lang::txt('COM_CONTENT_PAGEBREAK_INSERT_BUTTON') }}
  </button>
</form>
