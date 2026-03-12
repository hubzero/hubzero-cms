{{--
  Storefront Option — Admin edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');
  $text  = ($task == 'edit')
      ? Lang::txt('COM_STOREFRONT_EDIT')
      : Lang::txt('COM_STOREFRONT_NEW');

  Toolbar::title(
      Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_OPTION') . ': ' . $text,
      'storefront'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_DETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_STOREFRONT_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[oName]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               required
               maxlength="100"
               value="{{ $row->getName() }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_ID') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_ID') }}</td>
              <td>{{ $row->getId() ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_OPTION_GROUP') }}</td>
              <td>{{ $ogInfo->ogName }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- State --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PARAMETERS') }}">

        <div class="admin-field">
          <label for="field-state" class="label">
            {{ Lang::txt('COM_STOREFRONT_PUBLISH') }}
          </label>
          <select name="fields[state]"
                  id="field-state"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getActiveStatus() == 0)>
              {{ Lang::txt('JUNPUBLISHED') }}
            </option>
            <option value="1" @selected($row->getActiveStatus() == 1)>
              {{ Lang::txt('JPUBLISHED') }}
            </option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[oId]" value="{{ $row->getId() }}" />
  <input type="hidden" name="fields[ogId]" value="{{ $ogInfo->ogId }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
