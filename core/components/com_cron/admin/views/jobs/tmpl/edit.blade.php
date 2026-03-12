{{--
  Cron Jobs — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Cron\Helpers\Permissions::getActions('component');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_CRON') . ': ' . $text, 'cron');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('job');

  $recur = $row->get('recurrence');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_CRON_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-event" class="label">
          {{ Lang::txt('COM_CRON_FIELD_EVENT') }}
          <span class="text-error">*</span>
        </label>
        <select name="fields[event]" id="field-event"
                class="select select-bordered w-full" required>
          <option value="" @selected(!$row->get('plugin'))>
            {{ Lang::txt('COM_CRON_SELECT') }}
          </option>
          @if($plugins)
            @foreach($plugins as $plugin)
              <optgroup label="{{ Lang::txt('plg_cron_' . $plugin->plugin) }}">
                @if($plugin->events)
                  @foreach($plugin->events as $event)
                    @php
                      $eventVal = $plugin->plugin . '::' . $event['name'];
                    @endphp
                    <option value="{{ $eventVal }}"
                            @selected($row->get('event') == $event['name'])>
                      {{ $event['label'] }}
                    </option>
                  @endforeach
                @endif
              </optgroup>
            @endforeach
          @endif
        </select>
      </div>

  </x-admin-fieldset>

  {{-- Dynamic event parameter fieldsets --}}
  @if($plugins)
    @foreach($plugins as $plugin)
      @if(!isset($plugin->events) || !$plugin->events)
        @continue
      @endif
      @foreach($plugin->events as $event)
        @php
          $data  = '';
          $isActive = ($event['name'] == $row->get('event'));
          if ($isActive) {
              $data = $row->get('params');
          }

          $out = null;
          if ($event['params']) {
              $path = PATH_APP . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin;
              if (!is_dir($path)) {
                  $path = PATH_CORE . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin;
              }
              $param = new \Hubzero\Html\Parameter(
                  (is_object($data) ? $data->toString() : $data),
                  $path . DS . $plugin->plugin . '.xml'
              );
              $param->addElementPath($path);

              $html = [];
              if ($prm = $param->getParams('params', $event['params'])) {
                  foreach ($prm as $p) {
                      $html[] = '<div class="admin-field">';
                      if ($p[0]) {
                          $html[] = $p[0];
                          $html[] = $p[1];
                      } else {
                          $html[] = $p[1];
                      }
                      $html[] = '</div>';
                  }
              }

              $out = (!empty($html) ? implode("\n", $html) : $out);
          }

          if (!$out) {
              $out = '<div class="admin-field"><p class="italic text-muted-foreground">'
                  . Lang::txt('COM_CRON_NO_PARAMETERS_FOUND') . '</p></div>';
          }

          $paramId = 'params-' . $plugin->plugin . '--' . $event['name'];
        @endphp
        <div class="eventparams {{ $isActive ? '' : 'hidden' }}"
             id="{{ $paramId }}">
          <x-admin-fieldset legend="{{ Lang::txt('COM_CRON_FIELDSET_PARAMETERS') }}">
            {!! $out !!}
          </x-admin-fieldset>
        </div>
      @endforeach
    @endforeach
  @endif

  {{-- Recurrence --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_CRON_FIELDSET_RECURRENCE') }}">

      <div class="admin-field">
        <label for="field-recurrence" class="label">{{ Lang::txt('COM_CRON_FIELD_COMMON') }}</label>
        <select name="fields[recurrence]" id="field-recurrence"
                class="select select-bordered w-full">
          <option value=""          @selected($recur == '')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_SELECT') }}</option>
          <option value="custom"    @selected($recur == 'custom')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_CUSTOM') }}</option>
          <option value="0 0 1 1 *" @selected($recur == '0 0 1 1 *')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_YEAR') }}</option>
          <option value="0 0 1 * *" @selected($recur == '0 0 1 * *')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_MONTH') }}</option>
          <option value="0 0 * * 0" @selected($recur == '0 0 * * 0')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_WEEK') }}</option>
          <option value="0 0 * * *" @selected($recur == '0 0 * * *')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_DAY') }}</option>
          <option value="0 * * * *" @selected($recur == '0 * * * *')>{{ Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_AN_HOUR') }}</option>
        </select>
      </div>

      <div class="overflow-x-auto">
        <table class="table table-sm">
          <tbody id="custom" @class(['hidden' => $recur != 'custom'])>
            {{-- Minute --}}
            @php $min = $row->get('minute'); @endphp
            <tr>
              <th class="w-40">
                <label for="field-minute-c">{{ Lang::txt('COM_CRON_FIELD_MINUTE') }}</label>
              </th>
              <td class="w-32">
                <input type="text"
                       name="fields[minute][c]"
                       id="field-minute-c"
                       class="input input-bordered input-sm w-full"
                       value="{{ $min }}" />
              </td>
              <td>
                <select name="fields[minute][s]" id="field-minute-s"
                        class="select select-bordered select-sm w-full"
                        aria-label="{{ Lang::txt('COM_CRON_FIELD_MINUTE') }}">
                  <option value=""     @selected($min == '')>{{ Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') }}</option>
                  <option value="*"    @selected($min == '*')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY') }}</option>
                  <option value="*/5"  @selected($min == '*/5')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIVE') }}</option>
                  <option value="*/10" @selected($min == '*/10')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_TEN') }}</option>
                  <option value="*/15" @selected($min == '*/15')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIFTEEN') }}</option>
                  <option value="*/30" @selected($min == '*/30')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_THIRTY') }}</option>
                  @for($i = 0; $i < 60; $i++)
                    <option value="{{ $i }}" @selected($min == (string) $i)>{{ $i }}</option>
                  @endfor
                </select>
              </td>
            </tr>

            {{-- Hour --}}
            @php $hr = $row->get('hour'); @endphp
            <tr>
              <th>
                <label for="field-hour-c">{{ Lang::txt('COM_CRON_FIELD_HOUR') }}</label>
              </th>
              <td>
                <input type="text"
                       name="fields[hour][c]"
                       id="field-hour-c"
                       class="input input-bordered input-sm w-full"
                       value="{{ $hr }}" />
              </td>
              <td>
                <select name="fields[hour][s]" id="field-hour-s"
                        class="select select-bordered select-sm w-full"
                        aria-label="{{ Lang::txt('COM_CRON_FIELD_HOUR') }}">
                  <option value=""    @selected($hr == '')>{{ Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') }}</option>
                  <option value="*"   @selected($hr == '*')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY') }}</option>
                  <option value="*/2" @selected($hr == '*/2')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER') }}</option>
                  <option value="*/4" @selected($hr == '*/4')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_FOUR') }}</option>
                  <option value="*/6" @selected($hr == '*/6')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX') }}</option>
                  <option value="0"   @selected($hr == '0')>{{ Lang::txt('COM_CRON_FIELD_OPT_MIDNIGHT') }}</option>
                  @for($i = 1; $i < 24; $i++)
                    <option value="{{ $i }}" @selected($hr == (string) $i)>{{ $i }}</option>
                  @endfor
                </select>
              </td>
            </tr>

            {{-- Day of Month --}}
            @php $dy = $row->get('day'); @endphp
            <tr>
              <th>
                <label for="field-day-c">{{ Lang::txt('COM_CRON_FIELD_DAY_OF_MONTH') }}</label>
              </th>
              <td>
                <input type="text"
                       name="fields[day][c]"
                       id="field-day-c"
                       class="input input-bordered input-sm w-full"
                       value="{{ $dy }}" />
              </td>
              <td>
                <select name="fields[day][s]" id="field-day-s"
                        class="select select-bordered select-sm w-full"
                        aria-label="{{ Lang::txt('COM_CRON_FIELD_DAY_OF_MONTH') }}">
                  <option value="" @selected($dy == '')>{{ Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') }}</option>
                  <option value="*" @selected($dy == '*')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY') }}</option>
                  @for($i = 1; $i < 32; $i++)
                    <option value="{{ $i }}" @selected($dy == (string) $i)>{{ $i }}</option>
                  @endfor
                </select>
              </td>
            </tr>

            {{-- Month --}}
            @php $mo = $row->get('month'); @endphp
            <tr>
              <th>
                <label for="field-month-c">{{ Lang::txt('COM_CRON_FIELD_MONTH') }}</label>
              </th>
              <td>
                <input type="text"
                       name="fields[month][c]"
                       id="field-month-c"
                       class="input input-bordered input-sm w-full"
                       value="{{ $mo }}" />
              </td>
              <td>
                <select name="fields[month][s]" id="field-month-s"
                        class="select select-bordered select-sm w-full"
                        aria-label="{{ Lang::txt('COM_CRON_FIELD_MONTH') }}">
                  <option value=""    @selected($mo == '')>{{ Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') }}</option>
                  <option value="*"   @selected($mo == '*')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY') }}</option>
                  <option value="*/2" @selected($mo == '*/2')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER') }}</option>
                  <option value="*/3" @selected($mo == '*/3')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_THREE') }}</option>
                  <option value="*/6" @selected($mo == '*/6')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX') }}</option>
                  <option value="1"   @selected($mo == '1')>{{ Lang::txt('JANUARY_SHORT') }}</option>
                  <option value="2"   @selected($mo == '2')>{{ Lang::txt('FEBRUARY_SHORT') }}</option>
                  <option value="3"   @selected($mo == '3')>{{ Lang::txt('MARCH_SHORT') }}</option>
                  <option value="4"   @selected($mo == '4')>{{ Lang::txt('APRIL_SHORT') }}</option>
                  <option value="5"   @selected($mo == '5')>{{ Lang::txt('MAY_SHORT') }}</option>
                  <option value="6"   @selected($mo == '6')>{{ Lang::txt('JUNE_SHORT') }}</option>
                  <option value="7"   @selected($mo == '7')>{{ Lang::txt('JULY_SHORT') }}</option>
                  <option value="8"   @selected($mo == '8')>{{ Lang::txt('AUGUST_SHORT') }}</option>
                  <option value="9"   @selected($mo == '9')>{{ Lang::txt('SEPTEMBER_SHORT') }}</option>
                  <option value="10"  @selected($mo == '10')>{{ Lang::txt('OCTOBER_SHORT') }}</option>
                  <option value="11"  @selected($mo == '11')>{{ Lang::txt('NOVEMBER_SHORT') }}</option>
                  <option value="12"  @selected($mo == '12')>{{ Lang::txt('DECEMBER_SHORT') }}</option>
                </select>
              </td>
            </tr>

            {{-- Day of Week --}}
            @php $dow = $row->get('dayofweek'); @endphp
            <tr>
              <th>
                <label for="field-dayofweek-c">{{ Lang::txt('COM_CRON_FIELD_DAY_OF_WEEK') }}</label>
              </th>
              <td>
                <input type="text"
                       name="fields[dayofweek][c]"
                       id="field-dayofweek-c"
                       class="input input-bordered input-sm w-full"
                       value="{{ $dow }}" />
              </td>
              <td>
                <select name="fields[dayofweek][s]" id="field-dayofweek-s"
                        class="select select-bordered select-sm w-full"
                        aria-label="{{ Lang::txt('COM_CRON_FIELD_DAY_OF_WEEK') }}">
                  <option value="" @selected($dow == '')>{{ Lang::txt('COM_CRON_FIELD_OPT_CUSTOM') }}</option>
                  <option value="*" @selected($dow == '*')>{{ Lang::txt('COM_CRON_FIELD_OPT_EVERY') }}</option>
                  <option value="0" @selected($dow == '0')>{{ Lang::txt('SUN') }}</option>
                  <option value="1" @selected($dow == '1')>{{ Lang::txt('MON') }}</option>
                  <option value="2" @selected($dow == '2')>{{ Lang::txt('TUE') }}</option>
                  <option value="3" @selected($dow == '3')>{{ Lang::txt('WED') }}</option>
                  <option value="4" @selected($dow == '4')>{{ Lang::txt('THU') }}</option>
                  <option value="5" @selected($dow == '5')>{{ Lang::txt('FRI') }}</option>
                  <option value="6" @selected($dow == '6')>{{ Lang::txt('SAT') }}</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_CRON_FIELD_ID') }}</td>
              <td>
                {{ $row->get('id') }}
                <input type="hidden" name="fields[id]"
                       value="{{ $row->get('id') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_CRON_FIELD_CREATOR') }}</td>
              <td>
                {{ $row->creator->get('name') }}
                <input type="hidden" name="fields[created_by]"
                       value="{{ $row->get('created_by') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_CRON_FIELD_CREATED') }}</td>
              <td>
                {{ $row->get('created') }}
                <input type="hidden" name="fields[created]"
                       value="{{ Date::of($row->get('created'))->toLocal('Y-m-d H:i:s') }}" />
              </td>
            </tr>
            @if($row->get('modified'))
              <tr>
                <td>{{ Lang::txt('COM_CRON_FIELD_MODIFIER') }}</td>
                <td>
                  {{ $row->modifier->get('name', Lang::txt('COM_CRON_UNKNOWN')) }}
                  <input type="hidden" name="fields[modified_by]"
                         value="{{ $row->get('modified_by') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_CRON_FIELD_MODIFIED') }}</td>
                <td>
                  {{ $row->get('modified') }}
                  <input type="hidden" name="fields[modified]"
                         value="{{ Date::of($row->get('modified'))->toLocal('Y-m-d H:i:s') }}" />
                </td>
              </tr>
            @endif
            @if($row->get('id'))
              <tr>
                <td>{{ Lang::txt('COM_CRON_FIELD_LAST_RUN') }}</td>
                <td>
                  {{ $row->get('last_run') }}
                  <input type="hidden" name="fields[last_run]"
                         value="{{ $row->get('last_run') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_CRON_FIELD_NEXT_RUN') }}</td>
                <td>
                  {{ $row->get('next_run') }}
                  <input type="hidden" name="fields[next_run]"
                         value="{{ $row->get('next_run') }}" />
                </td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_CRON_FIELD_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-publish_up" class="label">{{ Lang::txt('COM_CRON_FIELD_START_RUNNING') }}</label>
          @php
            $pubUp = $row->get('publish_up');
            $pubUpVal = (!$pubUp || $pubUp == '0000-00-00 00:00:00') ? '' : $pubUp;
          @endphp
          {!! Html::input('calendar', 'fields[publish_up]', e($pubUpVal), ['id' => 'field-publish_up']) !!}
        </div>

        <div class="admin-field">
          <label for="field-publish_down" class="label">{{ Lang::txt('COM_CRON_FIELD_STOP_RUNNING') }}</label>
          @php
            $pubDown = $row->get('publish_down');
            $pubDownVal = (!$pubDown || $pubDown == '0000-00-00 00:00:00') ? '' : $pubDown;
          @endphp
          {!! Html::input('calendar', 'fields[publish_down]', e($pubDownVal), ['id' => 'field-publish_down']) !!}
        </div>

    </x-admin-fieldset>
  @endslot
</x-admin-edit>
