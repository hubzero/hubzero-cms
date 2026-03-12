{{--
  Storefront SKU Restrictions — Add users popup

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

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="component-form"
      class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('COM_STOREFRONT_ADD_NEW_USERS') }}
  </h3>

  <div class="form-control w-full mb-4">
    <label class="label" for="field-users">
      <span class="label-text text-base-content">
        {{ Lang::txt('COM_STOREFRONT_USERS_COMMA_SEPARATED') }}
      </span>
    </label>
    <textarea name="users"
              id="field-users"
              rows="4"
              class="textarea textarea-bordered w-full"
              required></textarea>
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
      {{ Lang::txt('COM_STOREFRONT_ADD_USERS') }}
    </button>
  </div>

  <input type="hidden" name="sId" value="{{ $sId }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="tmpl" value="component" />
  <input type="hidden" name="task" value="addusers" />
  {!! Html::input('token') !!}
</form>
