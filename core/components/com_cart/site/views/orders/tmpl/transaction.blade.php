{{--
  Single order item — displays one transaction in the orders list.

  Variables:
    $transaction — transaction object with tId, tLastUpdated, tInfo

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $tiTotalAmount = $transaction->tInfo->tiSubtotal
      + $transaction->tInfo->tiTax
      + $transaction->tInfo->tiShipping;

  $transactionItems = is_array($transaction->tInfo->tiItems)
      ? $transaction->tInfo->tiItems : [];
  $meta = !empty($transaction->tInfo->tiMeta)
      ? @unserialize($transaction->tInfo->tiMeta) : [];

  $warehouse = new \Components\Storefront\Models\Warehouse();
  $orderDate = date('F j, Y', strtotime($transaction->tLastUpdated));
@endphp

<div class="card bg-base-100 shadow-sm">
  <div class="card-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
      <h3 class="card-title text-base">
        {{ Lang::txt('COM_CART_ORDER_NUMBER', $transaction->tId) }}
      </h3>
      <div class="flex gap-4 text-sm text-base-content/60">
        <span>{{ Lang::txt('COM_CART_ORDER_PLACED') }}: {{ $orderDate }}</span>
        <span>
          {{ Lang::txt('COM_CART_ORDER_TOTAL_LABEL') }}:
          ${{ number_format($tiTotalAmount, 2) }}
        </span>
      </div>
    </div>

    <div class="divide-y divide-base-200">
      @foreach($transactionItems as $sId => $item)
        @php
          $info = is_array($item['info']) ? (object) $item['info'] : $item['info'];
          $action = '';
          $ptInfo = !empty($info->ptId) ? $warehouse->getProductTypeInfo($info->ptId) : null;
          $productType = $ptInfo['ptName'] ?? '';
          $productUrl = Route::url('index.php?option=com_storefront', false)
              . '/product/' . $info->pId;

          if ($productType == 'Course') {
              if ($info->available) {
                  $courseUrl = Route::url(
                      'index.php?option=com_courses/' . $item['meta']['courseId'],
                      false
                  );
                  $action = '<a class="link link-primary" href="' . $courseUrl . '">'
                      . Lang::txt('COM_CART_GO_TO_COURSE') . '</a>';
              } else {
                  $action = Lang::txt('COM_CART_PRODUCT_UNAVAILABLE');
              }
          } elseif ($productType == 'Software Download') {
              if ($info->available) {
                  $downloadUrl = Route::url('index.php?option=com_cart', false)
                      . 'download/' . $transaction->tInfo->tId
                      . '/' . $info->sId . '/direct';
                  $action = '<a class="link link-primary" href="' . $downloadUrl
                      . '" target="_blank" download="download" rel="noopener">'
                      . Lang::txt('COM_CART_DOWNLOAD') . '</a>';
              } else {
                  $action = Lang::txt('COM_CART_PRODUCT_UNAVAILABLE');
              }

              $hasMultipleSerials = isset($item['meta']['serialManagement'])
                  && $item['meta']['serialManagement'] == 'multiple'
                  && !empty($item['meta']['serials']);
              if ($hasMultipleSerials) {
                  $serialLabel = count($item['meta']['serials']) > 1
                      ? Lang::txt('COM_CART_SERIAL_NUMBERS')
                      : Lang::txt('COM_CART_SERIAL_NUMBER');
                  $action .= '<br>' . $serialLabel . ': <strong>'
                      . implode('<br>', $item['meta']['serials']) . '</strong>';
              } elseif (!empty($item['meta']['serial'])) {
                  $action .= '<br>' . Lang::txt('COM_CART_SERIAL_NUMBER')
                      . ': <strong>' . $item['meta']['serial'] . '</strong>';
              }
          } else {
              if (!empty($item['meta']['purchaseNote'])) {
                  $action = $item['meta']['purchaseNote'];
              }
          }
        @endphp
        <div class="flex items-center justify-between gap-4 py-3">
          <div>
            @if($info->available)
              <a href="{{ $productUrl }}" class="link link-hover text-primary">
                {{ $info->pName }}
                @if(!empty($item['options']))
                  @foreach($item['options'] as $oName), {{ $oName }}@endforeach
                @endif
              </a>
            @else
              <span class="text-base-content/60">
                {{ $info->pName }}
                @if(!empty($item['options']))
                  @foreach($item['options'] as $oName), {{ $oName }}@endforeach
                @endif
              </span>
            @endif
          </div>
          @if($action)
            <div class="text-sm shrink-0">{!! $action !!}</div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</div>
