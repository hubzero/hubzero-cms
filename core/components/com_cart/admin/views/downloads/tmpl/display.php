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
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Sku;

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('download');

Toolbar::title(Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'), 'cart');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
}

Toolbar::custom('download', 'download.png', '', 'Download CSV', false);

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
                <?php
                if (!empty($this->filters['skuRequested'])) {
                    ?>
                    <select name="skuRequested" id="skuRequested" class="filter filter-submit">
                        <option value="0"<?php if ($this->filters['skuRequested'] == -1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('All SKUs'); ?></option>
                        <option value="<?php echo $this->filters['skuRequested']; ?>"><?php
                            echo $this->skuRequestedName;
                        ?></option>
                    </select>
                    &nbsp;&nbsp;
                    <?php
                }
                ?>
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
            <?php if ($this->filters['uidNumber'] || $this->filters['pId'] || $this->filters['sId']) { ?>
                <tr>
                    <th colspan="8"><?php
                    if ($this->filters['uidNumber']) {
                        echo Lang::txt('COM_CART_ORDERS_FOR') . ': ';
                        $user = User::getInstance($this->filters['uidNumber']);

                        echo $user->get('id')
                            ? $user->get('name') . ' (' . $user->get('username') . ')'
                            : Lang::txt('COM_CART_USER_ID') . ': ' . $this->filters['uidNumber'];
                        $clearTxt = Lang::txt('JSEARCH_FILTER_CLEAR');
                        ?>
                            <button type="button" id="filter_uidNumber-clear"><?php echo $clearTxt; ?></button>
                            <?php
                    }
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
                <th scope="col">
                    <input type="checkbox"
                        name="toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle" class="sr-only visually-hidden"><?php
                        echo Lang::txt('JGLOBAL_CHECK_ALL');
                    ?></label>
                </th>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sort = @$this->filters['sort'];
                ?>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_PRODUCT', 'product', $sortDir, $sort); ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_CART_DOWNLOADED_BY', 'dName', $sortDir, $sort);
                ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_USER_INFO'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CART_EULA'); ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_CART_DOWNLOADED', 'dDownloaded', $sortDir, $sort);
                ?></th>
                <th scope="col">IP</th>
                <th scope="col"><?php echo Html::grid('sort', 'COM_CART_STATUS', 'dStatus', $sortDir, $sort); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8">
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
            switch ($row->dStatus) {
                case 1:
                    $class = 'publish';
                    $task = 'inactive';
                    $alt = Lang::txt('COM_CART_ACTIVE');
                    break;
                case 0:
                    $class = 'unpublish';
                    $task = 'active';
                    $alt = Lang::txt('COM_CART_INACTIVE');
                    break;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $row->dId; ?>"
                        class="checkbox-toggle" />
                    <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->dId; ?></label>
                </td>
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
                    if (!stripslashes($row->sSku)) {
                        $product .= '<br />SKU: <span class="missing">n/a</span>';
                    } else {
                        $sUrl = Route::url(
                            'index.php?option=com_storefront&controller=skus&task=edit&id='
                            . $row->sId
                        );
                        $sName = $this->escape(stripslashes($row->sSku));
                        $product .= '<br />SKU: <a href="' . $sUrl . '">' . $sName . '</a>';
                    }
                    ?>

                    <?php
                    $pUrl2 = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&pId=' . $row->pId
                    );
                    $product = '<a href="' . $pUrl2 . '">' . $pName . '</a>';
                    if (!stripslashes($row->pName)) {
                        $product = '<span class="missing">Product n/a</span>';
                    }
                    if (!stripslashes($row->sSku)) {
                        $product .= '<br />SKU: <span class="missing">n/a</span>';
                    } else {
                        $sUrl2 = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&sId=' . $row->sId
                        );
                        $product .= '<br />SKU: <a href="' . $sUrl2 . '">' . $sName . '</a>';
                    }
                    ?>
                    <span><?php echo $product; ?></span>
                </td>
                <td>
                    <?php if ($row->uidNumber) { ?>
                        <?php
                        $uidUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&uidNumber=' . $row->uidNumber
                        );
                        ?>
                    <a href="<?php echo $uidUrl; ?>">
                    <?php } ?>
                        <?php
                        $dName = $row->uidNumber
                            ? $this->escape(stripslashes($row->dName))
                                . ' (' . $this->escape(stripslashes($row->username)) . ')'
                            : Lang::txt('COM_CART_UNKNOWN');
                        ?>
                        <span><?php echo $dName; ?></span>
                        <?php if ($row->uidNumber) { ?>
                    </a>
                        <?php } ?>
                </td>
                <td>
                    <?php
                    if ($row->meta) {
                        if (array_key_exists('userInfo', $row->meta) && $row->meta['userInfo']) {
                            $meta = unserialize($row->meta['userInfo']['mtValue']);
                            $data = array();
                            foreach ($meta as $mtK => $mtV) {
                                if (is_array($mtV)) {
                                    $mtV = implode('; ', $mtV);
                                }
                                $data[] = $mtV;
                            }
                            echo implode(', ', $data);
                        }
                    } else {
                        echo '&nbsp;';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($row->meta) {
                        if (array_key_exists('eulaAccepted', $row->meta) && $row->meta['eulaAccepted']) {
                            if ($row->meta['eulaAccepted']['mtValue']) {
                                echo 'EULA accepted';
                            }
                        }
                    } else {
                        echo '&nbsp;';
                    }
                    ?>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->dDownloaded)); ?></span>
                </td>
                <td>
                    <span><?php echo $this->escape(stripslashes($row->dIp)); ?></span>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php
                        $stateUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=' . $task
                            . '&id=' . $row->dId
                        );
                        $stateTitle = Lang::txt('COM_CART_SET_TASK', $task);
                        ?>
                        <a class="state <?php echo $class; ?>"
                            href="<?php echo $stateUrl; ?>"
                            title="<?php echo $stateTitle; ?>">
                            <span><?php echo $alt; ?></span>
                        </a>
                    <?php } else { ?>
                        <span class="state <?php echo $class; ?>">
                        <span><?php echo $alt; ?></span>
                    </span>
                    <?php } ?>
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
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php $uidVal = $this->escape($this->filters['uidNumber']); ?>
    <input type="hidden"
        name="uidNumber"
        id="filter_uidNumber"
        value="<?php echo $uidVal; ?>" />
    <input type="hidden" name="pId" id="filter_pId" value="<?php echo $this->escape($this->filters['pId']); ?>" />
    <input type="hidden" name="sId" id="filter_sId" value="<?php echo $this->escape($this->filters['sId']); ?>" />

    <?php echo Html::input('token'); ?>
</form>