{{--
  Shopping cart — display cart items with summary sidebar.

  Variables from controller (homeTask):
    $cartInfo       — Cart object with items, totalItems, totalCart
    $couponPerks    — array: items, generic, shipping, info
    $membershipInfo — array keyed by SKU ID
    $notifications  — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $ordersUrl = Route::url('index.php?option=com_cart', false) . 'orders';
  $cartUrl = Route::url('index.php?option=' . $option, false);
  $shopUrl = Route::url('index.php?option=com_storefront', false);
  $checkoutUrl = Route::url(
      'index.php?option=' . $option . '&controller=checkout',
      false
  );

  $itemsPerks = !empty($couponPerks['items']) ? $couponPerks['items'] : [];
@endphp

<x-page-container :title="Lang::txt('COM_CART')">
  @slot('actions')
    <a class="btn btn-ghost btn-sm" href="{{ $ordersUrl }}">
      {{ Lang::txt('COM_CART_ORDERS') }}
    </a>
  @endslot

  @slot('sidebar')
    {{-- Cart summary --}}
    @if(!empty($cartInfo) && $cartInfo->totalItems > 0)
      <x-sidebar-card :title="Lang::txt('COM_CART_SUMMARY')">
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span>{{ Lang::txt('COM_CART_ITEMS') }}</span>
              <span>{{ $cartInfo->totalItems }}</span>
            </div>
            <div class="flex justify-between">
              <span>{{ Lang::txt('COM_CART_ITEMS_SUBTOTAL') }}</span>
              <span>${{ number_format($cartInfo->totalCart, 2) }}</span>
            </div>

            @php $discountsTotal = 0; @endphp

            @if(!empty($couponPerks['info']->itemsDiscountsTotal))
              @php $discountsTotal += $couponPerks['info']->itemsDiscountsTotal; @endphp
              <div class="flex justify-between text-success">
                <span>{{ Lang::txt('COM_CART_ITEMS_DISCOUNTS') }}</span>
                <span>-${{ number_format($couponPerks['info']->itemsDiscountsTotal, 2) }}</span>
              </div>
            @endif

            @if(!empty($couponPerks['generic']))
              @foreach($couponPerks['generic'] as $perk)
                <div class="flex justify-between">
                  <span class="text-success">{{ $perk->name }}</span>
                  <span class="text-success">
                    @if($perk->discount > 0)
                      -${{ number_format($perk->discount, 2) }}
                    @else
                      {{ Lang::txt('COM_CART_APPLIED_AT_CHECKOUT') }}
                    @endif
                  </span>
                </div>
                @php $discountsTotal += $perk->discount; @endphp
              @endforeach
            @endif

            @if(!empty($couponPerks['shipping']))
              <div class="flex justify-between">
                <span class="text-success">{{ $couponPerks['shipping']->name }}</span>
                <span class="text-sm text-base-content/60">
                  {{ Lang::txt('COM_CART_APPLIED_AT_CHECKOUT') }}
                </span>
              </div>
            @endif

            @if($discountsTotal > 0)
              <div class="divider my-1"></div>
              <div class="flex justify-between font-semibold">
                <span>{{ Lang::txt('COM_CART_SUBTOTAL') }}</span>
                <span>${{ number_format($cartInfo->totalCart - $discountsTotal, 2) }}</span>
              </div>
            @endif
          </div>

          @if($cartInfo->totalItems)
            <div class="card-actions mt-4">
              <a href="{{ $checkoutUrl }}" class="btn btn-primary btn-block">
                {{ Lang::txt('COM_CART_CHECKOUT') }}
              </a>
            </div>
          @endif
      </x-sidebar-card>
    @endif

    {{-- Coupon code --}}
    <x-sidebar-card :title="Lang::txt('COM_CART_COUPON_TITLE')">
        <form name="couponCodes" id="couponCodes" method="post">
          <div class="join w-full">
            <input type="text" name="couponCode" id="couponCode"
                   class="input input-bordered join-item w-full"
                   placeholder="{{ Lang::txt('COM_CART_COUPON_PLACEHOLDER') }}" />
            <button type="submit" name="addCouponCode" id="addCouponCode"
                    class="btn btn-primary join-item">
              {{ Lang::txt('COM_CART_APPLY') }}
            </button>
          </div>
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          {!! Html::input('token') !!}
        </form>
    </x-sidebar-card>
  @endslot

  <x-alert-list :notifications="$notifications" />

  {{-- Cart items --}}
  <form action="{{ $cartUrl }}" name="shoppingCart" id="shoppingCart" method="post">
    @if(!empty($cartInfo->items))
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>{{ Lang::txt('COM_CART_ITEM') }}</th>
              <th>{{ Lang::txt('COM_CART_QTY') }}</th>
              <th class="text-right">{{ Lang::txt('COM_CART_PRICE') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($cartInfo->items as $sId => $item)
              @php
                $info = $item['info'];
                if (!$item['cartInfo']->qty) { continue; }
                $productUrl = Route::url('index.php?option=com_storefront', false)
                    . '/product/' . $info->pId;
              @endphp
              <tr>
                <td>
                  <a href="{{ $productUrl }}"
                     class="link link-hover text-primary font-medium">
                    {{ $info->pName }}
                    @if(!empty($item['options']))
                      @foreach($item['options'] as $oName), {{ $oName }}@endforeach
                    @endif
                  </a>
                  @if(!empty($membershipInfo[$sId]))
                    <p class="text-sm text-base-content/60 mt-1">
                      @if(!empty($membershipInfo[$sId]->existingExpires))
                        {{ Lang::txt('COM_CART_EXTEND_SUBSCRIPTION', date('M j, Y', $membershipInfo[$sId]->existingExpires)) }}
                      @else
                        {{ Lang::txt('COM_CART_ITEM_VALID') }}
                      @endif
                      {{ Lang::txt('COM_CART_VALID_UNTIL', date('M j, Y', $membershipInfo[$sId]->newExpires)) }}
                    </p>
                  @endif
                </td>
                <td>
                  @if($info->sAllowMultiple)
                    <input type="number" maxlength="2" pattern="[0-9]*" min="0"
                           class="input input-bordered input-sm w-20"
                           name="skus[{{ $info->sId }}]"
                           value="{{ $item['cartInfo']->qty }}" />
                  @endif
                </td>
                <td class="text-right">
                  <span class="font-medium">
                    ${{ number_format($info->sPrice * $item['cartInfo']->qty, 2) }}
                  </span>
                  @if($item['cartInfo']->qty > 1)
                    <br>
                    <span class="text-sm text-base-content/60">
                      ${{ number_format($info->sPrice, 2) }} each
                    </span>
                  @endif
                  <div class="mt-1">
                    <button type="submit" name="delete_{{ $info->sId }}"
                            value="delete"
                            class="btn btn-ghost btn-xs text-error">
                      {{ Lang::txt('COM_CART_DELETE') }}
                    </button>
                  </div>
                </td>
              </tr>
              @if(!empty($itemsPerks[$sId]))
                <tr>
                  <td class="text-success text-sm">{{ $itemsPerks[$sId]->name }}</td>
                  <td></td>
                  <td class="text-right text-success">
                    -${{ number_format($itemsPerks[$sId]->discount, 2) }}
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex justify-between mt-4">
        <a href="{{ $shopUrl }}" class="btn btn-ghost">
          {{ Lang::txt('COM_CART_CONTINUE_SHOPPING') }}
        </a>
        <button type="submit" name="updateCart" id="updateCart"
                class="btn btn-outline">
          {{ Lang::txt('COM_CART_UPDATE_CART') }}
        </button>
      </div>
    @else
      <x-empty-state :title="Lang::txt('COM_CART_EMPTY')">
        <a href="{{ $shopUrl }}" class="btn btn-primary">
          {{ Lang::txt('COM_CART_START_SHOPPING') }}
        </a>
      </x-empty-state>
    @endif

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    {!! Html::input('token') !!}
  </form>
</x-page-container>
