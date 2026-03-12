{{--
  Storefront SKUs — Admin delete confirmation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_STOREFRONT') }}: {{ Lang::txt('COM_STOREFRONT_DELETE_SKU') }}"
    icon="storefront"
    option="{{ $option }}"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_CONFIRM_DELETE') }}">

      <p class="text-sm">
        {{ Lang::txt('COM_STOREFRONT_DELETE_SKU_CONFIRM') }}
      </p>

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="delete"
                 id="field-delete"
                 class="checkbox"
                 value="delete" />
          <span>{{ Lang::txt('COM_STOREFRONT_DELETE_POSITIVE') }}</span>
        </label>
      </div>

      <div>
        <button type="submit" class="btn btn-sm btn-error">
          {{ Lang::txt('COM_STOREFRONT_NEXT') }}
        </button>
      </div>

  </x-admin-fieldset>

  <input type="hidden" name="task" value="{{ $task }}" />
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="pId" value="{{ $pId }}" />
  @foreach ($sId as $id)
    <input type="hidden" name="sId[]" value="{{ $id }}" />
  @endforeach
</x-admin-edit>
