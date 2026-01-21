<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Sku;

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');

Toolbar::title(Lang::txt('COM_CART') . ': Items Ordered', 'cart.png');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}

Toolbar::custom('downloadOrders', 'download.png', '', 'Download CSV', false);

Toolbar::spacer();
Toolbar::help('downloads');

$this->js();
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
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span5">
                <label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
                <?php $searchVal = $this->escape($this->filters['search']); ?>
                <?php $searchPlc = Lang::txt('JSEARCH_FILTER'); ?>
                <input type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPlc; ?>" />
            </div>
            <div class="col span7 align-right">
                <label for="filter-report-from">From:</label>
                <?php $fromVal = $this->escape($this->filters['report-from']); ?>
                <?php $fromPlc = Lang::txt('From'); ?>
                <input type="text"
                    name="report-from"
                    id="filter-report-from"
                    class="filter"
                    value="<?php echo $fromVal; ?>"
                    placeholder="<?php echo $fromPlc; ?>" />
                &mdash;
                <label for="filter-report-to">To:</label>
                <?php $toVal = $this->escape($this->filters['report-to']); ?>
                <?php $toPlc = Lang::txt('To'); ?>
                <input type="text"
                    name="report-to"
                    id="filter-report-to"
                    class="filter"
                    value="<?php echo $toVal; ?>"
                    placeholder="<?php echo $toPlc; ?>" />
                <input type="submit" value="<?php echo Lang::txt('Update'); ?>" />
            </div>
        </div>
    </fieldset>
    <table class="adminlist">
        <thead>
            <?php if ($this->filters['order']) { ?>
                <tr>
                    <th colspan="7"><?php echo Lang::txt('COM_CART_ORDER'); ?>: #<?php echo $this->filters['order']; ?>
                        <?php $clearTxt = Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                        <button type="button" class="filter-clear"><?php echo $clearTxt; ?></button>
                    </th>
                </tr>
            <?php } ?>
            <?php if ($this->filters['pId'] || $this->filters['sId']) { ?>
                <tr>
                    <th colspan="7"><?php
                    if ($this->filters['pId']) {
                        echo Lang::txt('COM_CART_ORDERS_OF') . ': ';
                        $product = Product::getInstance($this->filters['pId']);

                        echo $product->getName();
                        ?>
                            <button type="button" id="filter_pId-clear"><?php
                                echo Lang::txt('JSEARCH_FILTER_CLEAR');
                            ?></button>
                            <?php
                    }
                    if ($this->filters['sId']) {
                        echo Lang::txt('COM_CART_ORDERS_OF') . ': ';
                        $sku = Sku::getInstance($this->filters['sId']);

                        echo $sku->getName();
                        ?>
                            <button type="button" id="filter_sId-clear"><?php
                                echo Lang::txt('JSEARCH_FILTER_CLEAR');
                            ?></button>
                            <?php
                    }
                    ?>

                    </th>
                </tr>
            <?php } ?>
            <tr>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sort = @$this->filters['sort'];
                ?>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_SKU_ID', 'sId', $sortDir, $sort); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_PRODUCT'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_QUANTITY'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_PRICE'); ?></th>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', $sortDir, $sort); ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_CART_ORDER_PLACED', 'tLastUpdated', $sortDir, $sort);
                ?></th>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', $sortDir, $sort); ?></th>
            </tr>
        </thead>
        <tfoot>
            tr>
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
                    <span>
                        <?php
                        $skuEditUrl = Route::url(
                            'index.php?option=com_storefront&controller=skus&task=edit&id='
                            . $itemInfo->sId
                        );
                        ?>
                        <a href="<?php echo $skuEditUrl; ?>">
                            <?php echo $this->escape(stripslashes($row->sId)); ?>
                        </a>
                    </span>
                </td>
                <td>
                    <?php
                    $pUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=' . $this->task
                        . '&pId=' . $itemInfo->pId
                    );
                    $pName = $this->escape(stripslashes($itemInfo->pName));
                    $product = '<a href="' . $pUrl . '">' . $pName . '</a>';
                    if (!stripslashes($itemInfo->pName)) {
                        $product = '<span class="missing">Product n/a</span>';
                    }
                    if (!stripslashes($itemInfo->sSku)) {
                        $product .= '<br />SKU: <span class="missing">SKU n/a</span>';
                    } else {
                        $sUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=' . $this->task
                            . '&sId=' . $row->sId
                        );
                        $sName = $this->escape(stripslashes($itemInfo->sSku));
                        $product .= '<br />SKU: <a href="' . $sUrl . '">' . $sName . '</a>';
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
                    <span>
                        <?php
                        $orderUrl = Route::url(
                            'index.php?option=com_cart&controller=orders&task=view&id='
                            . $row->tId
                        );
                        ?>
                        <a href="<?php echo $orderUrl; ?>">
                            <?php echo $this->escape(stripslashes($row->tId)); ?>
                        </a>
                    </span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->tLastUpdated)); ?></span>
                </td>
                <td>
                    <span>
                    <?php if ($row->uidNumber) { ?>
                        <?php
                        $memberUrl = Route::url(
                            'index.php?option=com_members&task=edit&id='
                            . $row->uidNumber
                        );
                        ?>
                        <a href="<?php echo $memberUrl; ?>">
                    <?php } ?>
                            <?php
                            $displayName = $row->name
                                ? $this->escape(stripslashes($row->name))
                                : Lang::txt('COM_CART_UNKNOWN');
                            echo $displayName;
                            ?>
                    <?php if ($row->uidNumber) { ?>
                        </a>
                    <?php } ?>
                    </span>
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
    <input type="hidden" name="order" id="filter_ordernum" value="<?php echo $this->filters['order']; ?>" />
    <input type="hidden" name="pId" id="filter_pId" value="<?php echo $this->filters['pId']; ?>" />
    <input type="hidden" name="sId" id="filter_sId" value="<?php echo $this->filters['sId']; ?>" />

    <?php echo Html::input('token'); ?>
</form>