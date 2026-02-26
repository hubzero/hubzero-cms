<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'), 'import');

if ($canDo->get('core.admin')) {
    Toolbar::custom('sample', 'sample', 'sample', 'COM_GROUPS_IMPORT_SAMPLE', false);
    Toolbar::spacer();
    Toolbar::custom('run', 'script', 'script', 'COM_GROUPS_RUN');
    Toolbar::custom('runtest', 'runtest', 'script', 'COM_GROUPS_TEST_RUN');
    Toolbar::spacer();
    Toolbar::addNew();
    Toolbar::editList();
    Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

$this->css('import');
?>

<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php $url = Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>
            <a<?php if ($this->controller == 'imports') {
                echo ' class="active"';
              } ?> href="<?php echo $url; ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'); ?></a>
        </li>
        <li>
            <?php $url = Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>
            <a<?php if ($this->controller == 'importhooks') {
                echo ' class="active"';
              } ?> href="<?php echo $url; ?>"><?php echo Lang::txt('COM_GROUPS_IMPORT_HOOKS'); ?></a>
        </li>
    </ul>
</nav>

<?php $url = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $url; ?>" method="post" name="adminForm" id="adminForm">

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all" />
                    <?php $txt = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <label for="checkall-toggle" class="sr-only visually-hidden"><?php echo $txt; ?></label>
                </th>
                <?php $val = Html::grid('sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
                <th scope="col" class="priority-6"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_IMPORT_DISPLAY_FIELD_NAME',
                    'name',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col"><?php echo $val; ?></th>
                <?php $txt = Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_NUMRECORDS'); ?>
                <th scope="col" class="priority-4"><?php echo $txt; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_IMPORT_DISPLAY_FIELD_CREATED',
                    'created_at',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col" class="priority-3"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_IMPORT_DISPLAY_FIELD_LASTRUN',
                    'ran_at',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col"><?php echo $val; ?></th>
                <?php $txt = Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?>
                <th scope="col" class="priority-4"><?php echo $txt; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">
                    <?php
                    // Initiate paging
                    echo $this->imports->pagination;
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php if ($this->imports->count() > 0) :
                $i = 0;
                ?>
                <?php foreach ($this->imports as $import) : ?>
                    <tr>
                        <td>
                            <?php if ($canDo->get('core.admin')) { ?>
                                <?php $val = $i;?>" value="<?php echo $import->get('id'); ?>
                                <input type="checkbox" name="id[]" id="cb<?php echo $val; ?>" class="checkbox-toggle" />
                                <?php $val = $i;?>" class="sr-only visually-hidden"><?php echo $import->get('id'); ?>
                                <label for="cb<?php echo $val; ?></label>
                            <?php } ?>
                        </td>
                        <td class="priority-6">
                            <?php echo $import->get('id'); ?>
                        </td>
                        <td>
                            <?php if ($canDo->get('core.admin')) { ?>
                                <?php
                                $url = Route::url(
                                    'index.php?option=' .
                                    $this->option .
                                    '&controller=' .
                                    $this->controller .
                                    '&task=edit&id=' .
                                    $import->get('id')
                                );
                                ?>
                                <a href="<?php echo $url; ?>">
                                    <?php echo $this->escape($import->get('name')); ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $this->escape($import->get('name')); ?>
                            <?php } ?>
                            <br />
                            <span class="hint">
                                <?php echo nl2br($this->escape($import->get('notes'))); ?>
                            </span>
                        </td>
                        <td class="priority-4">
                            <?php echo $this->escape($import->get('count', 0)); ?>
                        </td>
                        <td class="priority-3">
                            <strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON'); ?></strong>
                            <?php $val = Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a'); ?>
                            <time datetime="<?php echo $import->get('created_at'); ?>"><?php echo $val; ?></time><br />
                            <strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY'); ?></strong>
                            <?php
                            if ($created_by = User::getInstance($import->get('created_by'))) {
                                echo $created_by->get('name');
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                                $lastRun = $import->runs()
                                    ->whereEquals('import_id', $import->get('id'))
                                    ->whereEquals('dry_run', 0)
                                    ->ordered()
                                    ->row();
                            ?>
                            <?php if ($lastRun->get('id')) : ?>
                                <strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON'); ?></strong>
                                <?php $val = Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a'); ?>
                                <time datetime="<?php echo $import->get('ran_at'); ?>"><?php echo $val; ?></time><br />
                                <strong><?php echo Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY'); ?></strong>
                                <?php
                                if ($created_by = User::getInstance($lastRun->get('ran_by'))) {
                                    echo $created_by->get('name');
                                }
                                ?>
                            <?php else : ?>
                                n/a
                            <?php endif; ?>
                        </td>
                        <td class="priority-4">
                            <?php
                                $runs = $import->runs()
                                    ->whereEquals('import_id', $import->get('id'))
                                    ->whereEquals('dry_run', 0)
                                    ->total();

                                echo $runs;
                            ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php echo Lang::txt('COM_GROUPS_IMPORT_NONE'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>