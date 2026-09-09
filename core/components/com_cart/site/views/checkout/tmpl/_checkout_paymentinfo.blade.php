{{--
  Payment info display — partial for confirm view.

  Variables:
    $paymentInfo — HTML string describing payment method

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $changeUrl = Route::url('index.php?option=com_cart', false)
      . 'checkout/payment?update=true';
@endphp

<div class="card bg-base-100 shadow-sm mt-6">
  <div class="card-body">
    <div class="flex items-center justify-between">
      <h2 class="card-title text-base">{{ Lang::txt('COM_CART_PAYMENT_INFO') }}</h2>
      <a href="{{ $changeUrl }}" class="link link-primary text-sm">
        {{ Lang::txt('COM_CART_CHANGE') }}
      </a>
    </div>
    <p class="text-sm">{!! $paymentInfo !!}</p>
  </div>
</div>
