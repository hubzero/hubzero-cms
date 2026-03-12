{{--
  Storefront SKU — Software meta partial

  Included from the SKU edit view when the product type model is 'software'.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $eula = !empty($skuMeta['eula']) ? $skuMeta['eula'] : '';
  $downloadFile = !empty($skuMeta['downloadFile']) ? $skuMeta['downloadFile'] : '';
  $serialManagement = !empty($skuMeta['serialManagement']) ? $skuMeta['serialManagement'] : '';
  $serial = !empty($skuMeta['serial']) ? $skuMeta['serial'] : '';
  $downloadLimit = !empty($skuMeta['downloadLimit']) ? $skuMeta['downloadLimit'] : '';
  $globalDownloadLimit = !empty($skuMeta['globalDownloadLimit']) ? $skuMeta['globalDownloadLimit'] : '';
@endphp

{{-- General Software Options --}}
<x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_SOFTWARE_OPTIONS') }}">

    <div class="admin-field">
      <label for="eula" class="label">
        {{ Lang::txt('COM_STOREFRONT_EULA_OVERRIDE') }}
      </label>
      {!! $__view->editor(
          'fields[meta][eula]',
          e($eula),
          50,
          10,
          'eula',
          ['buttons' => false]
      ) !!}
    </div>

    <div class="admin-field">
      <label for="field-download-file" class="label">
        {{ Lang::txt('COM_STOREFRONT_DOWNLOAD_FILE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[meta][downloadFile]"
             id="field-download-file"
             class="input input-bordered input-sm w-full"
             maxlength="100"
             required
             value="{{ $downloadFile }}" />
    </div>

    <div class="admin-field">
      <label for="field-globalDownloadLimit" class="label">
        {{ Lang::txt('COM_STOREFRONT_GLOBAL_DOWNLOAD_LIMIT') }}
      </label>
      <input type="text"
             name="fields[meta][globalDownloadLimit]"
             id="field-globalDownloadLimit"
             class="input input-bordered input-sm w-full"
             maxlength="100"
             value="{{ $globalDownloadLimit }}" />
    </div>

    <div class="admin-field">
      <label for="field-downloadLimit" class="label">
        {{ Lang::txt('COM_STOREFRONT_DOWNLOAD_LIMIT_PER_USER') }}
      </label>
      <input type="text"
             name="fields[meta][downloadLimit]"
             id="field-downloadLimit"
             class="input input-bordered input-sm w-full"
             maxlength="100"
             value="{{ $downloadLimit }}" />
    </div>

</x-admin-fieldset>

{{-- Serial Numbers --}}
<x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_SERIAL_NUMBERS') }}">

    <div class="admin-field">
      <label for="field-serialManagement" class="label">
        {{ Lang::txt('COM_STOREFRONT_SERIAL_MANAGEMENT') }}
      </label>
      <select name="fields[meta][serialManagement]"
              id="field-serialManagement"
              class="select select-bordered select-sm w-full">
        <option value="" @selected(!$serialManagement)>
          {{ Lang::txt('COM_STOREFRONT_SERIAL_NONE') }}
        </option>
        <option value="single" @selected($serialManagement == 'single')>
          {{ Lang::txt('COM_STOREFRONT_SERIAL_SINGLE') }}
        </option>
        <option value="multiple" @selected($serialManagement == 'multiple')>
          {{ Lang::txt('COM_STOREFRONT_SERIAL_MULTIPLE') }}
        </option>
      </select>
    </div>

    <div class="admin-field">
      <label for="field-serial" class="label">
        {{ Lang::txt('COM_STOREFRONT_SINGLE_SERIAL_NUMBER') }}
      </label>
      <input type="text"
             name="fields[meta][serial]"
             id="field-serial"
             class="input input-bordered input-sm w-full"
             maxlength="255"
             value="{{ $serial }}" />
      <p class="text-xs text-muted-foreground mt-1">
        {{ Lang::txt('COM_STOREFRONT_SERIAL_SINGLE_HINT') }}
      </p>
    </div>

    @if ($serialManagement == 'multiple')
      @php
        $serialsUrl = 'index.php?option=' . $option
            . '&controller=serials&sId=' . $row->getId();
      @endphp
      <p>
        <a href="{{ $serialsUrl }}">
          {{ Lang::txt('COM_STOREFRONT_MANAGE_SERIAL_NUMBERS') }}
        </a>
      </p>
    @endif

</x-admin-fieldset>
