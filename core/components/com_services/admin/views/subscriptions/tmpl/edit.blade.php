{{--
  Subscriptions — Admin edit/manage

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;

  $__view->css();

  $dateFmt = Lang::txt('DATE_FORMAT_HZ1');
  $na      = Lang::txt('COM_SERVICES_NOT_APPLICABLE');
  $now     = Date::toSql();

  $added   = (intval($subscription->added) <> 0)
      ? Date::of($subscription->added)->toLocal($dateFmt) : null;
  $updated = (intval($subscription->updated) <> 0)
      ? Date::of($subscription->updated)->toLocal($dateFmt) : $na;
  $expires = (intval($subscription->expires) <> 0)
      ? Date::of($subscription->expires)->toLocal($dateFmt) : $na;

  $priceAmount = $subscription->currency . ' ' . $subscription->unitprice;
  $priceline   = Lang::txt('COM_SERVICES_PRICE_PER_UNIT', $priceAmount, $subscription->unitmeasure);
  if ($subscription->pointsprice > 0) {
      $priceline .= Lang::txt('COM_SERVICES_OR_POINTS', $subscription->pointsprice);
  }

  $currencyLabel = $subscription->usepoints
      ? Lang::txt('COM_SERVICES_POINTS')
      : $subscription->currency;

  $onhold_msg = ($subscription->status == 2)
      ? Lang::txt('COM_SERVICES_SEND_MESSAGE')
      : Lang::txt('COM_SERVICES_SUBSCRIPTION_ON_HOLD');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SERVICES') }}: {{ Lang::txt('COM_SERVICES_SUBSCRIPTIONS') }}"
    icon="services"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Subscription Info --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_SERVICES_SUBSCRIPTION_NUM', $subscription->id, $subscription->code) }}">

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_SERVICES_FIELD_SERVICE') }}</label>
        <p>{{ $subscription->title }} &mdash; <strong>{{ $priceline }}</strong></p>
      </div>

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_SERVICES_FIELD_PROFILE') }}</label>
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>Login</td>
              <td>{{ $customer->get('username') }}</td>
            </tr>
            <tr>
              <td>Name</td>
              <td>{{ $customer->get('name') }}</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>{{ $customer->get('email') }}</td>
            </tr>
            <tr>
              <td>Tel.</td>
              <td>{{ $customer->get('phone') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_SERVICES_FIELD_EMPLOYER') }}</label>
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>Company Name</td>
              <td>{{ $subscription->companyName }}</td>
            </tr>
            <tr>
              <td>Company Location</td>
              <td>{{ $subscription->companyLocation }}</td>
            </tr>
            <tr>
              <td>Company URL</td>
              <td>{{ $subscription->companyWebsite }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="admin-field">
        <label for="field-notes" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_NOTES') }}
        </label>
        <textarea name="notes"
                  id="field-notes"
                  class="textarea textarea-bordered w-full"
                  rows="6">{{ $subscription->notes }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Status info --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_SERVICES_COL_STATUS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_STATUS') }}</td>
              <td>
                @switch($subscription->status)
                  @case(1)
                    @if ($subscription->expires > $now)
                      <span class="badge badge-success">{{ Lang::txt('COM_SERVICES_STATE_ACTIVE') }}</span>
                    @else
                      <span class="badge badge-warning">{{ Lang::txt('COM_SERVICES_STATE_EXPIRED') }}</span>
                    @endif
                    @break
                  @case(0)
                    <span class="badge badge-info">{{ Lang::txt('COM_SERVICES_STATE_PENDING') }}</span>
                    @break
                  @case(2)
                    <span class="badge badge-ghost">{{ Lang::txt('COM_SERVICES_STATE_CANCELED') }}</span>
                    @break
                @endswitch
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_ADDED') }}</td>
              <td>{{ $added }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_EXPIRES') }}</td>
              <td>{{ $expires }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_LAST_UPDATED') }}</td>
              <td>{{ $updated }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_TOTAL_PAID') }}</td>
              <td>{{ $subscription->totalpaid }} {{ $currencyLabel }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_PENDING_PAYMENT') }}</td>
              <td>{{ $subscription->pendingpayment }} {{ $currencyLabel }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_ACTIVE_UNITS') }}</td>
              <td>{{ $subscription->units }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_COL_PENDING_UNITS') }}</td>
              <td>{{ $subscription->pendingunits }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Actions --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_SERVICES_FIELDSET_MANAGE') }}">

        {{-- Send message option (always available) --}}
        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="radio" name="action" value="message" class="radio radio-sm" />
            <span>{{ $onhold_msg }}</span>
          </label>
        </div>

        @if ($subscription->status == 2)
          {{-- Cancelled: refund options --}}
          @if ($subscription->pendingpayment > 0)
            <div class="admin-field">
              <label class="label cursor-pointer justify-start gap-2">
                <input type="radio" name="action" value="refund" class="radio radio-sm" />
                <span>{{ Lang::txt('COM_SERVICES_FIELD_PROCESS_REFUND') }}</span>
              </label>
            </div>

            <div class="admin-field">
              <label class="label">
                {{ Lang::txt('COM_SERVICES_FIELD_PENDING_REFUND_FOR', $subscription->pendingunits) }}
              </label>
              <p class="text-sm">{{ $subscription->pendingpayment }} {{ $currencyLabel }}</p>
            </div>

            <div class="admin-field">
              <label for="field-received_refund" class="label">
                {{ Lang::txt('COM_SERVICES_FIELD_REFUND_POSTED') }}
              </label>
              <div class="flex items-center gap-2">
                <input type="text"
                       name="received_refund"
                       id="field-received_refund"
                       class="input input-bordered input-sm w-28"
                       value="{{ $subscription->pendingpayment }}" />
                <span class="text-sm">{{ $currencyLabel }}</span>
              </div>
            </div>
          @endif
        @else
          {{-- Active/Pending: activate/extend options --}}
          <div class="admin-field">
            <label class="label cursor-pointer justify-start gap-2">
              <input type="radio" name="action" value="activate" class="radio radio-sm" />
              <span>{{ Lang::txt('COM_SERVICES_FIELD_ACTIVATE') }}</span>
            </label>
          </div>

          <div class="admin-field">
            <label for="field-received_payment" class="label">
              {{ Lang::txt('COM_SERVICES_FIELD_PAYMENT_RECEIVED') }}
            </label>
            <div class="flex items-center gap-2">
              @if ($subscription->pendingpayment > 0)
                <input type="text"
                       name="received_payment"
                       id="field-received_payment"
                       class="input input-bordered input-sm w-28"
                       value="{{ $subscription->pendingpayment }}" />
              @else
                <span class="text-sm">{{ $subscription->pendingpayment }}</span>
              @endif
              <span class="text-sm">{{ $currencyLabel }}</span>
            </div>
          </div>

          <div class="admin-field">
            <label for="field-newunits" class="label">
              {{ Lang::txt('COM_SERVICES_FIELD_ACTIVE_UNITS') }}
            </label>
            @php
              $showUnitsInput = $subscription->pendingunits > 0
                  || $subscription->expires < $now;
            @endphp
            @if ($showUnitsInput)
              <input type="text"
                     name="newunits"
                     id="field-newunits"
                     class="input input-bordered input-sm w-28"
                     value="{{ $subscription->pendingunits }}" />
            @else
              <span class="text-sm">{{ $subscription->pendingunits }}</span>
            @endif
          </div>

          <div class="admin-field">
            <label class="label cursor-pointer justify-start gap-2">
              <input type="radio" name="action" value="cancelsub" class="radio radio-sm" />
              <span>{{ Lang::txt('COM_SERVICES_FIELD_CANCEL_SUBSCRIPTION') }}</span>
            </label>
          </div>
        @endif

        {{-- Message textarea (always shown) --}}
        <div class="admin-field">
          <label for="field-message" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_SEND_MESSAGE') }}
          </label>
          <textarea name="message"
                    id="field-message"
                    class="textarea textarea-bordered w-full"
                    rows="4"></textarea>
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="usepoints" value="{{ $subscription->usepoints }}" />
  <input type="hidden" name="id" value="{{ $subscription->id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
