{{--
  Events — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $text = ($task == 'edit') ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW');

  Toolbar::title(Lang::txt('COM_EVENTS_EVENT') . ': ' . $text, 'event.png');
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('event');

  $xprofilec = User::getInstance($row->created_by);
  $xprofilem = User::getInstance($row->modified_by);
  $userm = is_object($xprofilem) ? $xprofilem->get('name') : '';
  $userc = is_object($xprofilec) ? $xprofilec->get('name') : '';

  $params = new \Hubzero\Html\Parameter(
      $row->params,
      Component::path($option) . DS . 'events.xml'
  );

  $tagsValue = isset($tags) ? e($tags) : '';

  $__view->js('events.blade.js');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Event details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_EVENT') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="title"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->title }}" />
      </div>

      <div class="admin-field">
        <label for="catid" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY') }}
          <span class="text-error">*</span>
        </label>
        {!! \Components\Events\Helpers\Html::buildCategorySelect($row->catid, '', 0, $option) !!}
      </div>

      <div class="admin-field">
        <label for="field-econtent" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACTIVITY') }}
        </label>
        {!! $__view->editor('econtent', $row->content, '45', '10', 'field-econtent') !!}
      </div>

      <div class="admin-field">
        <label for="field-adresse_info" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ADRESSE') }}
        </label>
        <input type="text"
               name="adresse_info"
               id="field-adresse_info"
               class="input input-bordered w-full"
               maxlength="120"
               value="{{ $row->adresse_info }}" />
      </div>

      <div class="admin-field">
        <label for="field-contact_info" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CONTACT') }}
        </label>
        <input type="text"
               name="contact_info"
               id="field-contact_info"
               class="input input-bordered w-full"
               maxlength="120"
               value="{{ $row->contact_info }}" />
      </div>

      <div class="admin-field">
        <label for="field-extra_info" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_EXTRA') }}
        </label>
        <input type="text"
               name="extra_info"
               id="field-extra_info"
               class="input input-bordered w-full"
               maxlength="240"
               value="{{ $row->extra_info }}" />
      </div>

      {{-- Custom fields from config --}}
      @foreach($fields as $field)
        <div class="admin-field">
          <label for="field-{{ $field[0] }}" class="label">
            {{ $field[1] }}
            @if($field[3])
              <span class="text-error">*</span>
            @endif
          </label>
          @if($field[2] == 'checkbox')
            <input type="checkbox"
                   name="fields[{{ $field[0] }}]"
                   id="field-{{ $field[0] }}"
                   value="1"
                   class="checkbox"
                   @checked(end($field) == 1) />
          @else
            <input type="text"
                   name="fields[{{ $field[0] }}]"
                   id="field-{{ $field[0] }}"
                   class="input input-bordered w-full"
                   maxlength="255"
                   value="{{ end($field) }}" />
          @endif
        </div>
      @endforeach

      <div class="admin-field">
        <label for="field-tags" class="label">
          {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TAGS') }}
        </label>
        <input type="text"
               name="tags"
               id="field-tags"
               class="input input-bordered w-full"
               value="{{ $tagsValue }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta info --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE') }}</td>
              <td>
                @if($row->state > 0)
                  <span class="badge badge-sm badge-success">
                    {{ Lang::txt('COM_EVENTS_EVENT_PUBLISHED') }}
                  </span>
                @elseif($row->state < 0)
                  <span class="badge badge-sm badge-info">
                    {{ Lang::txt('COM_EVENTS_EVENT_ARCHIVED') }}
                  </span>
                @else
                  <span class="badge badge-sm badge-ghost">
                    {{ Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED') }}
                  </span>
                @endif
              </td>
            </tr>
            @if($row->created)
              <tr>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED') }}</td>
                <td>{{ Date::of($row->created)->toLocal('F d, Y @ g:ia') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED_BY') }}</td>
                <td>{{ $userc }}</td>
              </tr>
            @else
              <tr>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED') }}</td>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_NEWEVENT') }}</td>
              </tr>
            @endif
            @if($row->modified && $row->modified != '0000-00-00 00:00:00')
              <tr>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED') }}</td>
                <td>{{ Date::of($row->modified)->toLocal('F d, Y @ g:ia') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED_BY') }}</td>
                <td>{{ $userm }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_PUBLISHING') }}">

        <div class="admin-field">
          <label for="field-publish_up" class="label">
            {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STARTDATE') }}
          </label>
          {!! Html::input('calendar', 'publish_up', $row->publish_up, ['id' => 'field-publish_up']) !!}
        </div>

        <div class="admin-field">
          <label for="field-publish_down" class="label">
            {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ENDDATE') }}
          </label>
          {!! Html::input('calendar', 'publish_down', $row->publish_down, ['id' => 'field-publish_down']) !!}
        </div>

        <div class="admin-field">
          <label for="time_zone" class="label">
            {{ Lang::txt('COM_EVENTS_CAL_TIME_ZONE') }}
          </label>
          {!! \Components\Events\Helpers\Html::buildTimeZoneSelect($row->time_zone, '') !!}
        </div>

    </x-admin-fieldset>

    {{-- Registration --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_REGISTRATION') }}">

        <div class="admin-field">
          <label for="field-registerby" class="label">
            {{ Lang::txt('COM_EVENTS_REGISTER_BY') }}
          </label>
          {!! Html::input('calendar', 'registerby', $row->registerby, ['id' => 'field-registerby']) !!}
        </div>

        <div class="admin-field">
          <label for="field-email" class="label">
            {{ Lang::txt('COM_EVENTS_EMAIL') }}
          </label>
          <input type="text"
                 name="email"
                 id="field-email"
                 class="input input-bordered w-full"
                 value="{{ $row->email }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_EVENTS_EMAIL_HINT') }}
          </p>
        </div>

        <div class="admin-field">
          <label for="field-restricted" class="label">
            {{ Lang::txt('COM_EVENTS_RESTRICTED') }}
          </label>
          <input type="text"
                 name="restricted"
                 id="field-restricted"
                 class="input input-bordered w-full"
                 value="{{ $row->restricted }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_EVENTS_RESTRICTED_HINT') }}
          </p>
        </div>

    </x-admin-fieldset>

    {{-- Registration fields params --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_REGISTRATION_FIELDS') }}" body-class="">
        {!! $params->render() !!}
    </x-admin-fieldset>

    {{-- Recurrence (group events only) --}}
    @if($row->scope == 'group')
      <x-admin-fieldset legend="{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE') }}">
          <div class="admin-field">
            <label for="field-repeating_rule" class="label">
              {{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE') }}
            </label>
            <input type="text"
                   name="repeating_rule"
                   id="field-repeating_rule"
                   class="input input-bordered w-full font-mono text-sm"
                   value="{{ $row->repeating_rule }}" />
            <p class="text-xs text-muted-foreground mt-1">
              {!! Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE_HINT', 'http://www.kanzaki.com/docs/ical/rrule.html') !!}
            </p>
          </div>
      </x-admin-fieldset>
    @endif
  @endslot

  {{-- Extra hidden fields not covered by <x-admin-edit> --}}
  <input type="hidden" name="id" value="{{ $row->id }}" />
  <input type="hidden" name="images" value="" />
</x-admin-edit>
