{{--
  Order View — Admin detail (read-only)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User as UserFacade;

  Toolbar::title(Lang::txt('COM_CART') . ': View order');
  Toolbar::cancel();

  if (UserFacade::authorise('core.edit', $option . '.component')) {
      Toolbar::custom('edit', 'edit.png', '', 'Edit', false);
  }

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
    {{-- Left column: Order & Shipping & Payment --}}
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
          <p><strong>Payment method:</strong> {{ $tInfo->tiPayment }}</p>
          @if(!empty($tInfo->tiPaymentDetails))
            <p><strong>Payment details:</strong> {{ $tInfo->tiPaymentDetails }}</p>
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
                <th class="w-24">Price</th>
                <th class="w-16">QTY</th>
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
                      {{ $itemInfo->pName ?? 'N/A' }}, {{ $itemInfo->sSku ?? 'N/A' }}
                      <br /><em class="text-warning text-sm">&mdash; Item is no longer available</em>
                    @endif
                  </td>
                  <td>{{ isset($ti->tiPrice) ? '$' . number_format($ti->tiPrice, 2) : 'N/A' }}</td>
                  <td>{{ $ti->qty }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </x-admin-fieldset>

      @php
        // Collect notes from SKU-specific checkout notes and general order notes
        $notes = [];
        foreach ($items as $sId => $item) {
            $meta = $item['transactionInfo']->tiMeta;
            if (!empty($meta->checkoutNotes)) {
                $label = ($tInfo->tiItems[$sId]['info']->pName ?? '')
                    . ', ' . ($tInfo->tiItems[$sId]['info']->sSku ?? '');
                $notes[] = ['label' => $label, 'notes' => $meta->checkoutNotes];
            }
        }
        $genericLabel = !empty($notes) ? 'Other notes/comments' : '';
        if ($tInfo->tiNotes) {
            $notes[] = ['label' => $genericLabel, 'notes' => $tInfo->tiNotes];
        }
      @endphp

      @if(!empty($notes))
        <x-admin-fieldset legend="Notes/Comments">
          @foreach($notes as $note)
            <div class="mb-2">
              @if($note['label'])
                <strong>{{ $note['label'] }}:</strong>
              @endif
              <p>{{ $note['notes'] }}</p>
            </div>
          @endforeach
        </x-admin-fieldset>
      @endif
    </div>
  </div>

  {{-- Changelog --}}
  @if(isset($log) && !empty($log))
    <div class="mt-8">
      <x-admin-fieldset legend="Changelog">
        <div class="space-y-4">
          @foreach($log as $entry)
            <article class="bg-base-200 rounded-lg overflow-hidden">
              <header class="bg-base-300 px-4 py-2 text-sm font-semibold">
                {{ $entry->description }}
                on <time datetime="{{ $entry->created }}">{{ date('F j, Y, g:i a', strtotime($entry->created)) }}</time>
                by {{ $entry->user }} [{{ $entry->created_by }}]
              </header>
              <div class="p-4 space-y-3">
                @foreach($entry->details as $change)
                  <div class="border-b border-base-300 pb-3 last:border-0 last:pb-0">
                    <p class="font-medium text-sm">{{ $change->message }}</p>
                    <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                      <div>
                        <p class="text-xs text-muted-foreground">New value:</p>
                        <div class="bg-success/10 rounded px-2 py-1 mt-1">{{ $change->new }}</div>
                      </div>
                      <div>
                        <p class="text-xs text-muted-foreground">Old value:</p>
                        <div class="bg-base-200 rounded px-2 py-1 mt-1">{{ $change->old }}</div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </article>
          @endforeach
        </div>
      </x-admin-fieldset>
    </div>
  @endif

  <input type="hidden" name="id" value="{{ $tId }}" />
  <input type="hidden" name="task" value="view" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  {!! Html::input('token') !!}
</form>
