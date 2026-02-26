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

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('category');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Option Groups', 'storefront');
if (0  && $canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}
//Toolbar::spacer();
//Toolbar::help('categories');
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
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
                <th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort); ?></th>
                <th scope="col">Options (published)</th>
<?php $stateSort = Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', $sortDir, $sort); ?>
                <th scope="col"><?php echo $stateSort; ?></th>
            </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="4"><?php
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
//for ($i=0, $n=count($this->rows); $i < $n; $i++)
$i = 0;

foreach ($this->rows as $row) {
    switch ($row->ogActive) {
        case 1:
            $class = 'publish';
            $task = 'unpublish';
            $alt = Lang::txt('COM_STOREFRONT_PUBLISHED');
            break;
        case 2:
            $class = 'expire';
            $task = 'publish';
            $alt = Lang::txt('COM_STOREFRONT_TRASHED');
            break;
        case 0:
            $class = 'unpublish';
            $task = 'publish';
            $alt = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
            break;
    }

    ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input
                        type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $row->ogId; ?>"
                        class="checkbox-toggle"
                    />
                    <label
                        for="cb<?php echo $i; ?>"
                        class="sr-only visually-hidden"
                    ><?php echo $row->ogId; ?></label>
                </td>
                <td>
                <?php if ($canDo->get('core.edit')) { ?>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=edit&ogId=' . $row->ogId
                    );
                    $editTitle = Lang::txt('COM_STOREFRONT_EDIT_CATEGORY');
                    ?>
                    <a href="<?php echo $editUrl; ?>" title="<?php echo $editTitle; ?>">
                        <span><?php echo $this->escape(stripslashes($row->ogName)); ?></span>
                    </a>
                <?php } else { ?>
                    <span>
                        <span><?php echo $this->escape(stripslashes($row->ogName)); ?></span>
                    </span>
                <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php
                        $optionsUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=options&task=display&ogId=' . $row->ogId
                        );
                        ?>
                        <a href="<?php echo $optionsUrl; ?>" title="View Options">
                            <span><?php
                            $key = $row->ogId;
                            $countInfo = $this->options->$key;
                            echo $countInfo->active + $countInfo->inactive;
                            if ($countInfo->active + $countInfo->inactive > 0) {
                                echo ' (' . $countInfo->active . ')';
                            }
                            ?></span>
                        </a>
                        &nbsp;
                        <?php
                        $addOptionUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=options&task=add&ogId=' . $row->ogId
                        );
                        ?>
                        <a class="state add" href="<?php echo $addOptionUrl; ?>">
                            <span>[ + ]</span>
                        </a>
                    <?php } else { ?>
                        <span><?php $key = $row->ogId;
                        echo $this->options->$key; ?></span>
                    </span>
                    <?php } ?>
                </td>
                <td>
                <?php if ($canDo->get('core.edit.state')) { ?>
                    <?php
                    $stateUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=' . $task . '&id=' . $row->ogId
                    );
                    $stateTitle = Lang::txt('COM_STOREFRONT_SET_TASK', $task);
                    ?>
                    <a
                        class="state <?php echo $class; ?>"
                        href="<?php echo $stateUrl; ?>"
                        title="<?php echo $stateTitle; ?>"
                    >
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

    <?php echo Html::input('token'); ?>
</form>