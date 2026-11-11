<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{filters: mixed, todo: mixed, uid: mixed} $this
 */

$url = 'index.php?option=com_members&id=' . $this->uid . '&active=todo';

$sortAppend = '';
$sortAppend .= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend .= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend .= '&sortdir=' . urlencode($sortbyDir);

$rows = $this->todo->entries('list', $this->filters);

?>

<div class="list-menu">
    <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
        <ul class="entries-menu order-options">
            <li>
                <a class="sort-content<?= $this->filters['sortby'] == 'content' ? ' active' : '' ?>"
                    href="<?= Route::url($url . $sortAppend . '&sortby=content') ?>"
                    title="<?= Lang::txt('PLG_PROJECTS_TODO_SORTBY_CONTENT') ?>">
                    &darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_CONTENT'); ?>
                </a>
            </li>
            <li>
                <a class="sort-project<?= $this->filters['sortby'] == 'project' ? ' active' : '' ?>"
                    href="<?= Route::url($url . $sortAppend . '&sortby=project') ?>"
                    title="<?= Lang::txt('PLG_PROJECTS_TODO_SORTBY_PROJECT') ?>">
                    &darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_PROJECT'); ?>
                </a>
            </li>
            <?php if ($this->filters['state']  == 1) { ?>
            <li>
                <a class="sort-due<?= $this->filters['sortby'] == 'complete' ? ' active' : '' ?>"
                    href="<?= Route::url($url . $sortAppend . '&sortby=complete') ?>"
                    title="<?= Lang::txt('PLG_PROJECTS_TODO_SORTBY_COMPLETE') ?>">
                    &darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_COMPLETE'); ?>
                </a>
            </li>
            <?php } else { ?>
            <li>
                <a class="sort-complete<?= $this->filters['sortby'] == 'due' ? ' active' : '' ?>"
                    href="<?= Route::url($url . $sortAppend . '&sortby=due') ?>"
                    title="<?= Lang::txt('PLG_PROJECTS_TODO_SORTBY_DUE') ?>">
                    &darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_DUE'); ?>
                </a>
            </li>
            <?php } ?>
        </ul>

        <ul class="entries-menu filter-options">
            <?php
            $linkUrl = Route::url(
                $url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby']
                    . '&mine=0&state=0',
            );
            $activeClass = !$this->filters['mine'] && !$this->filters['state'] ? ' active' : '';
            ?>
            <li><a href="<?= $linkUrl ?>" title="<?= Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE') ?>"
                class="filter-active<?= $activeClass ?>"><?= Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE') ?></a></li>
            <?php
            $linkUrl = Route::url(
                $url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&mine=1',
            );
            $linkText = Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE');
            ?>
            <li><a href="<?= $linkUrl ?>" title="<?= Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE') ?>"
                class="filter-mine<?= $this->filters['mine'] == 1 ? ' active' : '' ?>"><?= $linkText ?></a></li>
            <?php
            $linkUrl = Route::url(
                $url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&state=1',
            );
            $linkText = Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE');
            ?>
            <li><a href="<?= $linkUrl ?>" title="<?= Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE') ?>"
                class="filter-complete<?= $this->filters['state'] == 1 ? ' active' : '' ?>"><?= $linkText ?></a></li>
        </ul>
    </nav>
</div>
<table class="listing entries" id="todo-table">
    <thead>
        <tr>
            <th class="checkbox"><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ORDER'); ?></th>
            <th class="primarycolumn"><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ITEM'); ?></th>
            <th class="primarycolumn"><?php echo Lang::txt('PLG_PROJECTS_TODO_PROJECT'); ?></th>
            <th><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ASSIGNED'); ?></th>
            <th>
                <?= ($this->filters['state'])
                    ? Lang::txt('PLG_PROJECTS_TODO_COLUMN_COMPLETED')
                    : Lang::txt('PLG_PROJECTS_TODO_COLUMN_DUE') ?>
            </th>
            <th aria-label="<?php echo Lang::txt('Options'); ?>"><span class="sr-only">Options</span></th>
        </tr>
    </thead>
    <tbody id="todo-table-body" class="allow-sort">
    <?php if (count($rows) > 0) {
        $order = 1; ?>
        <?php foreach ($rows as $row) {
            $color = $row->get('color');

            $overdue = $row->isOverdue();
            $oNote = $overdue ? ' <span class="block">(' . Lang::txt('PLG_PROJECTS_TODO_OVERDUE') . ')</span>' : '';
            ?>
        <tr class="pin_grey" id="todo-<?php echo $row->get('id'); ?>">
            <td><span class="ordernum"><?php echo $order; ?></span></td>
            <?php
            $viewUrl = Route::url($row->project()->link('todo') . '&action=view&todoid=' . $row->get('id'));
            ?>
            <td><a href="<?= $viewUrl ?>"><?= \Hubzero\Utility\Str::truncate($row->get('content'), 200) ?></a>
                <?php
                $spanText = Lang::txt('PLG_PROJECTS_TODO_CREATED') . ' ' . $row->created('date') . ' '
                    . strtolower(Lang::txt('PLG_PROJECTS_TODO_BY')) . ' ' . $row->creator('name');
                ?>
                <span class="block mini faded"><?= $spanText ?>
                    <?php
                    $viewUrl = Route::url($row->project()->link('todo') . '&action=view&todoid=' . $row->get('id'));
                    ?>
                    | <?= Lang::txt('PLG_PROJECTS_TODO_COMMENTS') ?>: <a
                        href="<?= $viewUrl ?>"><?= $row->comments('count') ?></a></span>
            </td>
            <?php
            $linkUrl = Route::url($row->project()->link('todo'));
            ?>
            <td><a href="<?= $linkUrl ?>"><?= $this->escape($row->project('title')) ?></a></td>
            <td class="mini faded"><?php echo $row->isComplete() ? $row->closer('name') : $row->owner('name'); ?></td>
            <td class="mini nowrap"><?= $row->isComplete() ? $row->closed('date') : $row->due('date') . $oNote ?></td>
            <td></td>
        </tr>
            <?php $order++;
        } ?>
    <?php } else { ?>
        <tr>
            <td colspan="6"><p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TODO_NO_TODOS'); ?></p></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
