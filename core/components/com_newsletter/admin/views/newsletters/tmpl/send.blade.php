{{--
  Newsletter — Send view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_SEND') }}: {{ $newsletter->name }}"
    icon="newsletter"
    option="{{ $option }}"
    :edit="true"
/>

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  @if($newsletter->id)
    <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_SEND') }}">

        {{-- Newsletter name --}}
        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER') }}</label>
          <p class="text-sm font-semibold">{{ $newsletter->name }}</p>
        </div>

        {{-- Previous mailings --}}
        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SENT_PREVIOUSLY') }}</label>
          <div class="text-sm space-y-1">
            @if(count($mailings) > 0)
              @foreach($mailings as $mailing)
                @php
                  $sent = Date::of($mailing->date);
                  $now  = Date::of('now');

                  if ($sent > $now) {
                      $status     = 'Scheduled';
                      $badgeClass = 'badge-info';
                  } elseif ($mailing->recipients()->whereEquals('status', 'queued')->total() == 0) {
                      $status     = 'Sent';
                      $badgeClass = 'badge-success';
                  } else {
                      $status     = 'In Progress';
                      $badgeClass = 'badge-warning';
                  }
                @endphp
                <div>
                  <span class="badge badge-sm {{ $badgeClass }}">{{ $status }}</span>
                  {{ $sent->format('l, M d, Y @ g:ia') }}
                </div>
              @endforeach
            @else
              <span class="badge badge-sm badge-error">{{ Lang::txt('JNO') }}</span>
            @endif
          </div>
        </div>

        {{-- Scheduler --}}
        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE') }}</label>
          <div id="scheduler" class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="scheduler" value="1" checked
                     class="radio radio-sm" />
              <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_NOW') }}</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="scheduler" value="0"
                     class="radio radio-sm" />
              <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER') }}</span>
            </label>

            <div id="scheduler-alt" class="pl-6 flex flex-wrap items-center gap-2 text-sm">
              <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_DATE') }}</span>
              <label for="scheduler_date" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_DATE') }}</label>
              <input type="text"
                     name="scheduler_date"
                     id="scheduler_date"
                     class="input input-bordered input-sm w-40" />

              <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_TIME') }}</span>
              @php
                $optNull = Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_NULL');
              @endphp
              <label for="scheduler_date_hour" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_TIME') }} — Hour</label>
              <select name="scheduler_date_hour" id="scheduler_date_hour"
                      class="select select-bordered select-sm w-20">
                <option value="">{{ $optNull }}</option>
                @for($h = 1; $h < 13; $h++)
                  <option value="{{ $h }}">{{ $h }}</option>
                @endfor
              </select>
              <label for="scheduler_date_minute" class="sr-only">Minute</label>
              <select name="scheduler_date_minute" id="scheduler_date_minute"
                      class="select select-bordered select-sm w-20">
                <option value="">{{ $optNull }}</option>
                @for($m = 0; $m < 60; $m += 5)
                  @php $min = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                  <option value="{{ $min }}">{{ $min }}</option>
                @endfor
              </select>
              <label for="scheduler_date_meridian" class="sr-only">AM/PM</label>
              <select name="scheduler_date_meridian" id="scheduler_date_meridian"
                      class="select select-bordered select-sm w-20">
                <option value="">{{ $optNull }}</option>
                <option value="am">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_AM') }}</option>
                <option value="pm">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_PM') }}</option>
              </select>
              <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_EST') }}</span>
            </div>
          </div>
        </div>

        {{-- Mailing list --}}
        <div class="admin-field">
          <label for="mailinglist" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST') }}</label>
          <select name="mailinglist" id="mailinglist" class="select select-bordered w-full">
            <option value="">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_OPTION_NULL') }}</option>
            <option value="-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_OPTION_DEFAULT') }}</option>
            @foreach($mailinglists as $list)
              <option value="{{ $list->id }}">{{ $list->name }}</option>
            @endforeach
          </select>
          <p id="mailinglist-count" class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_RECIEVE') }}
            <span id="mailinglist-count-count"></span>
            <span id="mailinglist-emails"></span>
          </p>
        </div>

    </x-admin-fieldset>
  @endif

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="dosendnewsletter" />
  <input type="hidden" name="nid" value="{{ $newsletter->id }}" />
</form>
