<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Massmail\Admin\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_MASSMAIL'), 'massmail');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_MASSMAIL_CONFIRM_DELETE', 'delete');
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('mailings');

Html::behavior('tooltip');

$statuses = \Components\Massmail\Models\Mailing::statuses();

$this->css('display.css', 'com_massmail');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span6">
                <label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
                <input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MASSMAIL_FILTER_SEARCH'); ?>" />
                <input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
                <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <div class="col span6 align-right">
                <label for="filter-status"><?php echo Lang::txt('COM_MASSMAIL_STATUS'); ?>:</label>
                <select name="status" id="filter-status" class="filter filter-submit">
                    <option value=""><?php echo Lang::txt('COM_MASSMAIL_ALL_STATUSES'); ?></option>
                    <?php foreach ($statuses as $s) { ?>
                        <option value="<?php echo $s; ?>"<?php echo ($this->filters['status'] === $s ? ' selected="selected"' : ''); ?>><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($s)); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
                <th class="priority-5"><?php echo Lang::txt('JGRID_HEADING_ID'); ?></th>
                <th><?php echo Lang::txt('COM_MASSMAIL_COL_SUBJECT'); ?></th>
                <th><?php echo Lang::txt('COM_MASSMAIL_COL_STATUS'); ?></th>
                <th><?php echo Lang::txt('COM_MASSMAIL_COL_PROGRESS'); ?></th>
                <th class="priority-4"><?php echo Lang::txt('COM_MASSMAIL_COL_CREATED_BY'); ?></th>
                <th class="priority-3"><?php echo Lang::txt('COM_MASSMAIL_COL_CREATED'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="mm-list-foot" colspan="7"><?php echo $this->rows->pagination; ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php if (!$this->rows->count()) { ?>
            <tr><td colspan="7"><?php echo Lang::txt('COM_MASSMAIL_NO_MAILINGS'); ?></td></tr>
        <?php } else {
            $i = 0; foreach ($this->rows as $row) {
                        // Every row opens the same view/edit page (the editor): it shows the
                        // message, stays editable until the mailing starts sending, and its
                        // status side-panel exposes the admin approve/release/etc. controls
                        // appropriate to the current status.
                $link = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $row->get('id'));
                ?>
            <tr>
                <td><?php echo Html::grid('id', $i++, $row->get('id')); ?></td>
                <td class="priority-5"><a href="<?php echo $link; ?>"><?php echo $row->get('id'); ?></a></td>
                <td>
                    <a href="<?php echo $link; ?>"><?php echo $this->escape(stripslashes($row->get('subject'))); ?></a>
                    <?php if ((int) $row->get('ignore_optout')) {
                        ?><span class="badge badge-warning" title="<?php echo Lang::txt('COM_MASSMAIL_IGNORES_OPTOUT'); ?>">!</span><?php
                    } ?>
                </td>
                <td><a class="mm-status-link" href="<?php echo $link; ?>"><span class="mm-status mm-status-<?php echo $row->get('status'); ?>"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($row->get('status'))); ?></span></a></td>
                <td>
                    <?php
                    $total = (int) $row->get('total_recipients');
                    $sent  = (int) $row->get('sent_count');
                    $failed = (int) $row->get('failed_count');
                    echo $sent . ' / ' . $total;
                    if ($failed) {
                        echo ' <span class="mm-failed">(' . $failed . ' ' . Lang::txt('COM_MASSMAIL_FAILED') . ')</span>';
                    }
                    ?>
                </td>
                <td class="priority-4"><?php echo $this->escape($row->creator->get('name')); ?></td>
                <td class="priority-3"><?php echo $row->get('created') ? Date::of($row->get('created'))->toLocal('M j, Y') : '&mdash;'; ?></td>
            </tr>
            <?php }
        } ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>
