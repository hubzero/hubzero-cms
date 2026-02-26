<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$task = strtolower(Request::getCmd('task', ''));

$allUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$allLabel = Lang::txt('COM_CART_DOWNLOADS_REPORT_ALL');

$skuUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=sku'
);
$skuLabel = Lang::txt('COM_CART_DOWNLOADS_REPORT_SKU');

$allClass = (!$task || $task == 'display') ? ' class="active"' : '';
$skuClass = ($task == 'sku') ? ' class="active"' : '';
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li><a<?php echo $allClass; ?>
            href="<?php echo $allUrl; ?>"><?php echo $allLabel; ?></a></li>
        <li><a<?php echo $skuClass; ?>
            href="<?php echo $skuUrl; ?>"><?php echo $skuLabel; ?></a></li>
    </ul>
</nav><!-- / .sub-navigation -->