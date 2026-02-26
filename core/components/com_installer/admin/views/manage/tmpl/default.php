<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Document;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install');
if ($canDo->get('core.edit.state')) {
    Toolbar::publish('manage.publish', 'JTOOLBAR_ENABLE', true);
    Toolbar::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE', true);
    Toolbar::divider();
}
Toolbar::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', true);
Toolbar::divider();
/*if ($canDo->get('core.delete'))
{
    Toolbar::deleteList('', 'manage.remove', 'JTOOLBAR_UNINSTALL');
    Toolbar::divider();
}*/


if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_installer');
    Toolbar::divider();
}
Toolbar::help('manage');

Html::behavior('multiselect');
Html::behavior('tooltip');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', 'com_plugins');
$saveOrder = $listOrder == 'ordering';

?>

<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php
            $manageCls = ($this->controller == 'manage') ? ' class="active"' : '';
            $manageUrl = Route::url('index.php?option=' . $this->option . '&controller=manage');
            $coreTxt = Lang::txt('COM_INSTALLER_SUBMENU_CORE');
            ?>
            <a<?php echo $manageCls; ?> href="<?php echo $manageUrl; ?>">
                <?php echo $coreTxt; ?>
            </a>
        </li>
        <li>
            <?php
            $migCls = ($this->controller == 'migrations') ? ' class="active"' : '';
            $migUrl = Route::url('index.php?option=' . $this->option . '&controller=migrations');
            $migTxt = Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS');
            ?>
            <a<?php echo $migCls; ?> href="<?php echo $migUrl; ?>">
                <?php echo $migTxt; ?>
            </a>
        </li>
    </ul>
</nav>

<div id="installer-manage">
    <?php $formAction = Route::url('index.php?option=com_installer&controller=manage'); ?>
    <form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">

        <fieldset id="filter-bar">
            <div class="filter-search fltlft">
                <?php $filterLabel = Lang::txt('JSEARCH_FILTER_LABEL'); ?>
                <label class="filter-search-lbl" for="filter_search"><?php echo $filterLabel; ?></label>
                <input type="text" name="filter_search" id="filter_search"
                    class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />
                <button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
                <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>

            <div class="filter-select fltrt">

                <?php
                $helpers = '\Components\Installer\Admin\Helpers\Installer';
                $clientTxt = Lang::txt('COM_INSTALLER_VALUE_CLIENT_SELECT');
                $stateTxt = Lang::txt('COM_INSTALLER_VALUE_STATE_SELECT');
                $typeTxt = Lang::txt('COM_INSTALLER_VALUE_TYPE_SELECT');
                $folderTxt = Lang::txt('COM_INSTALLER_VALUE_FOLDER_SELECT');
                $locationOpts = $helpers::LocationOptions();
                $statusOpts = $helpers::StatusOptions();
                $typeOpts = $helpers::TypeOptions();
                $groupOpts = $helpers::GroupOptions();
                ?>

                <label for="filter_location"><?php echo $clientTxt; ?></label>
                <select name="filter_location" id="filter_location" class="inputbox filter filter-submit">
                    <option value=""><?php echo $clientTxt; ?></option>
                    <?php echo Html::select(
                        'options',
                        $locationOpts,
                        'value',
                        'text',
                        $this->filters['client_id'],
                        true
                    ); ?>
                </select>

                <label for="filter_status"><?php echo $stateTxt; ?></label>
                <select name="filter_status" id="filter_status" class="inputbox filter filter-submit">
                    <option value=""><?php echo $stateTxt; ?></option>
                    <?php echo Html::select('options', $statusOpts, 'value', 'text', $this->filters['status'], true); ?>
                </select>

                <label for="filter_type"><?php echo $typeTxt; ?></label>
                <select name="filter_type" id="filter_type" class="inputbox filter filter-submit">
                    <option value=""><?php echo $typeTxt; ?></option>
                    <?php echo Html::select('options', $typeOpts, 'value', 'text', $this->filters['type']); ?>
                </select>

                <label for="filter_group"><?php echo $folderTxt; ?></label>
                <select name="filter_group" id="filter_group" class="inputbox filter filter-submit">
                    <option value=""><?php echo $folderTxt; ?></option>
                    <?php echo Html::select('options', $groupOpts, 'value', 'text', $this->filters['group']); ?>
                </select>

            </div>
        </fieldset>

        <?php if (count($this->rows)) : ?>
        <table class="adminlist">
            <thead>
                <tr>
                    <th>
                        <?php $checkAllTxt = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                        <input type="checkbox" name="checkall-toggle"
                            id="checkall-toggle" value=""
                            title="<?php echo $checkAllTxt; ?>"
                            class="checkbox-toggle toggle-all" />
                        <label for="checkall-toggle"
                            class="sr-only visually-hidden"><?php echo $checkAllTxt; ?></label>
                    </th>
                    <th class="nowrap">
                        <?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="priority-2">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_HEADING_LOCATION',
                            'client_id',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="center">
                        <?php echo Html::grid('sort', 'JSTATUS', 'status', $listDirn, $listOrder); ?>
                    </th>
                    <th class="priority-3">
                        <?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_TYPE', 'type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="priority-4 center">
                        <?php echo Lang::txt('HVERSION'); ?>
                    </th>
                    <th class="priority-5">
                        <?php echo Lang::txt('JDATE'); ?>
                    </th>
                    <th class="priority-5">
                        <?php echo Lang::txt('JAUTHOR'); ?>
                    </th>
                    <th class="priority-4">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_HEADING_FOLDER',
                            'folder',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="priority-4">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_HEADING_ID',
                            'extension_id',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="11">
                        <?php echo $this->pagination; ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
            <?php
            $i = 0;
            foreach ($this->rows as $item) :
                $item->translate();

                $ordering   = ($listOrder == 'ordering');
                $canCheckin = User::authorise('core.manage', 'com_checkin')
                    || $item->checked_out == User::get('id')
                    || $item->checked_out == 0;
                $canChange = User::authorise('core.edit.state', 'com_installer')
                    && $canCheckin;

                $cls = $i % 2;
                if ($item->get('status') == 2) {
                    $cls .= ' protected';
                }
                ?>
                <tr class="row<?php echo $cls; ?>">
                    <td>
                        <?php echo Html::grid('id', $i, $item->get('extension_id')); ?>
                    </td>
                    <td>
                        <?php $tipTitle = htmlspecialchars($item->get('name') . '::' . $item->get('description')); ?>
                        <span class="bold hasTip" title="<?php echo $tipTitle; ?>">
                            <?php echo $item->get('name'); ?>
                        </span>
                    </td>
                    <td class="priority-2 center">
                        <?php echo $item->get('client'); ?>
                    </td>
                    <td class="center">
                        <?php if (!$item->get('element')) : ?>
                            <strong>X</strong>
                        <?php else : ?>
                            <?php echo Html::grid('published', $item->enabled, $i, '', $canChange); ?>
                        <?php endif; ?>
                    </td>
                    <td class="priority-3 center">
                        <?php echo Lang::txt('COM_INSTALLER_TYPE_' . $item->get('type')); ?>
                    </td>
                    <td class="priority-4 center">
                        <?php echo ($item->get('version') != '') ? $item->get('version') : '&#160;'; ?>
                        <?php if ($item->get('system_data')) : ?>
                            <?php if ($tooltip = $this->createCompatibilityInfo($item->get('system_data'))) : ?>
                                <?php
                                $compatTitle = Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_TITLE');
                                echo Html::behavior('tooltip', $tooltip, $compatTitle);
                                ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="priority-5 center">
                        <?php echo ($item->get('creationDate') != '') ? $item->get('creationDate') : '&#160;'; ?>
                    </td>
                    <td class="priority-5 center">
                        <?php
                        $authorTitle = addslashes(htmlspecialchars(
                            Lang::txt('COM_INSTALLER_AUTHOR_INFORMATION')
                            . '::' . $item->get('author_info')
                        ));
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo $authorTitle; ?>">
                            <?php echo ($item->get('author') != '') ? $item->get('author') : '&#160;'; ?>
                        </span>
                    </td>
                    <td class="priority-4 center">
                        <?php
                        $naTxt = Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE');
                        echo ($item->get('folder') != '') ? $item->get('folder') : $naTxt;
                        ?>
                    </td>
                    <td class="priority-4">
                        <?php echo $item->get('extension_id'); ?>
                    </td>
                </tr>
                <?php
                $i++;
            endforeach;
            ?>
            </tbody>
        </table>
        <?php endif; ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo Html::input('token'); ?>
    </form>
</div>
