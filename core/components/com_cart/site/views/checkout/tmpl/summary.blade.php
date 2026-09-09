{{--
  Checkout summary — review order before payment.

  Variables from controller (summaryTask):
    $transactionItems — array of transaction items
    $transactionInfo  — transaction info object
    $token            — security token
    $notifications    — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $perks = false;
  if (!empty($transactionInfo->tiPerks)) {
      $perks = unserialize($transactionInfo->tiPerks);
  }

  $membershipInfo = false;
  if (!empty($transactionInfo->tiMeta)) {
      $meta = unserialize($transactionInfo->tiMeta);
      if (!empty($meta['membershipInfo'])) {
          $membershipInfo = $meta['membershipInfo'];
      }
  }

  $orderTotal = $transactionInfo->tiSubtotal
      + $transactionInfo->tiShipping
      - $transactionInfo->tiDiscounts
      - $transactionInfo->tiShippingDiscount;
  $discount = $transactionInfo->tiDiscounts
      + $transactionInfo->tiShippingDiscount;
@endphp

<x-page-container :title="Lang::txt('COM_CART_REVIEW_ORDER')">
  @if(!empty($checkoutSteps))
    <x-step-nav :steps="$checkoutSteps" :current="$currentStepIndex" />
  @endif

  <x-alert-list :notifications="$notifications" />

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Items --}}
    <div>
      {!! $__view->view('_checkout_items', 'checkout')
            ->set('transactionItems', $transactionItems)
            ->set('perks', $perks)
            ->set('membershipInfo', $membershipInfo)
            ->set('tiShippingDiscount', $transactionInfo->tiShippingDiscount)
            ->loadTemplate() !!}
    </div>

    {{-- Order summary --}}
    <div class="card bg-base-100 shadow-sm h-fit">
      <div class="card-body">
        <h2 class="card-title text-base">{{ Lang::txt('COM_CART_ORDER_SUMMARY') }}</h2>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span>{{ Lang::txt('COM_CART_ORDER_SUBTOTAL') }}</span>
            <span>${{ number_format($transactionInfo->tiSubtotal, 2) }}</span>
          </div>
          @if($transactionInfo->tiShipping > 0)
            <div class="flex justify-between">
              <span>{{ Lang::txt('COM_CART_SHIPPING') }}</span>
              <span>${{ number_format($transactionInfo->tiShipping, 2) }}</span>
            </div>
          @endif
          @if($discount > 0)
            <div class="flex justify-between text-success">
              <span>{{ Lang::txt('COM_CART_DISCOUNTS') }}</span>
              <span>-${{ number_format($discount, 2) }}</span>
            </div>
          @endif
          <div class="divider my-1"></div>
          <div class="flex justify-between font-bold text-base">
            <span>{{ Lang::txt('COM_CART_ORDER_TOTAL') }}</span>
            <span>${{ number_format($orderTotal, 2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Notes --}}
  @php
    $notes = [];
    foreach ($transactionItems as $item) {
        $itemMeta = $item['transactionInfo']->tiMeta;
        if (isset($itemMeta->checkoutNotes) && $itemMeta->checkoutNotes) {
            $notes[] = [
                'label' => $item['info']->pName . ', ' . $item['info']->sSku,
                'notes' => $itemMeta->checkoutNotes,
            ];
        }
    }
    $genericNotesLabel = !empty($notes)
        ? Lang::txt('COM_CART_OTHER_NOTES') : '';
    if ($transactionInfo->tiNotes) {
        $notes[] = [
            'label' => $genericNotesLabel,
            'notes' => $transactionInfo->tiNotes,
        ];
    }
    $notesChangeUrl = Route::url('index.php?option=com_cart', false)
        . 'checkout/notes';
  @endphp
  @if(!empty($notes))
    <div class="card bg-base-100 shadow-sm mt-6">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <h2 class="card-title text-base">{{ Lang::txt('COM_CART_NOTES_COMMENTS') }}</h2>
          <a href="{{ $notesChangeUrl }}" class="link link-primary text-sm">
            {{ Lang::txt('COM_CART_CHANGE') }}
          </a>
        </div>
        @foreach($notes as $note)
          <p class="text-sm">
            @if($note['label'])
              <strong>{{ $note['label'] }}:</strong>
            @endif
            {{ $note['notes'] }}
          </p>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Shipping info --}}
  @if(in_array('shipping', $transactionInfo->steps))
    {!! $__view->view('_checkout_shippinginfo', 'checkout')
          ->set('transactionInfo', $transactionInfo)
          ->loadTemplate() !!}
  @endif

  {{-- Action button --}}
  @php
    if ($orderTotal > 0) {
        $buttonLabel = Lang::txt('COM_CART_PROCEED_TO_PAYMENT');
        $buttonLink = Route::url(
            'index.php?option=com_cart&controller=checkout/payment',
            false
        );
    } else {
        $buttonLabel = Lang::txt('COM_CART_PLACE_ORDER');
        $buttonLink = Route::url(
            'index.php?option=com_cart&controller=order/place/' . $token,
            false
        );
    }
  @endphp
  <div class="mt-6">
    <a href="{{ $buttonLink }}" class="btn btn-primary">{{ $buttonLabel }}</a>
  </div>
</x-page-container>
