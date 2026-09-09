{{--
  Download message — displays notifications/errors for download requests.

  Variables from controller (messageTask):
    $notifications — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_DOWNLOAD')">
  <x-alert-list :notifications="$notifications" />
</x-page-container>
