{{--
  Location form fields partial — shared by edit and component views

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="space-y-4">

  <div class="grid grid-cols-2 gap-4">
    <div class="admin-field">
      <label for="field-ipFROM" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_IP_FROM') }}</label>
      <input type="text"
             name="fields[ipFROM]"
             id="field-ipFROM"
             class="input input-bordered w-full font-mono"
             value="{{ long2ip((int)$row->get('ipFROM')) }}" />
    </div>
    <div class="admin-field">
      <label for="field-ipTO" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_IP_TO') }}</label>
      <input type="text"
             name="fields[ipTO]"
             id="field-ipTO"
             class="input input-bordered w-full font-mono"
             value="{{ long2ip((int)$row->get('ipTO')) }}" />
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="admin-field">
      <label for="field-continent" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_CONTINENT') }}</label>
      <select name="fields[continent]" id="field-continent"
              class="select select-bordered w-full" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_TOOLS_SELECT') }}</option>
        @foreach($continents as $code => $label)
          <option value="{{ $code }}" @selected($row->get('continent') == $code)>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="admin-field">
      <label for="field-countrySHORT" class="label text-base-content">{{ Lang::txt('Country') }}</label>
      <select name="fields[countrySHORT]" id="field-countrySHORT" class="select select-bordered w-full">
        <option value="">{{ Lang::txt('COM_TOOLS_SELECT') }}</option>
        @foreach($countries as $country)
          @if(!$country['code']) @continue @endif
          @if($row->get('continent') && $country['continent'] !== $row->get('continent')) @continue @endif
          <option value="{{ $country['code'] }}"
                  data-continent="{{ $country['continent'] }}"
                  @selected(strtoupper($row->get('countrySHORT', '')) === $country['code'])>
            {{ $country['name'] }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="admin-field">
      <label for="field-ipREGION" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_REGION') }}</label>
      <input type="text"
             name="fields[ipREGION]"
             id="field-ipREGION"
             class="input input-bordered w-full"
             value="{{ $row->get('ipREGION', '') }}" />
    </div>
    <div class="admin-field">
      <label for="field-ipCITY" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_CITY') }}</label>
      <input type="text"
             name="fields[ipCITY]"
             id="field-ipCITY"
             class="input input-bordered w-full"
             value="{{ $row->get('ipCITY', '') }}" />
    </div>
  </div>

  <div class="admin-field">
    <label for="field-notes" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_NOTES') }}</label>
    <textarea name="fields[notes]"
              id="field-notes"
              rows="4"
              class="textarea textarea-bordered w-full">{{ $row->get('notes', '') }}</textarea>
  </div>

</div>
