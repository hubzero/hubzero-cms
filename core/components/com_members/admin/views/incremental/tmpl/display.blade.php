{{--
  com_members — Incremental registration options

  Variables: $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('Incremental Options'),
      'user'
  );
  Toolbar::save();

  $dbh = App::get('db');
  $dbh->setQuery(
      'SELECT popover_text, award_per, test_group'
      . ' FROM `#__incremental_registration_options`'
      . ' ORDER BY added DESC LIMIT 1'
  );
  list($popoverText, $awardPer, $testGroup) = $dbh->loadRow();

  $dbh->setQuery(
      'SELECT hours FROM `#__incremental_registration_popover_recurrence` ORDER BY idx'
  );
  $recur = $dbh->loadColumn();

  $groups = new \Components\Members\Models\Incremental\Groups();
  $possibleCols = $groups->getPossibleColumns();
  $groupDefs = $groups->getAllGroups();

  $__view->js('incremental');
@endphp

<template id="incremental-cols-data">{{ json_encode($possibleCols) }}</template>

@include('com_members::admin.views.registration.tmpl._submenu')

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="Incremental Registration Options">
    <div class="admin-field">
      <label for="field-popover" class="label">Pop-over text</label>
      <textarea name="popover"
                id="field-popover"
                rows="10"
                class="textarea textarea-bordered w-full">{{ $popoverText }}</textarea>
    </div>

    <div class="admin-field">
      <label for="field-award-per" class="label">Award per field completed</label>
      <input type="text"
             name="award-per"
             id="field-award-per"
             class="input input-bordered w-full"
             value="{{ $awardPer }}" />
    </div>

    <div class="admin-field">
      <label for="field-test-group" class="label">Test group (name or id number)</label>
      <input type="text"
             name="test-group"
             id="field-test-group"
             class="input input-bordered w-full"
             value="{{ $testGroup }}" />
    </div>
  </x-admin-fieldset>

  <x-admin-fieldset legend="Field groups">
    <p>Packages of profile information to prompt starting some time after registration</p>

    <ol id="reg-groups">
      @foreach ($groupDefs as $idx => $group)
        @php
          $unit = 'hour';
          if ($group['hours'] % (24 * 7) == 0) {
              $unit = 'week';
              $group['hours'] /= (24 * 7);
          } elseif ($group['hours'] % 24 == 0) {
              $unit = 'day';
              $group['hours'] /= 24;
          }
        @endphp
        <li class="reg-group">
          <p>
            Beginning
            <input name="group-hours-{{ $idx }}"
                   value="{{ $group['hours'] }}"
                   size="3"
                   class="input input-bordered input-sm w-20"
                   aria-label="Hours for group {{ $idx + 1 }}" />
            <select name="group-time-unit-{{ $idx }}"
                    class="select select-bordered select-sm"
                    aria-label="Time unit for group {{ $idx + 1 }}">
              <option value="hour" @selected($unit == 'hour')>hours</option>
              <option value="day" @selected($unit == 'day')>days</option>
              <option value="week" @selected($unit == 'week')>weeks</option>
            </select>
            after registration, prompt for:
            <ul>
              @foreach ($group['cols'] as $cidx => $col)
                <li>
                  <select name="group-cols-{{ $idx }}[]"
                          class="select select-bordered select-sm"
                          aria-label="Profile field {{ $cidx + 1 }} for group {{ $idx + 1 }}">
                    <option value="">Select profile field...</option>
                    @foreach ($possibleCols as $colName => $colLabel)
                      <option value="{{ $colName }}"
                              @selected($colName == $col)>{{ $colLabel }}</option>
                    @endforeach
                  </select>
                  <button data-action="remove-parent-li"
                          class="btn btn-sm btn-ghost btn-error">
                    Remove field
                  </button>
                </li>
              @endforeach
            </ul>
            <button class="add-field btn btn-sm btn-ghost"
                    data-action="add-field" data-group-idx="{{ $idx }}">Add field</button>
          </p>
          <button class="btn btn-sm btn-ghost btn-error"
                  data-action="remove-group">
            Remove group
          </button>
        </li>
      @endforeach
    </ol>

    <p>
      <button class="btn btn-sm btn-ghost"
              data-action="add-group">Add group</button>
    </p>
  </x-admin-fieldset>

  <x-admin-fieldset legend="Recurrence">
    <p>Time to wait before asking again after subsequent clicks of the "ask me later" button</p>

    <ol id="reg-recurrence">
      @foreach ($recur as $idx => $r)
        @php
          $rUnit = 'hour';
          if ($r % (24 * 7) == 0) {
              $rUnit = 'week';
              $r /= (24 * 7);
          } elseif ($r % 24 == 0) {
              $rUnit = 'day';
              $r /= 24;
          }
        @endphp
        <li>
          <input name="recur-{{ $idx }}"
                 value="{{ $r }}"
                 size="3"
                 class="input input-bordered input-sm w-20"
                 aria-label="Recurrence {{ $idx + 1 }} value" />
          <select name="recur-type-{{ $idx }}"
                  class="select select-bordered select-sm"
                  aria-label="Recurrence {{ $idx + 1 }} time unit">
            <option value="hour" @selected($rUnit == 'hour')>hours</option>
            <option value="day" @selected($rUnit == 'day')>days</option>
            <option value="week" @selected($rUnit == 'week')>weeks</option>
          </select>
          <button class="btn btn-sm btn-ghost btn-error"
                  data-action="remove-parent-li">
            Remove recurrence
          </button>
        </li>
      @endforeach
    </ol>

    <p>
      <button class="btn btn-sm btn-ghost"
              data-action="add-recurrence">Add recurrence</button>
    </p>

    <p>
      After reaching the end of this list:<br />
      <label class="label cursor-pointer justify-start gap-2 text-base-content">
        <input type="radio"
               name="repeat-type"
               value="repeat"
               checked="checked"
               class="radio radio-sm" />
        repeat prompting indefinitely using the last delay listed between attempts
      </label>
      <label class="label cursor-pointer justify-start gap-2 text-base-content">
        <input type="radio"
               name="repeat-type"
               value="stop"
               class="radio radio-sm" />
        stop prompting
      </label>
    </p>
  </x-admin-fieldset>

  <input type="hidden" name="task" value="save" />
</x-admin-edit>
