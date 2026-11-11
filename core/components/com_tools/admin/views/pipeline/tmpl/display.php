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

Toolbar::title(Lang::txt('COM_TOOLS'), 'tools');
Toolbar::preferences('com_tools', '550');
Toolbar::spacer();
Toolbar::help('tools');

$this->css();

Html::behavior('tooltip');

$view = $this;

// The attribute that marks the current value in the form fields below
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
            <div class="col span6">
                <label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?></label>
                <input type="text" name="search" id="filter_search" class="filter"
                    value="<?= $this->escape($this->filters['search']) ?>"
                    placeholder="<?= Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER') ?>" />

                <input type="submit" value="<?php echo Lang::txt('COM_TOOLS_GO'); ?>" />
                <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <div class="col span6">
                <label for="filter-state"><?php echo Lang::txt('COM_TOOLS_FIELD_STATE'); ?>:</label>
                <select name="state" id="filter-state" class="filter filter-submit">
                    <option value="-1"<?= $stateSelected('-1') ?>><?= Lang::txt('COM_TOOLS_ALL_STATES') ?></option>
                    <option value="0"<?= $filterStateSelected(0) ?>><?= Lang::txt('JUNPUBLISHED') ?></option>
                    <option value="1"<?= $filterStateSelected(1) ?>><?= Lang::txt('COM_TOOLS_REGISTERED') ?></option>
                    <option value="2"<?= $filterStateSelected(2) ?>><?= Lang::txt('COM_TOOLS_CREATED') ?></option>
                    <option value="3"<?= $filterStateSelected(3) ?>><?= Lang::txt('COM_TOOLS_UPLOADED') ?></option>
                    <option value="4"<?= $filterStateSelected(4) ?>><?= Lang::txt('COM_TOOLS_INSTALLED') ?></option>
                    <option value="5"<?= $filterStateSelected(5) ?>><?= Lang::txt('COM_TOOLS_UPDATED') ?></option>
                    <option value="6"<?= $filterStateSelected(6) ?>><?= Lang::txt('COM_TOOLS_APPROVED') ?></option>
                    <option value="7"<?= $filterStateSelected(7) ?>><?= Lang::txt('JPUBLISHED') ?></option>
                    <option value="8"<?= $filterStateSelected(8) ?>><?= Lang::txt('COM_TOOLS_RETIRED') ?></option>
                    <option value="9"<?= $filterStateSelected(9) ?>><?= Lang::txt('COM_TOOLS_ABANDONED') ?></option>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col" class="priority-5">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_ID',
                        'id',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_NAME',
                        'toolname',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_TITLE',
                        'title',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_STATE',
                        'state',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_REGISTERED',
                        'registered',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_STATECHANGED',
                        'state_changed',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_VERSIONS',
                        'versions',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8">
                    <?php
                    // Initiate paging
                    echo $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit'],
                    );
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
            $row = &$this->rows[$i];

            switch ($row['state']) {
                case 0:
                    $state = 'unpublished';
                    break;
                case 1:
                    $state = 'registered';
                    break;
                case 2:
                    $state = 'created';
                    break;
                case 3:
                    $state = 'uploaded';
                    break;
                case 4:
                    $state = 'installed';
                    break;
                case 5:
                    $state = 'updated';
                    break;
                case 6:
                    $state = 'approved';
                    break;
                case 7:
                    $state = 'published';
                    break;
                case 8:
                    $state = 'retired';
                    break;
                case 9:
                    $state = 'abandoned';
                    break;
                default:
                    $state = 'unknown';
                    break;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="radio" name="id" id="cb<?= $i ?>" value="<?php echo $row['id'] ?>"
                        class="checkbox-toggle" />
                </td>
                <td class="priority-5">
                    <?php echo $this->escape($row['id']); ?>
                </td>
                <td>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                            . $row['id'],
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <?php echo $this->escape(stripslashes($row['toolname'])); ?>
                    </a>
                </td>
                <td class="priority-4">
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                            . $row['id'],
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <?php echo $this->escape(stripslashes($row['title'])); ?>
                    </a>
                </td>
                <td>
                    <span class="state <?= $state ?> hasTip"
                        title="<?= $this->escape(Lang::txt(strtoupper($this->option) . '_' . strtoupper($state))) ?>">
                        <?php
                        $spanText = $this->escape(Lang::txt(strtoupper($this->option) . '_' . strtoupper($state)));
                        ?>
                        <span><?= $spanText ?></span>
                    </span>
                </td>
                <td class="priority-3">
                    <time><?php echo $this->escape($row['registered']); ?></time>
                </td>
                <td class="priority-3">
                    <time><?php echo $this->escape($row['state_changed']); ?></time>
                </td>
                <td>
                    <?php
                    $linkUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=versions&id=' . $row['id'],
                    );
                    ?>
                    <a class="glyph menulist" href="<?= $linkUrl ?>">
                        <span><?php echo $this->escape($row['versions']); ?></span>
                    </a>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="view" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>