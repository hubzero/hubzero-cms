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

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{filters: mixed, rows: mixed, types: mixed} $this
 */

$canDo = Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_TAGGED'), 'tags');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_TAGS_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('tagged');

$view = $this;

// The attribute that marks the current value in the form fields below
$tblSelected = function ($value) use ($view) {
    if ($view->filters['tbl'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <label for="filter-tbl"><?php echo Lang::txt('COM_TAGS_FILTER'); ?>:</label>
        <select name="tbl" id="filter-tbl" class="filter filter-submit">
            <option value=""<?= !$this->filters['tbl'] ? ' selected="selected"' : '' ?>>
                <?= Lang::txt('COM_TAGS_FILTER_TYPE') ?>
            </option>
            <?php foreach ($this->types as $type) { ?>
                <option value="<?= $type->get('tbl') ?>"<?= $tblSelected($type->get('tbl')) ?>>
                    <?= $type->get('tbl') ?>
                </option>
            <?php } ?>
        </select>

        <input type="hidden" name="tagid" value="<?php echo $this->filters['tagid']; ?>" />
    </fieldset>

    <table class="adminlist">
        <?php if ($this->filters['tagid']) { ?>
            <caption><?php
            $tag = \Components\Tags\Models\Tag::oneOrFail($this->filters['tagid']);
            echo Lang::txt('COM_TAGS_TAG') . ': ' . $this->escape($tag->get('raw_tag')) . ' ('
                . $this->escape($tag->get('tag')) . ')';
            ?></caption>
        <?php } ?>
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <th scope="col" class="priority-5">
                    <?= Html::grid(
                        'sort',
                        'COM_TAGS_COL_ID',
                        'id',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <?php if (!$this->filters['tagid']) { ?>
                    <th scope="col">
                        <?= Html::grid(
                            'sort',
                            'COM_TAGS_COL_TAGID',
                            'tagid',
                            @$this->filters['sort_Dir'],
                            @$this->filters['sort'],
                        ) ?>
                    </th>
                <?php } ?>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TAGS_COL_TBL',
                        'tbl',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TAGS_COL_OBJECTID',
                        'objectid',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_TAGS_COL_CREATED',
                        'taggedon',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_TAGS_COL_CREATED_BY',
                        'taggerid',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="<?php echo (!$this->filters['tagid']) ? 7 : 6; ?>"><?php
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
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <input type="checkbox" name="id[]" id="cb<?= $i ?>" value="<?= $row->get('id') ?>"
                            class="checkbox-toggle" />
                        <label for="cb<?= $i ?>" class="sr-only visually-hidden"><?= $row->get('id') ?></label>
                    <?php } ?>
                </td>
                <td class="priority-5">
                    <?php if ($canDo->get('core.edit')) { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                                . $row->get('id'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
                            <?php echo $this->escape($row->get('id')); ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $this->escape($row->get('id')); ?>
                        </span>
                    <?php } ?>
                </td>
                <?php if (!$this->filters['tagid']) { ?>
                    <td>
                        <?php if ($canDo->get('core.edit')) { ?>
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=edit&id=' . $row->get('id'),
                            );
                            ?>
                            <a href="<?= $editUrl ?>">
                                <?php echo $this->escape($row->get('tagid')); ?>
                            </a>
                        <?php } else { ?>
                            <span>
                                <?php echo $this->escape($row->get('tagid')); ?>
                            </span>
                        <?php } ?>
                    </td>
                <?php } ?>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                                . $row->get('id'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
                            <?php echo $this->escape($row->get('tbl')); ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $this->escape($row->get('tbl')); ?>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                                . $row->get('id'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
                            <?php echo $this->escape($row->get('objectid')); ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $this->escape($row->get('objectid')); ?>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-3">
                    <?php
                    $timeText = ($row->get('taggedon') && $row->get('taggedon') != '0000-00-00 00:00:00')
                        ? $row->get('taggedon')
                        : Lang::txt('COM_TAGS_UNKNOWN');
                    ?>
                    <time datetime="<?= $row->get('taggedon') ?>"><?= $timeText ?></time>
                </td>
                <td class="priority-4">
                    <?php if ($row->get('taggerid')) { ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=com_members&controller=members&task=edit&id=' . $row->get('taggerid'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
                            <?php echo $this->escape($row->creator->get('name', Lang::txt('COM_TAGS_UNKNOWN'))); ?>
                        </a>
                    <?php } else { ?>
                        <?php echo Lang::txt('COM_TAGS_UNKNOWN'); ?>
                    <?php } ?>
                </td>
            </tr>
            <?php
            $i++;
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
    <input type="hidden" name="tag" value="<?php echo $this->filters['tagid']; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
