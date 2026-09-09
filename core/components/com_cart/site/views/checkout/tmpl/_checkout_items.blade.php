{{--
  Checkout items table — shared partial for summary and confirm views.

  Variables:
    $transactionItems  — array of transaction items
    $perks             — unserialized perks or false
    $membershipInfo    — membership info array or false
    $tiShippingDiscount — shipping discount amount

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

@if(!empty($transactionItems))
  <div class="overflow-x-auto">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_CART_ITEM') }}</th>
          <th>{{ Lang::txt('COM_CART_PRICE') }}</th>
          <th>{{ Lang::txt('COM_CART_QUANTITY') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactionItems as $sId => $item)
          @php
            $info = $item['info'];
            $transactionItemInfo = $item['transactionInfo'];
          @endphp
          <tr>
            <td>
              {{ $info->pName }}
              @if(!empty($item['options']))
                @foreach($item['options'] as $oName), {{ $oName }}@endforeach
              @endif
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
            <td>${{ number_format($transactionItemInfo->tiPrice, 2) }}</td>
            <td>{{ $transactionItemInfo->qty }}</td>
          </tr>

          {{-- Per-item coupon discount --}}
          @if(!empty($perks['items'][$sId]))
            <tr>
              <td class="text-success text-sm">
                {{ Lang::txt('COM_CART_COUPON_DISCOUNT') }}:
                {{ $perks['items'][$sId]->name }}
              </td>
              <td class="text-success">
                -${{ number_format($perks['items'][$sId]->discount, 2) }}
              </td>
              <td></td>
            </tr>
          @endif
        @endforeach

        {{-- Generic coupon discounts --}}
        @if(!empty($perks['generic']))
          @foreach($perks['generic'] as $coupon)
            @if($coupon->discount)
              <tr>
                <td class="text-success text-sm">
                  {{ Lang::txt('COM_CART_COUPON_DISCOUNT') }}:
                  {{ $coupon->name }}
                </td>
                <td class="text-success">
                  -${{ number_format($coupon->discount, 2) }}
                </td>
                <td></td>
              </tr>
            @endif
          @endforeach
        @endif

        {{-- Shipping discount --}}
        @if(!empty($perks['shipping']) && !empty($tiShippingDiscount) && $tiShippingDiscount > 0)
          <tr>
            <td class="text-success text-sm">
              {{ Lang::txt('COM_CART_COUPON_DISCOUNT') }}:
              {{ $perks['shipping']->name }}
            </td>
            <td class="text-success">
              -${{ number_format($tiShippingDiscount, 2) }}
            </td>
            <td></td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
@endif
