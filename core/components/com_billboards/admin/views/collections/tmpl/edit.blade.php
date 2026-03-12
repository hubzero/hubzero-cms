{{--
  Billboard Collection — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = $row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(
      Lang::txt('COM_BILLBOARDS_MANAGER') . ': '
      . Lang::txt('COM_BILLBOARDS_COLLECTIONS') . ': ' . $text,
      'billboards'
  );
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('collection');


  $__view->js();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-name" class="label">
          {{ Lang::txt('COM_BILLBOARDS_FIELD_COLLECTION_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="name"
               id="field-name"
               class="input input-bordered w-full required"
               required
               value="{{ $row->get('name', '') }}" />
      </div>

  </x-admin-fieldset>

  {{-- Hidden fields --}}
  <input type="hidden" name="id" value="{{ $row->get('id') }}" />
</x-admin-edit>
