<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('tooltip');

$canDo = \Components\Poll\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_POLL'), 'poll.png');
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_poll', '550');
    Toolbar::spacer();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_POLL_CONFIRM_DELETE');
    Toolbar::spacer();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('polls');

$formAction = Route::url('index.php?option=' . $this->option);
$searchVal = $this->escape($this->filters['search']);
$searchPlaceholder = Lang::txt('COM_POLL_SEARCH_PLACEHOLDER');
$orderDir = @$this->filters['order_Dir'];
$order = @$this->filters['order'];
$canEditState = $canDo->get('core.edit.state');
$canEdit = $canDo->get('core.edit');
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span6">
                <label for="filter_search">
                    <?php echo Lang::txt('JSEARCH_FILTER'); ?>:
                </label>
                <input
                    type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPlaceholder; ?>"
                />

                <input
                    type="submit"
                    value="<?php echo Lang::txt('COM_POLL_GO'); ?>"
                />
                <button class="filter filter-submit">
                    <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>
            <div class="col span6">
                <?php echo $this->filters['states']; ?>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" />
                </th>
                <th scope="col">
                    <?php echo Lang::txt('COM_POLL_COL_NUM'); ?>
                </th>
                <th scope="col" class="title">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_TITLE', 'title', $orderDir, $order); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_PUBLISHED', 'state', $orderDir, $order); ?>
                </th>
                <th scope="col" class="priority-2">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_OPEN', 'open', $orderDir, $order); ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_VOTES', 'voters', $orderDir, $order); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Lang::txt('COM_POLL_COL_OPTIONS'); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_LAG', 'lag', $orderDir, $order); ?>
                </th>
                <th scope="col" class="priority-5">
                    <?php echo Html::grid('sort', 'COM_POLL_COL_ID', 'id', $orderDir, $order); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="9">
                    <?php
                    echo $this->rows->pagination;
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            $task  = $row->get('state') ? 'unpublish' : 'publish';
            $class = $row->get('state') ? 'published' : 'unpublished';
            $alt   = $row->get('state')
                ? Lang::txt('JPUBLISHED')
                : Lang::txt('JUNPUBLISHED');

            $task2  = ($row->get('open') == 1) ? 'close' : 'open';
            $class2 = ($row->get('open') == 1) ? 'published' : 'unpublished';
            $alt2   = ($row->get('open') == 1)
                ? Lang::txt('COM_POLL_OPEN')
                : Lang::txt('COM_POLL_CLOSED');

            $checkedOut = $row->get('checked_out')
                && $row->get('checked_out') != User::get('id');
            $noEdit = $checkedOut || !$canEdit;

            $editUrl = Route::url(
                'index.php?option=' . $this->option
                . '&view=poll&task=edit&id=' . $row->get('id')
            );

            $token = Session::getFormToken();
            $stateUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=' . $task
                . '&id=' . $row->get('id')
                . '&' . $token . '=1'
            );
            $openUrl = Route::url(
                'index.php?option=' . $this->option
                . '&task=' . $task2
                . '&id=' . $row->get('id')
                . '&' . $token . '=1'
            );
            $stateTitle = Lang::txt('COM_POLL_SET_TO', $task);
            $openTitle = Lang::txt('COM_POLL_SET_TO', $task2);
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php if ($noEdit) { ?>
                        <span> </span>
                    <?php } else { ?>
                        <input
                            type="checkbox"
                            name="id[]"
                            id="cb<?php echo $i; ?>"
                            value="<?php echo $row->get('id'); ?>"
                            class="checkbox-toggle"
                        />
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row->id; ?>
                </td>
                <td>
                    <?php if ($noEdit) {
                        echo $row->get('title');
                    } else { ?>
                        <span
                            class="editlinktip hasTip"
                            title="<?php echo $this->escape($row->get('title')); ?>"
                        >
                            <a href="<?php echo $editUrl; ?>">
                                <?php echo $this->escape($row->get('title')); ?>
                            </a>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canEditState) { ?>
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
                <td class="priority-2">
                    <?php if ($canEditState) { ?>
                        <a
                            class="state <?php echo $class2; ?>"
                            href="<?php echo $openUrl; ?>"
                            title="<?php echo $openTitle; ?>"
                        >
                            <span><?php echo $alt2; ?></span>
                        </a>
                    <?php } else { ?>
                        <span class="state <?php echo $class2; ?>">
                            <span><?php echo $alt2; ?></span>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-3">
                    <?php echo $row->dates->count(); ?>
                </td>
                <td class="priority-4">
                    <?php echo $row->options->count(); ?>
                </td>
                <td class="priority-4">
                    <?php echo $row->get('lag'); ?>
                </td>
                <td class="priority-5">
                    <?php echo $row->get('id'); ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input
        type="hidden"
        name="filter_order"
        value="<?php echo $this->escape($this->filters['order']); ?>"
    />
    <input
        type="hidden"
        name="filter_order_Dir"
        value="<?php echo $this->escape($this->filters['order_Dir']); ?>"
    />

    <?php echo Html::input('token'); ?>
</form>
