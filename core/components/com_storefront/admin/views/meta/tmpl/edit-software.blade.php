{{--
  Storefront Meta — Software meta edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

  $text = ($task == 'edit')
      ? Lang::txt('COM_STOREFRONT_EDIT')
      : Lang::txt('COM_STOREFRONT_NEW');

  Toolbar::title(
      Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_PRODUCT_META') . ': ' . $text,
      'storefront'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $eulaRequired       = $meta->eulaRequired ?? 0;
  $eula               = $meta->eula ?? '';
  $globalDownloadLimit = $meta->globalDownloadLimit ?? '';
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="Software download options">

      <div class="admin-field">
        <label for="eulaRequired" class="label">
          Is EULA Required?
        </label>
        <select name="fields[eulaRequired]"
                id="eulaRequired"
                class="select select-bordered select-sm w-full">
          <option value="0" @selected($eulaRequired == 0)>
            {{ Lang::txt('COM_STOREFRONT_NO') }}
          </option>
          <option value="1" @selected($eulaRequired == 1)>
            {{ Lang::txt('COM_STOREFRONT_YES') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="eula" class="label">
          EULA (if required, can be overridden on a SKU level)
        </label>
        {!! $__view->editor(
            'fields[eula]',
            e($eula),
            50,
            10,
            'eula',
            ['buttons' => false]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-globalDownloadLimit" class="label">
          Total Downloads Limit
        </label>
        <input type="text"
               name="fields[globalDownloadLimit]"
               id="field-globalDownloadLimit"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               value="{{ $globalDownloadLimit }}" />
      </div>
  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PRODUCT_META') }}">
      <table class="admin-meta">
        <tbody>
          <tr>
            <td>
              {{ Lang::txt('COM_STOREFRONT_PRODUCT') }}
              {{ Lang::txt('COM_STOREFRONT_ID') }}
            </td>
            <td>
              {{ $row->getId() }}
              <input type="hidden"
                     name="fields[pId]"
                     id="field-id"
                     value="{{ $row->getId() }}" />
              <input type="hidden"
                     name="id"
                     id="id"
                     value="{{ $row->getId() }}" />
            </td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_STOREFRONT_PRODUCT') }}</td>
            <td>{{ $row->getName() }}</td>
          </tr>
        </tbody>
      </table>
    </x-admin-fieldset>
  @endslot
</x-admin-edit>
