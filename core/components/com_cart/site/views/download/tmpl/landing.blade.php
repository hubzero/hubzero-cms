{{--
  Download landing — auto-download with direct link fallback.

  Variables from controller (landingTask):
    $tId — transaction ID
    $sId — SKU ID

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Document;

  $link = Route::url('index.php?option=com_cart', true, 0)
      . 'download/' . $tId . '/' . $sId . '/direct';

  Document::addScript('/core/components/com_cart/site/assets/js/download.js');
@endphp

<x-page-container :title="Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_DOWNLOAD')">
  <p class="mb-2">{{ Lang::txt('COM_CART_DOWNLOAD_LANDING') }}</p>
  <p>{!! Lang::txt('COM_CART_DOWNLOAD_DIRECT_LINK', $link) !!}</p>
</x-page-container>
