<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Forum\Helpers\Permissions::getActions('section');

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SECTIONS'), 'forum');
if ($canDo->get('core.admin')) {
    Toolbar::preferences($this->option, '550');
    Toolbar::spacer();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('sections');
$db = App::get('db');
$query = $db
    ->getQuery()
    ->select('id')
    ->select('title')
    ->from('#__viewlevels');
$db->setQuery($query->toString());
$access_levels = $db->loadAssocList();

$view = $this;

// The attribute that marks the current value in the form fields below
$scopeinfoSelected = function ($value) use ($view) {
    if ($view->filters['scopeinfo'] == $value) {
        return ' selected="selected"';
    }

    return '';
};

$stateSelected = function ($value) use ($view) {
    if ($view->filters['state'] == $value) {
        return ' selected="selected"';
    }

    return '';
};

$filterStateSelected = function ($value) use ($view) {
    if ($view->filters['state'] === $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span4">
                <label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
                <input type="text" name="search" id="filter_search" class="filter"
                    value="<?= $this->escape($this->filters['search']) ?>"
                    placeholder="<?= Lang::txt('COM_FORUM_FILTER_SEARCH_PLACEHOLDER') ?>" />

                <input type="submit" value="<?php echo Lang::txt('COM_FORUM_GO'); ?>" />
                <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <div class="col span8 align-right">
                <label for="scopeinfo"><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE'); ?>:</label>
                <select name="scopeinfo" id="scopeinfo" class="filter filter-submit">
                    <option
                        value=""<?= $scopeinfoSelected('') ?>><?= Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT') ?></option>
                    <option
                        value="site:0"<?= $scopeinfoSelected('site:0') ?>><?= Lang::txt('COM_FORUM_NONE') ?></option>
                    <?php
                    $list = [];
                    foreach ($this->scopes as $result) {
                        if (!isset($list[$result->scope])) {
                            $list[$result->scope] = [];
                        }
                        $list[$result->scope][$result->scope_id] = $result;
                    }

                    $html = '';
                    foreach ($list as $label => $optgroup) {
                        if ($label == 'site') {
                            continue;
                        }
                        $html .= ' <optgroup label="' . $label . '">';
                        foreach ($optgroup as $result) {
                            $html .= ' <option value="' . $result->scope . ':' . $result->scope_id . '"';
                            if ($this->filters['scopeinfo'] == $result->scope . ':' . $result->scope_id) {
                                $html .= ' selected="selected"';
                            }
                            $html .= '>' . $this->escape(stripslashes($result->caption));
                            $html .= '</option>' . "\n";
                        }
                        $html .= '</optgroup>' . "\n";
                    }
                    echo $html;
                    ?>
                </select>

                <label for="filter-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label>
                <select name="state" id="filter-state" class="filter filter-submit">
                    <option value="-1"<?= $stateSelected('-1') ?>><?= Lang::txt('COM_FORUM_ALL_STATES') ?></option>
                    <option value="0"<?= $filterStateSelected(0) ?>><?= Lang::txt('JUNPUBLISHED') ?></option>
                    <option value="1"<?= $filterStateSelected(1) ?>><?= Lang::txt('JPUBLISHED') ?></option>
                    <option value="2"<?= $filterStateSelected(2) ?>><?= Lang::txt('JTRASHED') ?></option>
                </select>

                <label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
                <select name="access" id="filter-access" class="filter filter-submit">
                    <option value="-1"><?php echo Lang::txt('JOPTION_SELECT_ACCESS'); ?></option>
                    <?= Html::select(
                        'options',
                        Html::access('assetgroups'),
                        'value',
                        'text',
                        $this->filters['access'],
                    ) ?>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
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
                        'COM_FORUM_COL_ID',
                        'id',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_FORUM_COL_TITLE',
                        'title',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-2">
                    <?= Html::grid(
                        'sort',
                        'COM_FORUM_COL_STATE',
                        'state',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_FORUM_COL_ACCESS',
                        'access',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_FORUM_COL_SCOPE',
                        'scope',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7"><?php
                // initiate paging
                echo $this->rows->pagination;
                ?></td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            $k = 0;
            $i = 0;
            foreach ($this->rows as $row) {
                switch ($row->get('state')) {
                    case '2':
                        $task = 'publish';
                        $alt = Lang::txt('JTRASHED');
                        $cls = 'trash';
                        break;
                    case '1':
                        $task = 'unpublish';
                        $alt = Lang::txt('JPUBLISHED');
                        $cls = 'publish';
                        break;
                    case '0':
                    default:
                        $task = 'publish';
                        $alt = Lang::txt('JUNPUBLISHED');
                        $cls = 'unpublish';
                        break;
                }

                switch ($row->get('access')) {
                    case 1:
                        $color_access = 'public';
                        $task_access  = '1';
                        break;
                    case 2:
                        $color_access = 'registered';
                        $task_access  = '2';
                        break;
                    case 3:
                        $color_access = 'special';
                        $task_access  = '3';
                        break;
                    case 4:
                        $color_access = 'protected';
                        $task_access  = '4';
                        break;
                    case 5:
                        $color_access = 'private';
                        $task_access  = '5';
                        break;
                    default:
                        $color_access = 'other';
                        $task_access  = (string) $row->get('access');
                        break;
                }
                $row->access_level = $access_levels[$row->access - 1]['title'];

                $cat = $row->categories->count();
                ?>
                <tr class="<?php echo "row$k" . ($row->get('state') == 2 ? ' archived' : ''); ?>">
                    <td>
                        <input type="checkbox" name="id[]" id="cb<?= $i ?>"
                            value="<?= $row->get('id') ?>"class="checkbox-toggle" />
                        <label for="cb<?= $i ?>" class="sr-only visually-hidden"><?= $row->get('id') ?></label>
                    </td>
                    <td class="priority-5">
                        <?php echo $row->get('id'); ?>
                    </td>
                    <td>
                        <?php if ($canDo->get('core.edit')) { ?>
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=edit&id=' . $row->get('id'),
                            );
                            ?>
                            <a href="<?= $editUrl ?>">
                                <?php echo $this->escape(stripslashes($row->get('title'))); ?>
                            </a>
                        <?php } else { ?>
                            <span>
                                <?php echo $this->escape(stripslashes($row->get('title'))); ?>
                            </span>
                        <?php } ?>
                    </td>
                    <td class="priority-2">
                        <?php if ($canDo->get('core.edit.state')) { ?>
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task='
                                    . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1',
                            );
                            ?>
                            <a class="state <?= $cls ?>" href="<?= $linkUrl ?>"
                                title="<?= Lang::txt('COM_FORUM_SET_TO', $task) ?>">
                                <span><?php echo $alt; ?></span>
                            </a>
                        <?php } else { ?>
                            <span class="state <?php echo $cls; ?>">
                                <span><?php echo $alt; ?></span>
                            </span>
                        <?php } ?>
                    </td>
                    <td class="priority-4">
                        <span class="access <?= $color_access ?>"><?= $this->escape($row->access_level) ?></span>
                    </td>
                    <td class="priority-3">
                        <span class="scope">
                            <?php
                            $spanText = $this->escape($row->get('scope')) . ' ('
                                . (
                                    isset($list[$row->get('scope')][$row->get('scope_id')])
                                        ? $this->escape($list[$row->get('scope')][$row->get('scope_id')]->caption)
                                        : $this->escape($row->get('scope_id'))
                                ) . ')';
                            ?>
                            <span><?= $spanText ?></span>
                        </span>
                    </td>
                    <td>
                        <?php if ($cat > 0) { ?>
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=categories&section_id='
                                    . $row->get('id'),
                            );
                            ?>
                            <a class="glyph category" href="<?= $linkUrl ?>">
                                <span><?php echo $cat; ?></span>
                            </a>
                        <?php } else { ?>
                            <span class="glyph category">
                                <span><?php echo $cat; ?></span>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
                $i++;
            }
            ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
