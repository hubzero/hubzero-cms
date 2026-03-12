{{--
  Storefront Whitelist — Add new whitelisted users (popup)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $formAction = Route::url('index.php?option=' . $option, false);
@endphp

<form action="{{ $formAction }}" method="post" name="adminForm" id="component-form"
      class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('Add new whitelisted users') }}
  </h3>

  <div class="form-control w-full mb-4">
    <label for="field-users" class="label text-base-content">
      {{ Lang::txt('Users (comma-separated IDs or usernames)') }}
    </label>
    <input type="text"
           name="users"
           id="field-users"
           class="input input-bordered input-sm w-full"
           required />
  </div>

  <div class="flex justify-end gap-2">
    <button type="button"
            class="btn btn-ghost btn-sm"
            data-parent-callback="postMessage"
            data-callback-args='["admin-popup-close", "*"]'>
      {{ Lang::txt('JCANCEL') }}
    </button>
    <button type="submit"
            class="btn btn-primary btn-sm">
      {{ Lang::txt('JSAVE') }}
    </button>
  </div>

  <input type="hidden" name="sId" value="{{ $sId }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="tmpl" value="component" />
  <input type="hidden" name="task" value="addusers" />
  <input type="hidden" name="no_html" value="1" />
  {!! Html::input('token') !!}
</form>
