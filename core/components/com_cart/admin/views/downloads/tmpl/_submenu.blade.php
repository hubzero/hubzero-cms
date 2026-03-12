{{--
  Downloads sub-navigation partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $task = strtolower(Request::getCmd('task', ''));

  $allUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller,
      false, false
  );
  $skuUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller . '&task=sku',
      false, false
  );
@endphp

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a @class(['active' => !$task || $task == 'display'])
         href="{{ $allUrl }}">{{ Lang::txt('COM_CART_DOWNLOADS_REPORT_ALL') }}</a>
    </li>
    <li>
      <a @class(['active' => $task == 'sku'])
         href="{{ $skuUrl }}">{{ Lang::txt('COM_CART_DOWNLOADS_REPORT_SKU') }}</a>
    </li>
  </ul>
</nav>
