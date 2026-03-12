{{--
  Order Edit — Admin edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('order');

  Toolbar::title(Lang::txt('COM_CART') . ': Edit order info');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $userName = $user->get('id')
      ? e($user->get('name')) . ' (' . e($user->get('username')) . ')'
      : Lang::txt('COM_CART_UNKNOWN');

  $formUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller,
      false, false
  );
@endphp

<form action="{{ $formUrl }}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Left column: Order details, Shipping, Payment --}}
    <div class="space-y-6">
      <x-admin-fieldset legend="Order Details">
        <table class="table table-sm">
          <tbody>
            <tr>
              <th class="w-40">Order number:</th>
              <td>{{ $tInfo->tId }}</td>
            </tr>
            <tr>
              <th>Order placed:</th>
              <td><time datetime="{{ $tInfo->tLastUpdated }}">{{ $tInfo->tLastUpdated }}</time></td>
            </tr>
            <tr>
              <th>Ordered by:</th>
              <td>{{ $userName }}</td>
            </tr>
            <tr>
              <th>Order subtotal:</th>
              <td>${{ number_format($tInfo->tiSubtotal, 2) }}</td>
            </tr>
            @if(!empty($tInfo->tiTax) && $tInfo->tiTax)
              <tr>
                <th>Tax:</th>
                <td>${{ number_format($tInfo->tiTax, 2) }}</td>
              </tr>
            @endif
            @if(!empty($tInfo->tiShipping) && floatval($tInfo->tiShipping))
              <tr>
                <th>Shipping cost:</th>
                <td>${{ number_format($tInfo->tiShipping, 2) }}</td>
              </tr>
            @endif
            @if(!empty($tInfo->tiDiscounts) && floatval($tInfo->tiDiscounts))
              <tr>
                <th>Discounts:</th>
                <td>${{ number_format($tInfo->tiDiscounts, 2) }}</td>
              </tr>
            @endif
            <tr class="font-semibold">
              <th>Order total:</th>
              <td>${{ number_format($tInfo->tiTotal, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>

      @if(!empty($tInfo->tiShippingToFirst))
        <x-admin-fieldset legend="Shipping info">
          <p>
            <strong>Ship to:</strong><br />
            {{ $tInfo->tiShippingToFirst }} {{ $tInfo->tiShippingToLast }}<br />
            {{ $tInfo->tiShippingAddress }}<br />
            {{ $tInfo->tiShippingCity }}, {{ $tInfo->tiShippingState }} {{ $tInfo->tiShippingZip }}
          </p>
        </x-admin-fieldset>
      @endif

      @if(!empty($tInfo->tiPayment))
        <x-admin-fieldset legend="Payment info">
          <p>Payment method: {{ $tInfo->tiPayment }}</p>
          @if(!empty($tInfo->tiPaymentDetails))
            <div>
              <label for="tiPaymentDetails" class="label text-base-content">
                <span class="label-text text-base-content font-semibold">Payment details:</span>
              </label>
              <input type="text"
                     name="tiPaymentDetails"
                     id="tiPaymentDetails"
                     class="input input-bordered input-sm w-full"
                     value="{{ $tInfo->tiPaymentDetails }}" />
            </div>
          @endif
        </x-admin-fieldset>
      @endif
    </div>

    {{-- Right column: Items & Notes --}}
    <div class="space-y-6">
      <x-admin-fieldset legend="Items Ordered">
        <div class="overflow-x-auto">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Product</th>
                <th class="w-28">Price</th>
                <th class="w-20">QTY</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $sId => $itemOrdered)
                @php
                  $itemInfo = $itemOrdered['info'];
                  $ti = $itemOrdered['transactionInfo'];
                @endphp
                <tr>
                  <td>
                    @if($itemInfo->available)
                      @php
                        $pUrl = Route::url(
                            'index.php?option=com_storefront&controller=products&task=edit&id=' . $itemInfo->pId,
                            false, false
                        );
                        $sUrl = Route::url(
                            'index.php?option=com_storefront&controller=skus&task=edit&id=' . $itemInfo->sId,
                            false, false
                        );
                      @endphp
                      <a href="{{ $pUrl }}" target="_blank" rel="noopener"
                         class="link link-hover text-primary">{{ $itemInfo->pName }}</a>,
                      <a href="{{ $sUrl }}" target="_blank" rel="noopener"
                         class="link link-hover text-sm">{{ $itemInfo->sSku }}</a>
                    @else
                      {{ $itemInfo->pName }}, {{ $itemInfo->sSku }}
                      <br /><em class="text-warning text-sm">&mdash; Item is no longer available</em>
                    @endif
                  </td>
                  <td>
                    <label for="tiPrice-{{ $itemInfo->sId }}" class="sr-only">{{ Lang::txt('COM_CART_PRICE') }}</label>
                    <input type="text"
                           name="tiPrice[{{ $itemInfo->sId }}]"
                           id="tiPrice-{{ $itemInfo->sId }}"
                           class="input input-bordered input-sm w-24"
                           value="{{ $ti->tiPrice }}" />
                  </td>
                  <td>
                    <label for="tiQty-{{ $itemInfo->sId }}" class="sr-only">{{ Lang::txt('COM_CART_QUANTITY') }}</label>
                    <input type="text"
                           name="tiQty[{{ $itemInfo->sId }}]"
                           id="tiQty-{{ $itemInfo->sId }}"
                           class="input input-bordered input-sm w-16"
                           value="{{ $ti->qty }}" />
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </x-admin-fieldset>

      @php
        $notes = [];
        foreach ($items as $sId => $item) {
            $meta = $item['transactionInfo']->tiMeta;
            if (!empty($meta->checkoutNotes)) {
                $notes[] = [
                    'object'   => 'transactionItem',
                    'objectId' => $item['info']->sId,
                    'label'    => ($tInfo->tiItems[$sId]['info']->pName ?? '')
                        . ', ' . ($tInfo->tiItems[$sId]['info']->sSku ?? ''),
                    'notes'    => $meta->checkoutNotes,
                ];
            }
        }
        $genericLabel = !empty($notes) ? 'Other notes/comments' : '';
        if ($tInfo->tiNotes) {
            $notes[] = [
                'object'   => 'transaction',
                'objectId' => $tInfo->tId,
                'label'    => $genericLabel,
                'notes'    => $tInfo->tiNotes,
            ];
        }
      @endphp

      @if(!empty($notes))
        <x-admin-fieldset legend="Notes/Comments">
          @foreach($notes as $nIdx => $note)
            <div class="mb-4">
              @if($note['label'])
                <label for="note-{{ $nIdx }}" class="label text-base-content">
                  <span class="label-text text-base-content font-semibold">{{ $note['label'] }}</span>
                </label>
              @endif
              @if($note['object'] === 'transactionItem')
                <label for="note-{{ $nIdx }}" class="sr-only">{{ $note['label'] ?: 'Checkout notes' }}</label>
                <textarea name="checkoutNotes[{{ $note['objectId'] }}]"
                          id="note-{{ $nIdx }}"
                          rows="4"
                          class="textarea textarea-bordered w-full">{{ $note['notes'] }}</textarea>
              @elseif($note['object'] === 'transaction')
                <label for="note-{{ $nIdx }}" class="sr-only">{{ $note['label'] ?: 'Order notes' }}</label>
                <textarea name="tiNotes"
                          id="note-{{ $nIdx }}"
                          rows="4"
                          class="textarea textarea-bordered w-full">{{ $note['notes'] }}</textarea>
              @endif
            </div>
          @endforeach
        </x-admin-fieldset>
      @endif
    </div>
  </div>

  <input type="hidden" name="id" value="{{ $tId }}" />
  <input type="hidden" name="task" value="edit" />
  <input type="hidden" name="from" value="edit" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  {!! Html::input('token') !!}
</form>
