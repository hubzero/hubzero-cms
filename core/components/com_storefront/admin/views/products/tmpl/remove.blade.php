{{--
  Storefront Products — Admin delete confirmation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Delete Products', 'storefront');
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('Are you sure you want to delete all selected SKUs?') }}">

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="delete"
                 id="field-delete"
                 class="checkbox"
                 value="1" />
          <span>I'm positive. Go ahead and do the delete.</span>
        </label>
      </div>

      <div>
        <button type="submit" class="btn btn-sm btn-error">
          {{ Lang::txt('COM_STOREFRONT_NEXT') }}
        </button>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <div role="alert" class="alert alert-warning">
      {{ Lang::txt('This action cannot be undone.') }}
    </div>
  @endslot

  <input type="hidden" name="task" value="{{ $task }}" />
  <input type="hidden" name="step" value="2" />
  @foreach ($pIds as $pId)
    <input type="hidden" name="pIds[]" value="{{ $pId }}" />
  @endforeach
</x-admin-edit>
