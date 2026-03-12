{{--
  Citation Format — Admin configuration view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_FORMAT'), 'citation');
  Toolbar::save();
  Toolbar::spacer();
  Toolbar::help('format');


  $__view->js();

  $cf = new \Components\Citations\Helpers\Format();
  $keys = $cf->getTemplateKeys();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('CITATION_FORMAT') }}">

      <div class="admin-field">
        <label for="format-selector" class="label">{{ Lang::txt('CITATION_FORMAT_STYLE') }}</label>
        <select name="citationFormat[id]" id="format-selector" class="select select-bordered w-full">
          @foreach($formats as $format)
            @php $dataFormat = str_replace('"', '\"', $format->format); @endphp
            <option value="{{ $format->id }}"
                    @selected($currentFormat->id == $format->id)
                    data-format="{{ $dataFormat }}">
              {{ $format->style }}
            </option>
          @endforeach
          <option value="custom">{{ Lang::txt('CITATION_CUSTOM_FORMAT') }}</option>
        </select>
      </div>

      <div class="admin-field">
        <label for="format-string" class="label">{{ Lang::txt('CITATION_FORMAT_STRING') }}</label>
        <textarea name="citationFormat[format]" id="format-string"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="10">{{ trim(preg_replace('/\r|\n/', '', $currentFormat->format)) }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Placeholder Reference --}}
    <x-admin-fieldset legend="{{ Lang::txt('CITATION_FORMAT_PLACEHOLDER') }}">
        <table class="admin-table text-sm" id="preformatted">
          <thead>
            <tr>
              <th scope="col">{{ Lang::txt('CITATION_FORMAT_PLACEHOLDER') }}</th>
              <th scope="col">{{ Lang::txt('CITATION_FORMAT_VALUE') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($keys as $k => $v)
              <tr id="{{ $v }}">
                <td class="font-mono text-xs">{{ $v }}</td>
                <td>{{ $k }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="citationFormat[current]" value="{{ $currentFormat->id }}" />
</x-admin-edit>
