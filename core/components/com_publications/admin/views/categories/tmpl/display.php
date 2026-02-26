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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Publications\Helpers\Permissions::getActions('category');

$label = Lang::txt('COM_PUBLICATIONS_PUBLICATIONS');
$label2 = Lang::txt('COM_PUBLICATIONS_CATEGORIES');
Toolbar::title(
    $label . ': ' . $label2,
    'category'
);
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::editList();
    Toolbar::publishList('changestatus', Lang::txt('COM_PUBLICATIONS_CHANGE_STATUS'));
}
if ($canDo->get('core.delete')) {
    Toolbar::spacer();
    Toolbar::deleteList();
}

$this->css();
?>
<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&cotnroller=' . $this->controller); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                    />
                                        <label for="checkall-toggle" class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                        </label>
                </th>
                <?php
                $sDir = @$this->filters['sort_Dir'];
                $sCol = @$this->filters['sort'];
                $idSort = Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ID'), 'id', $sDir, $sCol);
                $nameSort = Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_NAME'), 'name', $sDir, $sCol);
                $contSort = Html::grid(
                    'sort',
                    Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'),
                    'contributable',
                    $sDir,
                    $sCol
                );
                $stateSort = Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'), 'state', $sDir, $sCol);
                ?>
                <th class="priority-4"><?php echo $idSort; ?></th>
                <th><?php echo $nameSort; ?></th>
                <th class="priority-3"><?php echo $contSort; ?></th>
                <th class="priority-2"><?php echo $stateSort; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->rows->pagination; ?>
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
                    <input
                        type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $row->id; ?>"
                        class="checkbox-toggle"
                    />
                    <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->id; ?></label>
                </td>
                <td class="priority-4">
                    <?php echo $row->id; ?>
                </td>
                <td>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=edit&id=' . $row->id
                    );
                    ?>
                    <a href="<?php echo $editUrl; ?>">
                        <span><?php echo $this->escape($row->name); ?></span>
                    </a>
                    <span class="block">
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') . ': ' . $this->escape($row->alias); ?> |
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS'); ?>
                        <?php echo $langTxt . ': ' . $this->escape($row->url_alias); ?> |
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE') . ': ' . $this->escape($row->dc_type); ?>
                    </span>
                </td>
                <td class="priority-3 centeralign">
                    <span class="state <?php echo ($row->contributable == 1) ? 'yes' : 'no'; ?>">
                        <span><?php echo ($row->contributable == 1) ? Lang::txt('JYES') : Lang::txt('JNO'); ?></span>
                    </span>
                </td>
                <td class="priority-2 centeralign">
                    <span class="state <?php echo ($row->state == 1) ? 'on' : 'off'; ?>">
                        <span><
                            ?php echo ($row->state == 1) ? Lang:
                            :txt('COM_PUBLICATIONS_ON') : Lang::txt('COM_PUBLICATIONS_OFF'); ?></span>
                    </span>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>