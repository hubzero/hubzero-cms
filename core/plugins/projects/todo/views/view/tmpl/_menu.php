<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

$sortAppend  = '';
$sortAppend .= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend .= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir   = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend .= '&sortdir=' . urlencode($sortbyDir);

$lists = $this->todo->getLists($this->model->get('id'));

$colors = array(
    'orange', 'lightblue', 'green',
    'purple', 'blue', 'black',
    'red', 'yellow', 'pink'
);
$used = array();
if (!empty($lists)) {
    foreach ($lists as $list) {
        $used[] = $list->color;
    }
}
$unused = array_diff($colors, $used);
shuffle($unused);

$layout = $this->filters['layout'];
$todolist = $this->filters['todolist'];
$sortdir = $this->filters['sortdir'];
$sortby = $this->filters['sortby'];

// Sort order links
$priorityActive = ($sortby == 'priority') ? ' active' : '';
$priorityUrl = Route::url(
    $url . $sortAppend . '&sortby=priority&l=' . $layout
);
$priorityTitle = Lang::txt('PLG_PROJECTS_TODO_SORTBY_PRIORITY');
$priorityLabel = Lang::txt('PLG_PROJECTS_TODO_SORT_PRIORITY');

$completeActive = ($sortby == 'complete') ? ' active' : '';
$completeUrl = Route::url(
    $url . $sortAppend . '&sortby=complete&l=' . $layout
);
$completeTitle = Lang::txt('PLG_PROJECTS_TODO_SORTBY_COMPLETE');
$completeLabel = Lang::txt('PLG_PROJECTS_TODO_SORT_COMPLETE');

$dueActive = ($sortby == 'due') ? ' active' : '';
$dueUrl = Route::url(
    $url . $sortAppend . '&sortby=due&l=' . $layout
);
$dueTitle = Lang::txt('PLG_PROJECTS_TODO_SORTBY_DUE');
$dueLabel = Lang::txt('PLG_PROJECTS_TODO_SORT_DUE');

// View option links
$viewBase = $url . '&list=' . $todolist;
$pinboardActive = ($layout == 'pinboard') ? ' active' : '';
$pinboardUrl = Route::url(
    $viewBase . '&l=pinboard&sortdir=' . $sortdir
    . '&sortby=' . $sortby
);
$pinboardTitle = Lang::txt(
    'PLG_PROJECTS_TODO_LIST_VIEW_PINBOARD'
);

$listActive = ($layout == 'list') ? ' active' : '';
$listUrl = Route::url(
    $viewBase . '&l=list&sortdir=' . $sortdir
    . '&sortby=' . $sortby
);
$listTitle = Lang::txt('PLG_PROJECTS_TODO_LIST_VIEW_LIST');

// Filter links
$filterBase = $viewBase . '&l=' . $layout
    . '&sortdir=' . $sortdir . '&sortby=' . $sortby;

$activeFilterUrl = Route::url(
    $filterBase . '&mine=0&state=0'
);
$activeFilterLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_ACTIVE'
);
$activeFilterClass = (!$this->filters['mine']
    && !$this->filters['state']) ? ' active' : '';

$mineFilterUrl = Route::url($filterBase . '&mine=1');
$mineFilterLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_MINE'
);
$mineFilterClass = ($this->filters['mine'] == 1)
    ? ' active' : '';

$completeFilterUrl = Route::url(
    $filterBase . '&state=1'
);
$completeFilterLabel = Lang::txt(
    'PLG_PROJECTS_TODO_FILTER_COMPLETE'
);
$completeFilterClass = ($this->filters['state'] == 1)
    ? ' active' : '';

$onListLabel = Lang::txt('PLG_PROJECTS_TODO_ON_LIST');

?>
<div class="list-menu">
    <ul class="entries-menu order-options">
        <li>
            <a class="sort-priority<?php echo $priorityActive; ?>"
                href="<?php echo $priorityUrl; ?>"
                title="<?php echo $priorityTitle; ?>">
                &darr; <?php echo $priorityLabel; ?>
            </a>
        </li>
        <?php if ($this->filters['state'] == 1) { ?>
            <li>
                <a class="sort-due<?php echo $completeActive; ?>"
                    href="<?php echo $completeUrl; ?>"
                    title="<?php echo $completeTitle; ?>">
                    &darr; <?php echo $completeLabel; ?>
                </a>
            </li>
        <?php } else { ?>
            <li>
                <a class="sort-complete<?php echo $dueActive; ?>"
                    href="<?php echo $dueUrl; ?>"
                    title="<?php echo $dueTitle; ?>">
                    &darr; <?php echo $dueLabel; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <ul class="entries-menu view-options">
        <li class="view-pinboard<?php echo $pinboardActive; ?>">
            <a href="<?php echo $pinboardUrl; ?>"
                title="<?php echo $pinboardTitle; ?>"
                >&nbsp;</a>
        </li>
        <li class="view-list<?php echo $listActive; ?>">
            <a href="<?php echo $listUrl; ?>"
                title="<?php echo $listTitle; ?>"
                >&nbsp;</a>
        </li>
    </ul>
    <ul class="entries-menu filter-options">
        <li>
            <a href="<?php echo $activeFilterUrl; ?>"
                title="<?php echo $activeFilterLabel; ?>"
                class="filter-active<?php echo $activeFilterClass; ?>"
                ><?php echo $activeFilterLabel; ?></a>
        </li>
        <li>
            <a href="<?php echo $mineFilterUrl; ?>"
                title="<?php echo $mineFilterLabel; ?>"
                class="filter-mine<?php echo $mineFilterClass; ?>"
                ><?php echo $mineFilterLabel; ?></a>
        </li>
        <li>
            <a href="<?php echo $completeFilterUrl; ?>"
                title="<?php echo $completeFilterLabel; ?>"
                class="filter-complete<?php echo $completeFilterClass; ?>"
                ><?php echo $completeFilterLabel; ?></a>
        </li>
    </ul>
    <?php if (!$todolist && $this->model->access('content')) { ?>
        <div class="list-selector" id="list-selector">
            <span id="pinner">
                <?php echo $onListLabel; ?>
                <span class="show-options">&nbsp;</span>
            </span>
            <div id="pinoptions">
                <ul>
                    <?php
                    foreach ($lists as $list) {
                        $class = $list->color
                            ? 'pin_' . $list->color
                            : 'pin_grey';
                        $listItemUrl = Route::url(
                            $url . '&list=' . $list->color
                            . '&l=' . $layout
                            . '&sortby=' . $sortby
                            . '&sortdir=' . $sortdir
                        );
                        $listName = stripslashes(
                            $list->todolist
                        );
                        ?>
                        <li>
                            <span class="<?php echo $class; ?>">
                                <a href="<?php echo $listItemUrl; ?>">
                                    <?php echo $listName; ?>
                                </a>
                            </span>
                            </label>
                        </li>
                    <?php } ?>
                    <?php if (!empty($unused)) { // can add a list
                        $newcolor = $unused[0];
                        ?>
                        <li class="newcolor">
                            <span class="pin pin_<?php echo $newcolor; ?>">
                                &nbsp;
                            </span>
                            <input type="hidden"
                                name="newcolor"
                                value="<?php echo $newcolor; ?>" />
                            <?php echo Html::input('token'); ?>
                            <input type="text"
                                name="newlist"
                                placeholder="<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_NEW_LIST'); ?>"
                                value=""
                                maxlength="50"
                                class="newlist-input"/>
                            <input type="submit"
                                class="btn"
                                value="<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD'); ?>"
                                class="todo-submit"/>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>
</div>
