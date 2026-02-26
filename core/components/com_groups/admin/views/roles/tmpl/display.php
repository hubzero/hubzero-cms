<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_ROLES'), 'groups');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_GROUPS_DELETE_CONFIRM', 'delete');
}
Toolbar::spacer();
Toolbar::help('groups');

Html::behavior('tooltip');
?>

<?php $url = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $url; ?>" method="post" name="adminForm" id="adminForm">
<?php if ($tmpl == 'component') { ?>
    <fieldset>
        <div class="configuration">
            <div class="fltrt configuration-options">
                <button type="button" onclick="submitbutton('add');"><?php echo Lang::txt('JACTION_CREATE');?></button>
                <?php $txt = Lang::txt('JACTION_DELETE'); ?>
                <button type="button" onclick="submitbutton('remove');"><?php echo $txt;?></button>
            </div>
            <?php echo Lang::txt('COM_GROUPS_ROLES'); ?>
        </div>
    </fieldset>
    <fieldset id="filter-bar" class="filter clearfix">
    </fieldset>
<?php } ?>
    <table class="adminlist">
        <thead>
            <tr>
                <th colspan="3">
                    <?php $val1 = $this->escape(stripslashes($this->group->get('cn'))); ?>
                    <?php $val = $this->escape(stripslashes($this->group->get('description'))); ?>
                    <?php if ($tmpl != 'component') {
                        ?><a href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php
                    } ?><?php echo Lang::txt('COM_GROUPS'); ?><?php if ($tmpl != 'component') {
    ?></a><?php
                    } ?> > (<?php echo $val1; ?>) <?php echo $val; ?>
                </th>
            </tr>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all" />
                    <?php $txt = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <label for="checkall-toggle" class="sr-only visually-hidden"><?php echo $txt; ?></label>
                </th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_FIELD_ID',
                    'id',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col" class="priority-4"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_NAME',
                    'name',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col"><?php echo $val; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="3"><?php
                echo $this->rows->pagination;
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
                    <input
                        type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
                    <?php $v1 = $i; ?>
                    <?php $v0 = $row->get('id'); ?>
                    <label for="cb<?php echo $v1; ?>" class="sr-only visually-hidden"><?php echo $v0; ?></label>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape($row->get('id')); ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) : ?>
                        <?php $val = Route::url(
                            'index.php?option=' .
                                $this->option .
                                '&controller=' .
                                $this->controller .
                                '&task=edit&id=' .
                                $row->get('id') .
                                '&gid=' .
                                $this->filters['gid'] .
                                ($tmpl ? '&tmpl=' . $tmpl : '')
                        ); ?>
                        <a href="<?php echo $val; ?>">
                            <?php echo $this->escape(stripslashes($row->get('name'))); ?>
                        </a>
                    <?php else : ?>
                        <span>
                            <?php echo $this->escape(stripslashes($row->get('name'))); ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="gid" value="<?php echo $this->filters['gid']; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>