<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=com_members&id=' . $this->uid . '&active=todo';

$sortAppend = '';
$sortAppend .= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend .= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend .= '&sortdir=' . urlencode($sortbyDir);

$rows = $this->todo->entries('list', $this->filters);

// Pre-compute sort link URLs and labels
$sortContentUrl = Route::url(
    $url . $sortAppend . '&sortby=content'
);
$sortContentTitle = Lang::txt(
    'PLG_PROJECTS_TODO_SORTBY_CONTENT'
);
$sortContentActive = ($this->filters['sortby'] == 'content')
    ? ' active' : '';

$sortProjectUrl = Route::url(
    $url . $sortAppend . '&sortby=project'
);
$sortProjectTitle = Lang::txt(
    'PLG_PROJECTS_TODO_SORTBY_PROJECT'
);
$sortProjectActive = ($this->filters['sortby'] == 'project')
    ? ' active' : '';

$sortCompleteUrl = Route::url(
    $url . $sortAppend . '&sortby=complete'
);
$sortCompleteTitle = Lang::txt(
    'PLG_PROJECTS_TODO_SORTBY_COMPLETE'
);
$sortCompleteActive = ($this->filters['sortby'] == 'complete')
    ? ' active' : '';

$sortDueUrl = Route::url(
    $url . $sortAppend . '&sortby=due'
);
$sortDueTitle = Lang::txt(
    'PLG_PROJECTS_TODO_SORTBY_DUE'
);
$sortDueActive = ($this->filters['sortby'] == 'due')
    ? ' active' : '';

// Pre-compute filter link URLs and classes
$filterBase = $url
    . '&sortdir=' . $this->filters['sortdir']
    . '&sortby=' . $this->filters['sortby'];

$filterActiveUrl = Route::url(
    $filterBase . '&mine=0&state=0'
);
$filterActiveLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_ACTIVE'
);
$filterActiveClass = 'filter-active';
if (!$this->filters['mine'] && !$this->filters['state']) {
    $filterActiveClass .= ' active';
}

$filterMineUrl = Route::url($filterBase . '&mine=1');
$filterMineLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_MINE'
);
$filterMineClass = 'filter-mine';
if ($this->filters['mine'] == 1) {
    $filterMineClass .= ' active';
}

$filterCompleteUrl = Route::url(
    $filterBase . '&state=1'
);
$filterCompleteLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_COMPLETE'
);
$filterCompleteClass = 'filter-complete';
if ($this->filters['state'] == 1) {
    $filterCompleteClass .= ' active';
}

// Pre-compute table header label
$dueOrCompleteLabel = ($this->filters['state'])
    ? Lang::txt('PLG_PROJECTS_TODO_COLUMN_COMPLETED')
    : Lang::txt('PLG_PROJECTS_TODO_COLUMN_DUE');

$filterAriaLabel = Lang::txt(
    'JGLOBAL_FILTER_AND_SORT_RESULTS'
);

$sortContentLabel = Lang::txt(
    'PLG_PROJECTS_TODO_SORT_CONTENT'
);
$sortProjectLabel = Lang::txt(
    'PLG_PROJECTS_TODO_SORT_PROJECT'
);
$sortCompleteLabel = Lang::txt(
    'PLG_PROJECTS_TODO_SORT_COMPLETE'
);
$sortDueLabel = Lang::txt(
    'PLG_PROJECTS_TODO_SORT_DUE'
);

?>

<div class="list-menu">
    <nav class="entries-filters"
        aria-label="<?php echo $filterAriaLabel; ?>">
        <ul class="entries-menu order-options">
            <li>
                <a class="sort-content<?php echo $sortContentActive; ?>"
                    href="<?php echo $sortContentUrl; ?>"
                    title="<?php echo $sortContentTitle; ?>">
                    &darr; <?php echo $sortContentLabel; ?>
                </a>
            </li>
            <li>
                <a class="sort-project<?php echo $sortProjectActive; ?>"
                    href="<?php echo $sortProjectUrl; ?>"
                    title="<?php echo $sortProjectTitle; ?>">
                    &darr; <?php echo $sortProjectLabel; ?>
                </a>
            </li>
            <?php if ($this->filters['state']  == 1) { ?>
            <li>
                <a class="sort-due<?php echo $sortCompleteActive; ?>"
                    href="<?php echo $sortCompleteUrl; ?>"
                    title="<?php echo $sortCompleteTitle; ?>">
                    &darr; <?php echo $sortCompleteLabel; ?>
                </a>
            </li>
            <?php } else { ?>
            <li>
                <a class="sort-complete<?php echo $sortDueActive; ?>"
                    href="<?php echo $sortDueUrl; ?>"
                    title="<?php echo $sortDueTitle; ?>">
                    &darr; <?php echo $sortDueLabel; ?>
                </a>
            </li>
            <?php } ?>
        </ul>

        <ul class="entries-menu filter-options">
            <li>
                <a href="<?php echo $filterActiveUrl; ?>"
                    title="<?php echo $filterActiveLabel; ?>"
                    class="<?php echo $filterActiveClass; ?>">
                    <?php echo $filterActiveLabel; ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $filterMineUrl; ?>"
                    title="<?php echo $filterMineLabel; ?>"
                    class="<?php echo $filterMineClass; ?>">
                    <?php echo $filterMineLabel; ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $filterCompleteUrl; ?>"
                    title="<?php echo $filterCompleteLabel; ?>"
                    class="<?php echo $filterCompleteClass; ?>">
                    <?php echo $filterCompleteLabel; ?>
                </a>
            </li>
        </ul>
    </nav>
</div>
<?php
$colOrder = Lang::txt('PLG_PROJECTS_TODO_COLUMN_ORDER');
$colItem = Lang::txt('PLG_PROJECTS_TODO_COLUMN_ITEM');
$colProject = Lang::txt('PLG_PROJECTS_TODO_PROJECT');
$colAssigned = Lang::txt('PLG_PROJECTS_TODO_COLUMN_ASSIGNED');
?>
<table class="listing entries" id="todo-table">
    <thead>
        <tr>
            <th class="checkbox"><?php echo $colOrder; ?></th>
            <th class="primarycolumn"><?php echo $colItem; ?></th>
            <th class="primarycolumn"><?php echo $colProject; ?></th>
            <th><?php echo $colAssigned; ?></th>
            <th><?php echo $dueOrCompleteLabel; ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody id="todo-table-body" class="allow-sort">
    <?php if (count($rows) > 0) {
        $order = 1; ?>
        <?php foreach ($rows as $row) {
            $color = $row->get('color');

            $overdue = $row->isOverdue();
            $oNote = $overdue
                ? ' <span class="block">'
                    . '(' . Lang::txt('PLG_PROJECTS_TODO_OVERDUE')
                    . ')</span>'
                : '';

            $todoLink = Route::url(
                $row->project()->link('todo')
                . '&action=view&todoid=' . $row->get('id')
            );
            $todoContent = \Hubzero\Utility\Str::truncate(
                $row->get('content'),
                200
            );
            $projectLink = Route::url(
                $row->project()->link('todo')
            );
            $projectTitle = $row->project('title');

            $createdLabel = Lang::txt('PLG_PROJECTS_TODO_CREATED')
                . ' ' . $row->created('date') . ' '
                . strtolower(Lang::txt('PLG_PROJECTS_TODO_BY'))
                . ' ' . $row->creator('name');
            $commentsLabel = Lang::txt(
                'PLG_PROJECTS_TODO_COMMENTS'
            );
            $commentsCount = $row->comments('count');

            $assignee = $row->isComplete()
                ? $row->closer('name')
                : $row->owner('name');
            $dateInfo = $row->isComplete()
                ? $row->closed('date')
                : $row->due('date') . $oNote;
            ?>
        <tr class="pin_grey" id="todo-<?php echo $row->get('id'); ?>">
            <td>
                <span class="ordernum"><?php echo $order; ?></span>
            </td>
            <td>
                <a href="<?php echo $todoLink; ?>">
                    <?php echo $todoContent; ?>
                </a>
                <span class="block mini faded">
                    <?php echo $createdLabel; ?>
                    | <?php echo $commentsLabel; ?>:
                    <a href="<?php echo $todoLink; ?>">
                        <?php echo $commentsCount; ?>
                    </a>
                </span>
            </td>
            <td>
                <a href="<?php echo $projectLink; ?>">
                    <?php echo $projectTitle; ?>
                </a>
            </td>
            <td class="mini faded"><?php echo $assignee; ?></td>
            <td class="mini nowrap"><?php echo $dateInfo; ?></td>
            <td></td>
        </tr>
            <?php $order++;
        } ?>
    <?php } else { ?>
        <tr>
            <td colspan="6">
                <p class="noresults">
                    <?php echo Lang::txt('PLG_PROJECTS_TODO_NO_TODOS'); ?>
                </p>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
