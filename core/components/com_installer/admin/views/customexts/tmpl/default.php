<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_' . $this->controller));
Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_' . $this->controller), 'customexts');

if ($canDo->get('core.create')) {
    Toolbar::addNew('customexts.edit');
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_INSTALLER_CUSTOMEXTS_DELETE_CONFIRM', 'remove');
}
Toolbar::divider();
if ($canDo->get('core.edit.state')) {
    Toolbar::publish('customexts.publish', 'JTOOLBAR_ENABLE', true);
    Toolbar::unpublish('customexts.unpublish', 'JTOOLBAR_DISABLE', true);
    Toolbar::divider();
}
Toolbar::custom('customexts.update', 'refresh', '', 'COM_INSTALLER_CUSTOMEXTS_UPDATE_CODE');
Toolbar::divider();

Toolbar::help('customexts');

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
            $cls = ($this->controller == 'customexts') ? ' class="active"' : '';
            $url = Route::url('index.php?option=' . $this->option . '&controller=customexts');
            $txt = Lang::txt('COM_INSTALLER_CUSTOMEXTS_SUBMENU');
            ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>">
                <?php echo $txt; ?>
            </a>
        </li>
    </ul>
</nav>

<div id="installer-customexts">
    <?php $actionUrl = Route::url('index.php?option=com_installer&controller=customexts'); ?>
    <form action="<?php echo $actionUrl; ?>"
        method="post"
        name="adminForm"
        id="adminForm"
    >

        <fieldset id="filter-bar">

            <div class="filter-search fltlft">
                <?php $filterLabel = Lang::txt('JSEARCH_FILTER_LABEL'); ?>
                <label class="filter-search-lbl" for="filter_search">
                    <?php echo $filterLabel; ?>
                </label>
                <?php $searchVal = $this->escape($this->filters['search']); ?>
                <input type="text"
                    name="filter_search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                />
                <button type="submit">
                    <?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>
                </button>
                <button type="button" class="filter-clear">
                    <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>

            <div class="filter-select fltrt">

                <?php
                $helpers = '\Components\Installer\Admin\Helpers\Installer';
                $clientLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT');
                $stateLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_STATE_SELECT');
                $typeLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT');
                $folderLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT');
                $locationOpts = $helpers::LocationOptions();
                $statusOpts = $helpers::StatusOptions();
                $typeOpts = $helpers::TypeOptions();
                $groupOpts = $helpers::GroupOptions();
                ?>

                <label for="filter_location">
                    <?php echo $clientLabel; ?>
                </label>
                <select name="filter_location"
                    id="filter_location"
                    class="inputbox filter filter-submit"
                >
                    <option value="">
                        <?php echo $clientLabel; ?>
                    </option>
                    <?php echo Html::select(
                        'options',
                        $locationOpts,
                        'value',
                        'text',
                        $this->filters['client_id'],
                        true
                    ); ?>
                </select>

                <label for="filter_status">
                    <?php echo $stateLabel; ?>
                </label>
                <select name="filter_status"
                    id="filter_status"
                    class="inputbox filter filter-submit"
                >
                    <option value="">
                        <?php echo $stateLabel; ?>
                    </option>
                    <?php echo Html::select(
                        'options',
                        $statusOpts,
                        'value',
                        'text',
                        $this->filters['status'],
                        true
                    ); ?>
                </select>

                <label for="filter_type">
                    <?php echo $typeLabel; ?>
                </label>
                <select name="filter_type"
                    id="filter_type"
                    class="inputbox filter filter-submit"
                >
                    <option value="">
                        <?php echo $typeLabel; ?>
                    </option>
                    <?php echo Html::select(
                        'options',
                        $typeOpts,
                        'value',
                        'text',
                        $this->filters['type']
                    ); ?>
                </select>

                <label for="filter_group">
                    <?php echo Lang::txt('COM_INSTALLER_VALUE_FOLDER_SELECT'); ?>
                </label>
                <select name="filter_group"
                    id="filter_group"
                    class="inputbox filter filter-submit"
                >
                    <option value="">
                        <?php echo $folderLabel; ?>
                    </option>
                    <?php echo Html::select(
                        'options',
                        $groupOpts,
                        'value',
                        'text',
                        $this->filters['group']
                    ); ?>
                </select>

            </div>
        </fieldset>


        <?php if (count($this->rows)) : ?>
        <table class="adminlist">
            <thead>
                <tr>
                    <th>
                        <?php $checkAll = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                        <input type="checkbox"
                            name="checkall-toggle"
                            id="checkall-toggle"
                            value=""
                            title="<?php echo $checkAll; ?>"
                            class="checkbox-toggle toggle-all"
                        />
                        <label for="checkall-toggle"
                            class="sr-only visually-hidden"
                        >
                            <?php echo $checkAll; ?>
                        </label>
                    </th>
                    <th class="nowrap">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_NAME',
                            'name',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="center">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_STATUS',
                            'status',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="priority-2">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_LOCATION',
                            'client_id',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="priority-3">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_TYPE',
                            'type',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="priority-4 center">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_FOLDER',
                            'folder',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                    <th class="priority-5">
                        <?php
                        $modifiedOn = Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_ON');
                        echo $modifiedOn;
                        ?>
                    </th>
                    <th class="priority-5">
                        <?php
                        $modifiedBy = Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_BY');
                        echo $modifiedBy;
                        ?>
                    </th>
                    <th class="priority-4">
                        <?php echo Html::grid(
                            'sort',
                            'COM_INSTALLER_CUSTOMEXTS_HEADING_ID',
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
                $ordering   = ($listOrder == 'ordering');
                $canChange  = User::authorise('core.edit.state', 'com_installer');

                $cls = $i % 2;
                ?>
                <tr class="row<?php echo $cls; ?>">
                    <td>
                        <?php echo Html::grid('id', $i, $item->get('extension_id')); ?>
                    </td>
                    <td>
                        <?php if ($canDo->get('core.edit')) { ?>
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=edit&id=' . $item->get('extension_id')
                            );
                            ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape(stripslashes($item->get('name'))); ?>
                        </a>
                        <?php } else { ?>
                        <span>
                            <?php echo $this->escape(stripslashes($item->get('name'))); ?>
                        </span>
                        <?php } ?>
                    </td>
                    <td class="center">
                        <?php if (!$item->get('alias')) : ?>
                            <strong>X</strong>
                        <?php else : ?>
                            <?php echo Html::grid('published', $item->enabled, $i, '', $canChange); ?>
                        <?php endif; ?>
                    </td>
                    <td class="priority-2 center">
                        <?php echo ($item->get('client_id') == 1) ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE'); ?>
                    </td>
                    <td class="priority-3 center">
                        <?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_TYPE_' . $item->get('type')); ?>
                    </td>
                    <td class="priority-4 center">
                        <?php echo ($item->get('folder') != '') ? $item->get('folder') : '&#160;'; ?>
                    </td>
                    <td class="priority-5 center">
                        <?php echo ($item->get('modified') != '') ? $item->get('modified') : '&#160;'; ?>
                    </td>
                    <td class="priority-5 center">
                        <?php
                        $modifier = User::getInstance($item->get('modified_by'));
                        $unknownTxt = Lang::txt('COM_INSTALLER_CUSTOMEXTS_UNKNOWN');
                        $modName = $modifier->get('name', $unknownTxt);
                        $modId = $item->get('modified_by');
                        echo $this->escape($modName . ' (' . $modId . ')');
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
