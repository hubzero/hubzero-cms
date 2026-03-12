{{--
  Newsletter Tools — Mozify image tool

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->js();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TOOLS') }}"
    icon="tools"
    option="{{ $option }}"
/>

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="item-form"
      enctype="multipart/form-data">

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Mozify form --}}
    <div>
      <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY') }}">

          <p class="text-sm text-muted-foreground">
            {{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_DESC') }}
          </p>

          <div class="admin-field">
            <label for="image-file" class="label">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_FILE') }}</label>
            <input type="file" name="image-file" id="image-file"
                   class="file-input file-input-bordered w-full" />
          </div>

          <div class="divider text-sm">{{ Lang::txt('or') }}</div>

          <div class="admin-field">
            <label for="image-url" class="label">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_URL') }}</label>
            <input type="text" name="image-url" id="image-url"
                   class="input input-bordered w-full" />
          </div>

          <div class="admin-field">
            <label for="mosaic-size" class="label">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOSAIC_SIZE') }}</label>
            <select name="mosaic-size" id="mosaic-size" class="select select-bordered w-full">
              @foreach([1, 3, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50] as $size)
                <option value="{{ $size }}" @selected($size == 5)>{{ $size }}</option>
              @endforeach
            </select>
          </div>

          <div class="admin-field">
            <button type="submit" class="btn btn-primary">{{ Lang::txt('Submit') }}</button>
          </div>

      </x-admin-fieldset>
    </div>

    {{-- Results --}}
    @if($code != '')
      <div>
        <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_ORIGINAL') }}">
            <img src="{{ str_replace(PATH_APP, '', $original) }}" alt="" class="max-w-full" />
        </x-admin-fieldset>

        <div class="admin-fieldset mt-4">
          <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOZIFIED') }}</h3>
          <div class="admin-fieldset-body">
            <iframe id="preview-iframe" title="{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOZIFIED') }}" class="w-full border border-base-300 rounded-box"></iframe>
            <div id="preview-code" class="hidden">{!! $preview !!}</div>
          </div>
        </div>

        <div class="admin-fieldset mt-4">
          <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_CODE') }}</h3>
          <div class="admin-fieldset-body">
            <label for="code" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_CODE') }}</label>
            <textarea id="code"
                      class="textarea textarea-bordered w-full font-mono text-sm"
                      rows="10">{{ str_replace("\n", '', $code) }}</textarea>
          </div>
        </div>
      </div>
    @endif

  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="mozify" />
</form>
