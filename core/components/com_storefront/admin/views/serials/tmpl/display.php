<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': SKU\'s serial numbers', 'storefront');

$newPopupUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&tmpl=component&task=new&sId=' . $this->sku->getId()
);
Toolbar::appendButton('Popup', 'new', 'New', $newPopupUrl, 570, 170);
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}

Toolbar::spacer();
//Toolbar::custom('upload', 'upload.png', '', 'Upload CSV', false);
$uploadPopupUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&tmpl=component&task=upload&sId=' . $this->sku->getId()
);
Toolbar::appendButton(
    'Popup',
    'upload',
    'Upload CSV',
    $uploadPopupUrl,
    570,
    170
);
Toolbar::spacer();
Toolbar::cancel();

?>

<?php
$formAction = Route::url('index.php?option=' . $this->option);
$skuEditUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=skus&task=edit&id=' . $this->sku->getId()
);
$editSkuTitle = Lang::txt('Edit SKU');
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th colspan="5">
                    Serial numbers for: <a
                        href="<?php echo $skuEditUrl; ?>"
                        title="<?php echo $editSkuTitle; ?>"
                    ><?php echo $this->sku->getName(); ?></a>
                </th>
            </tr>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                    />
                    <label
                        for="checkall-toggle"
                        class="sr-only visually-hidden"
                    ><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
                </th>
<?php
$sortDir = @$this->filters['sort_Dir'];
$sort = @$this->filters['sort'];
?>
                <th scope="col"><?php echo Html::grid('sort', 'ID', 'srId', $sortDir, $sort); ?></th>
                <th scope="col"><?php echo Html::grid('sort', 'Serial Number', 'srNumber', $sortDir, $sort); ?></th>
                <th scope="col"><?php echo Html::grid('sort', 'Status', 'srStatus', $sortDir, $sort); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5"><?php
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
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php if ($row->srStatus == 'available') { ?>
                        <input
                            type="checkbox"
                            name="srId[]"
                            id="cb<?php echo $i; ?>"
                            value="<?php echo $row->srId; ?>"
                            class="checkbox-toggle"
                        />
                        <label
                            for="cb<?php echo $i; ?>"
                            class="sr-only visually-hidden"
                        ><?php echo $row->srId; ?></label>
                    <?php } ?>
                </td>
                <td>
                    <span>
                        <?php echo $this->escape(stripslashes($row->srId)); ?>
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo $this->escape(stripslashes($row->srNumber)); ?>
                    </span>
                </td>
                <td>
                    <span>
                        <?php echo $this->escape(stripslashes($row->srStatus)); ?>
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
    <input type="hidden" name="sId" value="<?php echo $this->sId; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>