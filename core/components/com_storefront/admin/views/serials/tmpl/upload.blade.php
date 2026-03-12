{{--
  Storefront Serial Numbers — CSV upload (popup)

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
      enctype="multipart/form-data" class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('COM_STOREFRONT_UPLOAD_SERIAL_NUMBERS_CSV') }}
  </h3>

  <div class="form-control w-full mb-4">
    <label for="csvFile" class="label text-base-content">
      {{ Lang::txt('COM_STOREFRONT_CSV_FILE') }}
    </label>
    <input type="file"
           name="csvFile"
           id="csvFile"
           accept=".csv"
           class="file-input file-input-bordered w-full"
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
      {{ Lang::txt('COM_STOREFRONT_IMPORT') }}
    </button>
  </div>

  <input type="hidden" name="sId" value="{{ $sId }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="tmpl" value="component" />
  <input type="hidden" name="task" value="uploadcsv" />
  {!! Html::input('token') !!}
</form>
