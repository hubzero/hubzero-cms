{{--
  Checkout shipping — address entry form with saved addresses sidebar.

  Variables from controller (shippingTask):
    $savedShippingAddresses — array of saved address objects
    $notifications          — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $states = \Components\Cart\Helpers\Helper::getUsStates();

  $firstName = e(Request::getString('shippingToFirst', User::get('givenName'), 'post'));
  $lastName = e(Request::getString('shippingToLast', User::get('surname'), 'post'));
  $address = e(Request::getString('shippingAddress', '', 'post'));
  $city = e(Request::getString('shippingCity', '', 'post'));
  $zip = e(Request::getString('shippingZip', '', 'post'));
  $selectedState = Request::getString('shippingState', '', 'post');
@endphp

<x-page-container :title="Lang::txt('COM_CART_CHECKOUT_SHIPPING')">
  @if(!empty($checkoutSteps))
    <x-step-nav :steps="$checkoutSteps" :current="$currentStepIndex" />
  @endif

  @slot('sidebar')
    @if(!empty($savedShippingAddresses))
      <x-sidebar-card :title="Lang::txt('COM_CART_SELECT_SAVED_ADDRESS')">
          <div class="space-y-3">
            @foreach($savedShippingAddresses as $addr)
              <div class="border border-base-300 rounded-lg p-3">
                <p class="text-sm">
                  {{ $addr->saToFirst }} {{ $addr->saToLast }}<br>
                  {{ $addr->saAddress }}<br>
                  {{ $addr->saCity }}, {{ $addr->saState }} {{ $addr->saZip }}
                </p>
                @php
                  $selectUrl = Route::url(
                      'index.php?option=com_cart&controller=checkout/shipping/select/'
                      . $addr->saId,
                      false
                  );
                @endphp
                <a href="{{ $selectUrl }}" class="link link-primary text-sm mt-1 inline-block">
                  {{ Lang::txt('COM_CART_SHIP_TO_ADDRESS') }}
                </a>
              </div>
            @endforeach
          </div>
      </x-sidebar-card>
    @endif
  @endslot

  <x-alert-list :notifications="$notifications" />

  <form name="cartShippingInfo" method="post" class="max-w-lg">
    <x-form-section :heading="Lang::txt('COM_CART_SHIPPING_ADDRESS')">
      <x-form-field name="shippingToFirst" :label="Lang::txt('COM_CART_FIRST_NAME')">
        <input type="text" name="shippingToFirst" id="shippingToFirst"
               class="input input-bordered w-full" value="{{ $firstName }}" />
      </x-form-field>

      <x-form-field name="shippingToLast" :label="Lang::txt('COM_CART_LAST_NAME')">
        <input type="text" name="shippingToLast" id="shippingToLast"
               class="input input-bordered w-full" value="{{ $lastName }}" />
      </x-form-field>

      <x-form-field name="shippingAddress" :label="Lang::txt('COM_CART_SHIPPING_ADDRESS')">
        <input type="text" name="shippingAddress" id="shippingAddress"
               class="input input-bordered w-full" value="{{ $address }}" />
      </x-form-field>

      <x-form-field name="shippingCity" :label="Lang::txt('COM_CART_CITY')">
        <input type="text" name="shippingCity" id="shippingCity"
               class="input input-bordered w-full" value="{{ $city }}" />
      </x-form-field>

      <div class="grid grid-cols-2 gap-4">
        <x-form-field name="shippingZip" :label="Lang::txt('COM_CART_ZIP')">
          <input type="text" name="shippingZip" id="shippingZip"
                 class="input input-bordered w-full" value="{{ $zip }}" />
        </x-form-field>

        <x-form-field name="shippingState" :label="Lang::txt('COM_CART_STATE')">
          <select name="shippingState" id="shippingState"
                  class="select select-bordered w-full">
            <option value="">{{ Lang::txt('COM_CART_SELECT_STATE') }}</option>
            @foreach($states as $abbr => $state)
              <option value="{{ $abbr }}" {{ $selectedState == $abbr ? 'selected' : '' }}>
                {{ $state }}
              </option>
            @endforeach
          </select>
        </x-form-field>
      </div>

      <x-form-field name="saveAddress" :label="Lang::txt('COM_CART_SAVE_ADDRESS_FUTURE')" type="checkbox">
          <input type="checkbox" class="checkbox checkbox-sm"
                 name="saveAddress" id="saveAddress" />
      </x-form-field>

      <div class="mt-4">
        <button type="submit" name="submitShippingInfo" id="submitShippingInfo"
                class="btn btn-primary">
          {{ Lang::txt('COM_CART_NEXT') }}
        </button>
      </div>
    </x-form-section>
  </form>
</x-page-container>
