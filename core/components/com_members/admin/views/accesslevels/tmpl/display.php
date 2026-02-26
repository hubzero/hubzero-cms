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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_ACCESSLEVELS'), 'user');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
    Toolbar::divider();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
    Toolbar::divider();
}
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_members');
    Toolbar::divider();
}
Toolbar::help('levels');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', $this->option);
$saveOrder = $listOrder == 'a.ordering';

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php $cls = ($this->controller == 'accessgroups') ? ' class="active"' : ''; ?>
            <?php $url = Route::url('index.php?option=' . $this->option . '&controller=accessgroups'); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_ACCESSGROUPS');
            ?></a>
        </li>
        <li>
            <?php $cls = ($this->controller == 'accesslevels') ? ' class="active"' : ''; ?>
            <?php $url = Route::url('index.php?option=' . $this->option . '&controller=accesslevels'); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_ACCESSLEVELS');
            ?></a>
        </li>
    </ul>
</nav>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search">
            <label
                class="filter-search-lbl"
                for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_ACCESS_LEVELS'); ?></label>
            <input
                type="text"
                name="filter_search"
                id="filter_search"
                class="filter"
                value="<?php echo $this->escape($this->filters['search']); ?>"
                placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_TITLE_LEVELS'); ?>"/>
            <button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        value=""
                        title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>"
                        class="checkbox-toggle toggle-all"/>
                </th>
                <th class="priority-3">
                    <?php echo Lang::txt('JGRID_HEADING_ID'); ?>
                </th>
                <th class="left">
                    <?php echo Html::grid(
                        'sort',
                        'COM_MEMBERS_HEADING_LEVEL_NAME',
                        'title',
                        $this->filters['sort_Dir'],
                        $this->filters['sort']
                    ); ?>
                </th>
                <th>
                    <?php echo Html::grid(
                        'sort',
                        'JGRID_HEADING_ORDERING',
                        'ordering',
                        $this->filters['sort_Dir'],
                        $this->filters['sort']
                    ); ?>
                    <?php if ($canOrder && $saveOrder) :?>
                        <?php echo Html::grid('order', $this->rows); ?>
                    <?php endif; ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="4">
                    <?php echo $this->rows->pagination; ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $i = 0;
        $n = $this->rows->count();
        foreach ($this->rows as $row) :
            $ordering  = ($listOrder == 'ordering');
            $canCreate = User::authorise('core.create', $this->option);
            $canEdit   = User::authorise('core.edit', $this->option);
            $canChange = User::authorise('core.edit.state', $this->option);
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo Html::grid('id', $i, $row->get('id')); ?>
                </td>
                <td class="center priority-3">
                    <?php echo (int) $row->get('id'); ?>
                </td>
                <td>
                    <?php if ($canEdit) : ?>
                        <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' .
                            $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
                            <?php echo $this->escape($row->get('title')); ?>
                        </a>
                    <?php else : ?>
                        <?php echo $this->escape($row->get('title')); ?>
                    <?php endif; ?>
                </td>
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                            <?php if ($listDirn == 'asc') : ?>
                                <?php
                                $orderUpIcon = ($i > 0)
                                    ? Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb')
                                    : '&#160;';
                                ?>
                                <span><?php echo $orderUpIcon; ?></span>
                                <span><?php
                                    echo ($i < ($n - 1))
                                        ? Html::grid(
                                            'orderDown',
                                            $i,
                                            'orderdown',
                                            '',
                                            'JLIB_HTML_MOVE_DOWN',
                                            true,
                                            'cb'
                                        )
                                        : '&#160;';
                                        ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <?php
                                $orderUpIcon = ($i > 0)
                                    ? Html::grid('orderUp', $i, 'orderdown', '', 'JLIB_HTML_MOVE_UP', true, 'cb')
                                    : '&#160;';
                                ?>
                                <span><?php echo $orderUpIcon; ?></span>
                                <span><?php
                                    echo ($i < ($n - 1))
                                        ? Html::grid(
                                            'orderDown',
                                            $i,
                                            'orderup',
                                            '',
                                            'JLIB_HTML_MOVE_DOWN',
                                            true,
                                            'cb'
                                        )
                                        : '&#160;';
                                        ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                        <input
                            type="text"
                            name="order[]"
                            size="5"
                            value="<?php echo $row->get('ordering'); ?>"
                            <?php echo $disabled ?> class="text-area-order"/>
                    <?php else : ?>
                        <?php echo $row->get('ordering'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
    <?php echo Html::input('token'); ?>
</form>
