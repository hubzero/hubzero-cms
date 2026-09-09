{{--
  Order complete — thank you page after successful purchase.

  Variables from controller:
    $transactionInfo — transaction info object with tiItems, tiMeta (serialized)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<x-page-container :title="Lang::txt('COM_CART_THANK_YOU')">
  <p class="text-lg mb-2">{{ Lang::txt('COM_CART_THANK_YOU_TEXT') }}</p>
  <p class="text-base-content/60 mb-6">{{ Lang::txt('COM_CART_CONFIRM_EMAIL') }}</p>

  @if(!empty($transactionInfo))
    @php
      $transactionItems = unserialize($transactionInfo->tiItems);
      $meta = unserialize($transactionInfo->tiMeta);
      $membershipInfo = $meta['membershipInfo'] ?? [];
      $warehouse = new \Components\Storefront\Models\Warehouse();
    @endphp

    <h2 class="text-lg font-semibold mb-3">{{ Lang::txt('COM_CART_ORDER_SUMMARY') }}</h2>

    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>{{ Lang::txt('COM_CART_ITEM') }}</th>
            <th>{{ Lang::txt('COM_CART_STATUS') }}</th>
            <th>{{ Lang::txt('COM_CART_NOTES') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactionItems as $sId => $item)
            @php
              $info = $item['info'];
              $action = '';
              $status = '';
              $productType = $warehouse->getProductTypeInfo($info->ptId)['ptName'];

              if ($productType == 'Course') {
                  $status = Lang::txt('COM_CART_STATUS_REGISTERED');
                  $courseUrl = Route::url(
                      'index.php?option=com_courses/' . $item['meta']['courseId'],
                      false
                  );
                  $action = '<a class="link link-primary" href="' . $courseUrl . '">'
                      . Lang::txt('COM_CART_GO_TO_COURSE') . '</a>';
              } elseif ($productType == 'Software Download') {
                  $status = Lang::txt('COM_CART_STATUS_READY');
                  $downloadUrl = Route::url('index.php?option=com_cart', false)
                      . 'download/' . $transactionInfo->tId . '/' . $info->sId;
                  $action = '<a class="link link-primary" href="' . $downloadUrl
                      . '" target="_blank" rel="noopener">'
                      . Lang::txt('COM_CART_DOWNLOAD') . '</a>';

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
                  $status = Lang::txt('COM_CART_STATUS_PURCHASED');
                  if (!empty($item['meta']['purchaseNote'])) {
                      $action = $item['meta']['purchaseNote'];
                  }
              }
            @endphp
            <tr>
              <td>
                {{ $info->pName }}
                @if(!empty($item['options']))
                  @foreach($item['options'] as $oName), {{ $oName }}@endforeach
                @endif
              </td>
              <td>
                <span class="badge badge-sm badge-ghost">{{ $status }}</span>
              </td>
              <td>{!! $action !!}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</x-page-container>
