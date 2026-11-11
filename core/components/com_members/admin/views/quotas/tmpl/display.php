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

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{classes: mixed, filters: mixed, rows: mixed} $this
 */

$canDo = Components\Members\Helpers\Admin::getActions();

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS'), 'user');
if ($canDo->get('core.edit')) {
    Toolbar::custom('syncQuotasToSystem', 'refresh', 'refresh', 'Sync Selected User Quotas');
    Toolbar::spacer();
}

if ($canDo->get('core.create')) {
    Toolbar::addNew();
    Toolbar::spacer();
}

Toolbar::help('quotas');


$this
    ->css('quotas.css')
    ->js('quotas.js');

$view = $this;

// The attribute that marks the current value in the form fields below
$searchFieldSelected = function ($value) use ($view) {
    if ($view->filters['search_field'] == $value) {
        return ' selected="selected"';
    }

    return '';
};

$classAliasSelected = function ($value) use ($view) {
    if ($view->filters['class_alias'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<?php
    $this
         ->view('_submenu')
         ->display();
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span5">
                <label for="filter_search_field"><?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?></label>
                <select name="search_field" id="filter_search_field" class="filter">
                    <option value="username"<?= $searchFieldSelected('username') ?>>
                        <?= Lang::txt('COM_MEMBERS_QUOTA_USERNAME') ?>
                    </option>
                    <option value="name"<?= $searchFieldSelected('name') ?>>
                        <?= Lang::txt('COM_MEMBERS_QUOTA_NAME') ?>
                    </option>
                </select>

                <label for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_FOR'); ?></label>
                <input type="text" name="search" id="filter_search" class="filter"
                    value="<?= $this->escape($this->filters['search']) ?>"
                    placeholder="<?= Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER') ?>" />

                <input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_GO'); ?>" />
            </div>
            <div class="col span7">
                <select name="class_alias" id="filter_class_alias" class="filter filter-submit">
                    <option value=""<?= $classAliasSelected('') ?>>
                        <?= Lang::txt('COM_MEMBERS_FILTER_QUOTA_CLASS') ?>
                    </option>
                    <?php foreach ($this->classes as $class) : ?>
                        <?php
                        $fieldValue = $this->escape($class->get('alias'));
                        $selectedAttr = $classAliasSelected($class->get('alias'));
                        $optionText = $this->escape($class->get('alias'));
                        ?>
                        <option value="<?= $fieldValue ?>"<?= $selectedAttr ?>><?= $optionText ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </fieldset>
    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <?php
                $headerText = Html::grid(
                    'sort',
                    'COM_MEMBERS_QUOTA_ID',
                    'id',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort'],
                );
                ?>
                <!-- <th class="priority-6"><?= $headerText ?></th> -->
                <th class="priority-5">
                    <?= Html::grid(
                        'sort',
                        'COM_MEMBERS_QUOTA_USER_ID',
                        'user_id',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_MEMBERS_QUOTA_USERNAME',
                        'username',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th>
                    <?= Html::grid(
                        'sort',
                        'COM_MEMBERS_QUOTA_NAME',
                        'name',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_MEMBERS_QUOTA_CLASS',
                        'class_alias',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th><?php echo Lang::txt('COM_MEMBERS_QUOTA_DISK_USAGE'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
                    <?php
                    // Initiate paging
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
            ?>
            <?php
            $getQuotaUsageUrl = Route::url(
                'index.php?option=com_members&controller=quotas&task=getQuotaUsage&id=' . $row->get('id'),
                false,
            );
            ?>
            <tr class="<?= "row$k quota-row" ?>" data-quota="<?= $getQuotaUsageUrl ?>">
                <td>
                    <input type="checkbox" name="id[]" id="cb<?= $i ?>" value="<?= $row->get('user_id') ?>"
                        class="checkbox-toggle" />
                    <label for="cb<?= $i ?>" class="sr-only visually-hidden"><?= $row->get('user_id') ?></label>
                </td>
                <td class="priority-5">
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                            . $row->get('id'),
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <?php echo $this->escape($row->get('user_id')); ?>
                    </a>
                </td>
                <td class="priority-4">
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='
                            . $row->get('id'),
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <?php echo $this->escape($row->get('username')); ?>
                    </a>
                </td>
                <td>
                    <?php echo $this->escape($row->get('name')); ?>
                </td>
                <td class="priority-3">
                    <?php echo $this->escape($row->get('class_alias', 'custom')); ?>
                </td>
                <td>
                    <div class="usage-calculating"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CALCULATING'); ?></div>
                    <div class="usage-outer">
                        <div class="usage-inner"></div>
                    </div>
                    <div class="usage-unavailable"><?php echo Lang::txt('COM_MEMBERS_QUOTA_UNAVAILABLE'); ?></div>
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
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>