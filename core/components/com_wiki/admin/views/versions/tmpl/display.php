<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Wiki\Helpers\Permissions::getActions('page');

$toolbarTitle = Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE') . ': ' . Lang::txt('COM_WIKI_REVISIONS');
Toolbar::title($toolbarTitle, 'wiki');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_WIKI_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('revisions');
?>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <tbody>
            <tr>
                <th><?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?></th>
                <td><?php echo $this->escape(stripslashes($this->page->title)); ?></td>
                <th class="priority-2"><?php echo Lang::txt('COM_WIKI_COL_SCOPE'); ?></th>
                <?php
                $scopeStr = $this->escape(stripslashes($this->page->get('scope')))
                    . ':' . $this->page->get('scope_id');
                ?>
                <td class="priority-2"><?php echo $scopeStr; ?></td>
            </tr>
            <tr>
                <th>(<?php echo Lang::txt('COM_WIKI_COL_ID'); ?>) <?php echo Lang::txt('COM_WIKI_COL_PAGENAME'); ?></th>
                <td>(<?php echo $this->page->get('id'); ?>)
                    <?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?>
                </td>
                <th class="priority-2"><?php echo Lang::txt('COM_WIKI_COL_PATH'); ?></th>
                <td class="priority-2"><?php echo $this->escape(stripslashes($this->page->get('path'))); ?></td>
            </tr>
        </tbody>
    </table>

    <fieldset id="filter-bar">
        <label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
        <input type="text" name="search" id="filter_search" class="filter"
            value="<?php echo $this->escape($this->filters['search']); ?>"
            placeholder="<?php echo Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

        <input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
        <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle"
                        value="" class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle" class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    </label>
                </th>
                <?php
                $dir = @$this->filters['sort_Dir'];
                $col = @$this->filters['sort'];
                $sortId      = Html::grid('sort', 'COM_WIKI_COL_ID', 'id', $dir, $col);
                $sortRev     = Html::grid('sort', 'COM_WIKI_COL_REVISION', 'revision', $dir, $col);
                $sortSummary = Html::grid('sort', 'COM_WIKI_COL_EDIT_SUMMARY', 'summary', $dir, $col);
                $sortApprove = Html::grid('sort', 'COM_WIKI_COL_APPROVED', 'approved', $dir, $col);
                $sortMinor   = Html::grid('sort', 'COM_WIKI_COL_MINOR_EDIT', 'minor_edit', $dir, $col);
                $sortCreated = Html::grid('sort', 'COM_WIKI_COL_CREATED', 'created', $dir, $col);
                $sortCreator = Html::grid('sort', 'COM_WIKI_COL_CREATOR', 'created_by', $dir, $col);
                ?>
                <th scope="col" class="priority-4"><?php echo $sortId; ?></th>
                <th scope="col"><?php echo $sortRev; ?></th>
                <th scope="col" class="priority-5"><?php echo $sortSummary; ?></th>
                <th scope="col"><?php echo $sortApprove; ?></th>
                <th scope="col" class="priority-4"><?php echo $sortMinor; ?></th>
                <th scope="col" class="priority-3"><?php echo $sortCreated; ?></th>
                <th scope="col" class="priority-2"><?php echo $sortCreator; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8"><?php
                // Initiate paging
                echo $this->rows->pagination;
                ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            switch ($row->get('approved')) {
                case '2':
                    $color_access = 'trashed';
                    $class = 'trashed';
                    $task = '0';
                    $alt = Lang::txt('COM_WIKI_STATE_TRASHED');
                    break;

                case '1':
                    $color_access = 'public';
                    $class = 'approved';
                    $task = '0';
                    $alt = Lang::txt('COM_WIKI_STATE_APPROVED');
                    break;
                case '0':
                    $color_access = 'private';
                    $class = 'unapprove';
                    $task = '1';
                    $alt = Lang::txt('COM_WIKI_STATE_NOT_APPROVED');
                    break;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="checkbox" name="id[]" id="cb<?php echo $i;?>"
                        value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
                    <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden">
                        <?php echo $row->get('id'); ?>
                    </label>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape($row->get('id')); ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <?php
                        $editRevUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&id=' . $row->get('id')
                            . '&pageid=' . $this->filters['pageid']
                            . '&' . Session::getFormToken() . '=1'
                        );
                        $revNum = Lang::txt('COM_WIKI_REVISION_NUM', $this->escape(stripslashes($row->get('version'))));
                        ?>
                        <a href="<?php echo $editRevUrl; ?>">
                            <?php echo $revNum; ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $revNum; ?>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-5">
                    <?php
                    $summaryVal = (trim($row->get('summary', '')))
                        ? $this->escape(stripslashes($row->get('summary')))
                        : Lang::txt('COM_WIKI_NONE');
                    echo $summaryVal;
                    ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php
                        $approveUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=approve&id=' . $row->get('id')
                            . '&pageid=' . $this->filters['pageid']
                            . '&approve=' . $task
                            . '&' . Session::getFormToken() . '=1'
                        );
                        ?>
                        <a class="access <?php echo $class . ' ' . $color_access; ?>" href="<?php echo $approveUrl; ?>">
                            <span><?php echo $alt; ?></span>
                        </a>
                    <?php } else { ?>
                        <span class="access <?php echo $class . ' ' . $color_access; ?>">
                            <span><?php echo $alt; ?></span>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-4">
                    <span class="state <?php echo ($row->get('minor_edit')) ? 'yes' : 'no'; ?>">
                        <span><?php echo $this->escape($row->get('minor_edit')); ?></span>
                    </span>
                </td>
                <td class="priority-2">
                    <?php $createdStr = $this->escape($row->created('time') . ' ' . $row->created('date')); ?>
                    <time datetime="<?php echo $this->escape($row->get('created')); ?>">
                        <?php echo $createdStr; ?>
                    </time>
                </td>
                <td class="priority-3">
                    <span class="glyph user">
                        <?php
                        $creatorName = $this->escape(
                            stripslashes($row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')))
                        );
                        echo $creatorName;
                        ?>

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

    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />

    <?php echo Html::input('token'); ?>
</form>
