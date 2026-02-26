<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

defined('_HZEXEC_') or die();

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');

Toolbar::title(Lang::txt('COM_CART') . ': Items Ordered', 'cart.png');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}

Toolbar::custom('downloadorders', 'download.png', '', 'Download CSV', false);

Toolbar::spacer();
Toolbar::help('downloads');
?>

<?php
$this->view('_submenu')
    ->display();
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm">
    <table class="adminlist">
        <thead>
            <?php
            $sortDir = @$this->filters['sort_Dir'];
            $sort = @$this->filters['sort'];
            ?>
            <tr>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_SKU_ID', 'sId', $sortDir, $sort); ?></th>
                <th scope="col">Product</th>
                <th scope="col">QTY</th>
                <th scope="col">Price</th>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', $sortDir, $sort); ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_CART_ORDER_PALCED', 'tLastUpdated', $sortDir, $sort);
                ?></th>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', $sortDir, $sort); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7"><?php
                    // Initiate paging
                    echo $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit']
                    );
                    ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;

        foreach ($this->rows as $row) {
            $itemInfo = $row->itemInfo['info'];
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php
                    $skuUrl = Route::url(
                        'index.php?option=com_storefront&controller=skus&task=edit&id='
                        . $itemInfo->sId
                    );
                    $sId = '<a href="' . $skuUrl . '">'
                        . $this->escape(stripslashes($row->sId)) . '</a>';
                    ?>
                    <span><?php echo $sId; ?></span>
                </td>
                <td>
                    <?php
                    $pUrl = Route::url(
                        'index.php?option=com_storefront&controller=products&task=edit&id='
                        . $itemInfo->pId
                    );
                    $pName = $this->escape(stripslashes($itemInfo->pName));
                    $product = '<a href="' . $pUrl . '">' . $pName . '</a>';
                    if (!stripslashes($itemInfo->pName)) {
                        $product = '<span class="missing">Product n/a</span>';
                    }
                    if (!stripslashes($itemInfo->sSku)) {
                        $product .= ', <span class="missing">SKU n/a</span>';
                    } else {
                        $sUrl = Route::url(
                            'index.php?option=com_storefront&controller=skus&task=edit&id='
                            . $row->sId
                        );
                        $sName = $this->escape(stripslashes($itemInfo->sSku));
                        $product .= ', <a href="' . $sUrl . '">' . $sName . '</a>';
                    }
                    ?>
                    <span><?php echo $product; ?></span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->tiQty)); ?></span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->tiPrice)); ?></span>
                </td>
                <td>
                    <?php
                    $orderUrl = Route::url(
                        'index.php?option=com_cart&controller=orders&task=view&id='
                        . $row->tId
                    );
                    $tId = '<a href="' . $orderUrl . '">'
                        . $this->escape(stripslashes($row->tId)) . '</a>';
                    ?>
                    <span><?php echo $tId; ?></span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->tLastUpdated)); ?></span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->name)); ?></span>
                </td>
            </tr>
            <?php
            $i++;
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

    <?php echo Html::input('token'); ?>
</form>