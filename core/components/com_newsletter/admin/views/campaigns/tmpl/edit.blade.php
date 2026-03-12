{{--
  Campaign — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $canDo     = \Components\Newsletter\Helpers\Permissions::getActions('campaign');
  $hasSecret = strlen($campaign->secret) > 0;
  $text      = $hasSecret ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW');

  $exDate = $campaign->expire_date
      ? Date::of($campaign->expire_date)
      : Date::of('+90 days');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_CAMPAIGN') }}: {{ $text }}"
    icon="campaigns"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ $text }} {{ Lang::txt('COM_NEWSLETTER_CAMPAIGN') }}">

      @if($hasSecret)
        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_NEWSLETTER_CAMPAIGN_ID') }}</label>
          <p class="text-sm">{{ $campaign->id }}</p>
        </div>
      @endif

      <div class="admin-field">
        <label for="campaign-title" class="label">
          {{ Lang::txt('COM_NEWSLETTER_CAMPAIGN_NAME') }}
        </label>
        <input type="text"
               name="campaign[title]"
               id="campaign-title"
               class="input input-bordered w-full"
               value="{{ $campaign->title }}" />
      </div>

      <div class="admin-field">
        <label for="campaign-expire_date" class="label">
          {{ Lang::txt('COM_NEWSLETTER_CAMPAIGN_EXPIRE_DATE') }}
        </label>
        <input type="date"
               name="campaign[expire_date_display]"
               id="campaign-expire_date"
               class="input input-bordered w-full"
               value="{{ Date::of($exDate)->toLocal('Y-m-d') }}" />
      </div>

      <div class="admin-field">
        <label for="campaign-description" class="label">
          {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_DESC') }}
        </label>
        <textarea name="campaign[description]"
                  id="campaign-description"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $campaign->description }}</textarea>
      </div>

      @if($hasSecret)
        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox"
                   name="params[reset_secret]"
                   id="cb-reset-secret"
                   class="checkbox checkbox-sm"
                   value="1" />
            <span>{{ Lang::txt('Reset Campaign Secret') }}</span>
          </label>
        </div>
      @endif

  </x-admin-fieldset>

  <input type="hidden" name="campaign[id]" value="{{ $campaign->id }}" />
  <input type="hidden" name="campaign[expire_date_gmt]" value="{{ Date::of($exDate, 'GMT') }}" />
  <input type="hidden" name="campaign[expire_date_local]" value="{{ Date::of($exDate)->toLocal() }}" />
</x-admin-edit>
