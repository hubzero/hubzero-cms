{{--
  Member Activity — notification digest settings.

  Variables from plugin (onMembers):
    $member   — member profile object
    $settings — digest settings object

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()->js();
@endphp

@if ($__view->getError())
  <div class="alert alert-error mb-4" role="alert">{{ $__view->getError() }}</div>
@endif

<form action="{{ Route::url($member->link() . '&active=activity&task=savesettings') }}"
      method="post" class="max-w-lg">
  <fieldset class="space-y-4">
    <legend class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_DIGESTS') }}</legend>

    @php
      $frequencies = [
          0 => Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_NONE'),
          2 => Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_DAILY'),
          3 => Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_WEEKLY'),
          4 => Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_MONTHLY'),
      ];
    @endphp

    @foreach ($frequencies as $value => $label)
      <label class="flex items-center gap-3 cursor-pointer" for="field-settings-frequency-{{ $value }}">
        <input type="radio" name="settings[frequency]" id="field-settings-frequency-{{ $value }}"
               value="{{ $value }}" class="radio radio-primary"
               {{ $settings->get('frequency') == $value ? 'checked' : '' }} />
        <span class="label-text">{{ $label }}</span>
      </label>
    @endforeach

    <input type="hidden" name="settings[id]" value="{{ $settings->get('id', 0) }}" />
    <input type="hidden" name="settings[scope_id]" value="{{ $member->get('id') }}" />
    <input type="hidden" name="settings[scope]" value="user" />
  </fieldset>

  <input type="hidden" name="id" value="{{ $member->get('id') }}" />
  <input type="hidden" name="option" value="com_members" />
  <input type="hidden" name="active" value="activity" />
  <input type="hidden" name="action" value="savesettings" />

  {!! Html::input('token') !!}

  <div class="flex gap-2 mt-6">
    <button type="submit" class="btn btn-primary">
      {{ Lang::txt('PLG_MEMBERS_ACTIVITY_SAVE') }}
    </button>
    <a class="btn btn-ghost" href="{{ Route::url($member->link() . '&active=activity') }}">
      {{ Lang::txt('JCANCEL') }}
    </a>
  </div>
</form>
