{{--
  Job Category — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Jobs\Helpers\Permissions::getActions('category');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_JOBS') }}: {{ Lang::txt('COM_JOBS_CATEGORIES') }}: {{ $text }}"
    icon="category"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @if($task == 'edit')
    <div role="alert" class="alert alert-warning mb-4">
      {{ Lang::txt('COM_JOBS_WARNING_EDIT_CATEGORY') }}
    </div>
  @endif

  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="category" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_TITLE') }} <span class="text-error">*</span>
        </label>
        <input type="text"
               name="category"
               id="category"
               class="input input-bordered w-full"
               maxlength="100"
               required
               value="{{ $row->category }}" />
      </div>

      <div class="admin-field">
        <label for="description" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_DESCRIPTION') }}
        </label>
        <input type="text"
               name="description"
               id="description"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->description }}" />
      </div>

  </x-admin-fieldset>

  <input type="hidden" name="id" value="{{ $row->id }}" />
</x-admin-edit>
