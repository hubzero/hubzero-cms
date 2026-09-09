{{--
  Payment method selection — displays payment options from plugins.

  Variables from controller (paymentTask):
    $transaction — transaction object

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $paymentOptions = Event::trigger(
      'cart.onRenderPaymentOptions',
      [$transaction, User::getRoot()]
  );
@endphp

<x-page-container :title="Lang::txt('COM_CART_PAYMENT')">
  @if(count($paymentOptions) > 1)
    <p class="mb-4">{{ Lang::txt('COM_CART_CHOOSE_PAYMENT') }}</p>
  @endif

  @if(count($paymentOptions))
    <div class="space-y-4">
      @foreach($paymentOptions as $method)
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-base">{{ $method['title'] }}</h3>
            {!! $method['options'] !!}
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div role="alert" class="alert alert-warning">
      <span>{{ Lang::txt('COM_CART_NO_PAYMENT_OPTIONS') }}</span>
    </div>
  @endif
</x-page-container>
