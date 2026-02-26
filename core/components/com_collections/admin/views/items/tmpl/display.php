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

$canDo = \Components\Collections\Helpers\Permissions::getActions('post');

Toolbar::title(
    Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_ITEMS'),
    'collections'
);
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$sortDir = @$this->filters['sort_Dir'];
$sort = @$this->filters['sort'];
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span6">
                <?php
                $searchVal = $this->escape($this->filters['search']);
                $searchPlaceholder = Lang::txt(
                    'COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER'
                );
                ?>
                <label for="filter_search">
                    <?php echo Lang::txt('JSEARCH_FILTER'); ?>:
                </label>
                <input type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPlaceholder; ?>" />

                <input type="submit"
                    value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
                <button type="button" class="filter-clear">
                    <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>
            <div class="col span6">
                <?php
                $filterTypeLabel = Lang::txt(
                    'COM_COLLECTIONS_FILTER_TYPE'
                );
                $filterTypeAll = Lang::txt(
                    'COM_COLLECTIONS_FILTER_TYPE_ALL'
                );
                ?>
                <label for="filter-type">
                    <?php echo $filterTypeLabel; ?>
                </label>
                <select name="type"
                    id="filter-type"
                    class="filter filter-submit">
                    <option value="">
                        <?php echo $filterTypeAll; ?>
                    </option>
                    <?php foreach ($this->types as $type) { ?>
                        <?php
                        $typeVal = $this->escape($type->get('type'));
                        $selected = ($type->get('type') == $this->filters['type'])
                            ? ' selected="selected"'
                            : '';
                        ?>
                        <option value="<?php echo $typeVal; ?>"<?php echo $selected; ?>>
                            <?php echo $typeVal; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    </label>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_ID', 'id', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_DESCRIPTION', 'description', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_CREATED', 'created', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_CREATEDBY', 'created_by', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-2">
                    <?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_TYPE', 'type', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo Lang::txt('COM_COLLECTIONS_COL_POSTS'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
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
            $content = \Hubzero\Utility\Str::truncate(
                strip_tags($row->get('description')),
                75
            );
            $content = $content ?: Lang::txt('COM_COLLECTIONS_NONE');
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <input type="checkbox"
                            name="id[]"
                            id="cb<?php echo $i; ?>"
                            value="<?php echo $row->get('id'); ?>"
                            class="checkbox-toggle" />
                        <label for="cb<?php echo $i; ?>"
                            class="sr-only visually-hidden">
                            <?php echo $row->get('id'); ?>
                        </label>
                    <?php } ?>
                </td>
                <td class="priority-4">
                    <?php echo $row->get('id'); ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&id=' . $row->get('id')
                        );
                        ?>
                        <a href="<?php echo $editUrl; ?>">
                            <span><?php echo $content; ?></span>
                        </a>
                    <?php } else { ?>
                        <span>
                            <span><?php echo $content; ?></span>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-4">
                    <time datetime="<?php echo $row->get('created'); ?>">
                        <?php echo $row->get('created'); ?>
                    </time>
                </td>
                <td class="priority-3">
                    <span class="glyph member">
                        <?php
                        $creatorName = $row->creator->get(
                            'name',
                            Lang::txt('COM_COLLECTIONS_UNKNOWN')
                        );
                        echo $this->escape($creatorName);
                        ?>
                    </span>
                </td>
                <td class="priority-2">
                    <?php echo $this->escape($row->get('type')); ?>
                </td>
                <td class="priority-3">
                    <?php echo $this->escape($row->posts()->total()); ?>
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
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
