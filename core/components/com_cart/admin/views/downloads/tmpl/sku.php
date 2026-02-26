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

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('download');

Toolbar::title(Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_SOFTWARE_DOWNLOADS') . ' by SKU', 'cart');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}

Toolbar::custom('downloadSku', 'download.png', '', 'Download CSV', false);

//Toolbar::spacer();
//Toolbar::help('downloads');

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
                <label for="filter-report-from"><?php echo Lang::txt('From'); ?>:</label>
                <?php $fromVal = $this->escape($this->filters['report-from']); ?>
                <?php $fromPlc = Lang::txt('From'); ?>
                <input type="text"
                    name="report-from"
                    id="filter-report-from"
                    class="filter"
                    value="<?php echo $fromVal; ?>"
                    placeholder="<?php echo $fromPlc; ?>" />
                &mdash;
                <label for="filter-report-to"><?php echo Lang::txt('To'); ?>:</label>
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
            <tr>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sort = @$this->filters['sort'];
                ?>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_PRODUCT', 'product', $sortDir, $sort); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_SKU'); ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_CART_DOWNLOADED', 'downloaded', $sortDir, $sort);
                ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="3">
                    <?php
                    // Initiate paging
                    echo $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit']
                    );
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php
                    $pUrl = Route::url(
                        'index.php?option=com_storefront&controller=products&task=edit&id='
                        . $row->pId
                    );
                    $pName = $this->escape(stripslashes($row->pName));
                    $product = '<a href="' . $pUrl . '">' . $pName . '</a>';
                    if (!stripslashes($row->pName)) {
                        $product = '<span class="missing">Product n/a</span>';
                    }
                    ?>
                    <span><?php echo $product; ?></span>
                </td>
                <td>
                    <?php
                    if (!stripslashes($row->sSku)) {
                        $sku = '<span class="missing">SKU n/a</span>';
                    } else {
                        $sUrl = Route::url(
                            'index.php?option=com_storefront&controller=skus&task=edit&id='
                            . $row->sId
                        );
                        $sName = $this->escape(stripslashes($row->sSku));
                        $sku = '<a href="' . $sUrl . '">' . $sName . '</a>';
                    }
                    ?>
                    <span><?php echo $sku; ?></span>
                </td>
                <td>
                    <?php
                    $dlUrl = Route::url(
                        'index.php?option=com_cart&controller=downloads&task=display&skuRequested='
                        . $row->sId
                    );
                    $dlCount = $this->escape(stripslashes($row->downloaded));
                    $downloaded = '<a href="' . $dlUrl . '">' . $dlCount . '</a>';
                    ?>
                    <span><?php echo $downloaded; ?></span>
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
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

    <?php echo Html::input('token'); ?>
</form>