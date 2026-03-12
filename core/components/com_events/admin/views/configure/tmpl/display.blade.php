{{--
  Events — Admin configuration form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\Route;

  Toolbar::title(
      Lang::txt('COM_EVENTS_MANAGER') . ': ' . Lang::txt('COM_EVENTS_CONFIGURATION'),
      'event'
  );
  Toolbar::save();
  Toolbar::cancel();

  $fields = $config->fields;
  $fieldCount = count($fields);
  $n = max($fieldCount, 10);
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- General configuration --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG') }}">

      <div class="admin-field">
        <label for="field-adminmail" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_ADMINMAIL') }}
        </label>
        <input type="text"
               name="config[adminmail]"
               id="field-adminmail"
               class="input input-bordered w-full"
               maxlength="50"
               value="{{ $config->adminmail }}" />
      </div>

      <div class="admin-field">
        <label for="field-starday" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_FIRSTDAY') }}
        </label>
        <select name="config[starday]"
                id="field-starday"
                class="select select-bordered w-full">
          <option value="0"
              @selected($config->starday == '0')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_SUNDAY_FIRST') }}
          </option>
          <option value="1"
              @selected($config->starday == '1')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_MONDAY_FIRST') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-mailview" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_VIEWMAIL') }}
        </label>
        <select name="config[mailview]"
                id="field-mailview"
                class="select select-bordered w-full">
          <option value="YES"
              @selected($config->mailview == 'YES')>
            {{ Lang::txt('JYES') }}
          </option>
          <option value="NO"
              @selected($config->mailview == 'NO')>
            {{ Lang::txt('JNO') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-byview" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_VIEWBY') }}
        </label>
        <select name="config[byview]"
                id="field-byview"
                class="select select-bordered w-full">
          <option value="YES"
              @selected($config->byview == 'YES')>
            {{ Lang::txt('JYES') }}
          </option>
          <option value="NO"
              @selected($config->byview == 'NO')>
            {{ Lang::txt('JNO') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-hitsview" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_VIEWHITS') }}
        </label>
        <select name="config[hitsview]"
                id="field-hitsview"
                class="select select-bordered w-full">
          <option value="YES"
              @selected($config->hitsview == 'YES')>
            {{ Lang::txt('JYES') }}
          </option>
          <option value="NO"
              @selected($config->hitsview == 'NO')>
            {{ Lang::txt('JNO') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-dateformat" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_DATEFORMAT') }}
        </label>
        <select name="config[dateformat]"
                id="field-dateformat"
                class="select select-bordered w-full">
          <option value="0"
              @selected($config->dateformat == '0')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_FRENCH_ENGLISH') }}
          </option>
          <option value="1"
              @selected($config->dateformat == '1')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_US') }}
          </option>
          <option value="2"
              @selected($config->dateformat == '2')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_DEUTSCH') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-stdtime" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_TIMEFORMAT') }}
        </label>
        <select name="config[calUseStdTime]"
                id="field-stdtime"
                class="select select-bordered w-full">
          <option value="YES"
              @selected($config->calUseStdTime == 'YES')>
            {{ Lang::txt('JYES') }}
          </option>
          <option value="NO"
              @selected($config->calUseStdTime == 'NO')>
            {{ Lang::txt('JNO') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-startview" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_STARTPAGE') }}
        </label>
        <select name="config[startview]"
                id="field-startview"
                class="select select-bordered w-full">
          <option value="day"
              @selected($config->startview == 'day')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_REP_DAY') }}
          </option>
          <option value="week"
              @selected($config->startview == 'week')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_REP_WEEK') }}
          </option>
          <option value="month"
              @selected($config->startview == 'month')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_REP_MONTH') }}
          </option>
          <option value="year"
              @selected($config->startview == 'year')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_REP_YEAR') }}
          </option>
          <option value="categories"
              @selected($config->startview == 'categories')>
            {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORIES') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-rowsppg" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_NUMEVENTS') }}
        </label>
        <input type="text"
               name="config[calEventListRowsPpg]"
               id="field-rowsppg"
               class="input input-bordered w-20"
               size="3"
               value="{{ $config->calEventListRowsPpg }}" />
      </div>

  </x-admin-fieldset>

  {{-- Custom fields --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_CAL_LANG_CUSTOM_FIELDS') }}">

      <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_FIELD') }}</th>
              <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_TYPE') }}</th>
              <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_REQUIRED') }}</th>
              <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_SHOW') }}</th>
            </tr>
          </thead>
          <tbody>
            @for($i = 0; $i < $n; $i++)
              @php
                $fTitle    = $fields[$i][1] ?? '';
                $fType     = $fields[$i][2] ?? 'text';
                $fRequired = !empty($fields[$i][3]);
                $fShow     = !empty($fields[$i][4]);
              @endphp
              <tr>
                <td>
                  <input type="text"
                         name="fields[{{ $i }}][title]"
                         class="input input-bordered input-sm w-full"
                         maxlength="255"
                         aria-label="{{ Lang::txt('COM_EVENTS_CAL_LANG_FIELD') }} {{ $i + 1 }}"
                         value="{{ $fTitle }}" />
                </td>
                <td>
                  <select name="fields[{{ $i }}][type]"
                          class="select select-bordered select-sm"
                          aria-label="{{ Lang::txt('COM_EVENTS_CAL_LANG_TYPE') }} {{ $i + 1 }}">
                    <option value="text"
                        @selected($fType == 'text')>
                      {{ Lang::txt('COM_EVENTS_CAL_LANG_TEXT') }}
                    </option>
                    <option value="checkbox"
                        @selected($fType == 'checkbox')>
                      {{ Lang::txt('COM_EVENTS_CAL_LANG_CHECKBOX') }}
                    </option>
                  </select>
                </td>
                <td class="text-center">
                  <input type="checkbox"
                         name="fields[{{ $i }}][required]"
                         class="checkbox checkbox-sm"
                         value="1"
                         aria-label="{{ Lang::txt('COM_EVENTS_CAL_LANG_REQUIRED') }} {{ $i + 1 }}"
                         @checked($fRequired) />
                </td>
                <td class="text-center">
                  <input type="checkbox"
                         name="fields[{{ $i }}][show]"
                         class="checkbox checkbox-sm"
                         value="1"
                         aria-label="{{ Lang::txt('COM_EVENTS_CAL_LANG_SHOW') }} {{ $i + 1 }}"
                         @checked($fShow) />
                </td>
              </tr>
            @endfor
          </tbody>
        </table>
      </div>

  </x-admin-fieldset>
</x-admin-edit>
