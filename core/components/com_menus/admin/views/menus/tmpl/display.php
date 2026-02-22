<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Menus\Helpers\Menus::getActions($this->filters['parent_id']);

Toolbar::title(Lang::txt('COM_MENUS_VIEW_MENUS_TITLE'), 'menumgr');

if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::divider();
    Toolbar::deleteList('COM_MENUS_MENU_CONFIRM_DELETE');
}

Toolbar::custom(
    'rebuild',
    'refresh.png',
    'refresh_f2.png',
    'JTOOLBAR_REBUILD',
    false
);
if ($canDo->get('core.admin')) {
    Toolbar::divider();
    Toolbar::preferences($this->option);
}
Toolbar::divider();
Toolbar::help('menus');

// Include the component HTML helpers.
Html::addIncludePath(Component::path($this->option) . '/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');

$uri = Request::current();
$return = base64_encode($uri);

$userId = User::get('id');
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>

<form action="<?php echo $formAction; ?>"
    method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th rowspan="2">
                    <?php $checkAllTitle = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <input type="checkbox" name="checkall-toggle" value=""
                        title="<?php echo $checkAllTitle; ?>"
                        class="checkbox-toggle toggle-all" />
                </th>
                <th rowspan="2">
                    <?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
                </th>
                <th colspan="3" class="priority-4">
                    <?php echo Lang::txt('COM_MENUS_HEADING_NUMBER_MENU_ITEMS'); ?>
                </th>
                <th rowspan="2">
                    <?php echo Lang::txt('COM_MENUS_HEADING_LINKED_MODULES'); ?>
                </th>
                <th class="nowrap priority-5" rowspan="2">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            <tr>
                <th class="priority-4">
                    <?php echo Lang::txt('COM_MENUS_HEADING_PUBLISHED_ITEMS'); ?>
                </th>
                <th class="priority-4">
                    <?php echo Lang::txt('COM_MENUS_HEADING_UNPUBLISHED_ITEMS'); ?>
                </th>
                <th class="priority-4">
                    <?php echo Lang::txt('COM_MENUS_HEADING_TRASHED_ITEMS'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->items->pagination; ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) :
            $canCreate = User::authorise('core.create', $this->option);
            $canEdit   = User::authorise('core.edit', $this->option);
            $canChange = User::authorise('core.edit.state', $this->option);
            $menutype = $item->get('menutype');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo Html::grid('id', $i, $item->get('id')); ?>
                </td>
                <td>
                    <?php
                    $itemsUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=items&menutype=' . $menutype
                    );
                    ?>
                    <a href="<?php echo $itemsUrl; ?> ">
                        <?php echo $this->escape($item->get('title')); ?>
                    </a>
                    <?php $typeLabel = Lang::txt('COM_MENUS_MENU_MENUTYPE_LABEL'); ?>
                    <p class="smallsub">(<span><?php echo $typeLabel; ?></span>
                        <?php if ($canEdit) : ?>
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=edit&id=' . $item->get('id')
                            );
                            $desc = $this->escape($item->get('description'));
                            $menutypeEsc = $this->escape($menutype);
                            echo '<a href="' . $editUrl
                                . ' title=' . $desc . '">'
                                . $menutypeEsc . '</a>';
                            ?>)
                        <?php else : ?>
                            <?php echo $this->escape($menutype); ?>)
                        <?php endif; ?>
                    </p>
                </td>
                <td class="priority-4 center btns">
                    <?php
                    $pubUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=items&menutype=' . $menutype
                        . '&filter_published=1'
                    );
                    ?>
                    <a href="<?php echo $pubUrl; ?>">
                        <?php echo $item->countPublishedItems(); ?>
                    </a>
                </td>
                <td class="priority-4 center btns">
                    <?php
                    $unpubUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=items&menutype=' . $menutype
                        . '&filter_published=0'
                    );
                    ?>
                    <a href="<?php echo $unpubUrl; ?>">
                        <?php echo $item->countUnpublishedItems(); ?>
                    </a>
                </td>
                <td class="priority-4 center btns">
                    <?php
                    $trashUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=items&menutype=' . $menutype
                        . '&filter_published=-2'
                    );
                    ?>
                    <a href="<?php echo $trashUrl; ?>">
                        <?php echo $item->countTrashedItems(); ?>
                    </a>
                </td>
                <td class="left">
                    <?php if (isset($this->modules[$menutype])) : ?>
                    <ul>
                        <?php foreach ($this->modules[$menutype] as &$module) : ?>
                        <li>
                            <?php if ($canEdit) : ?>
                                <?php
                                $modUrl = Route::url(
                                    'index.php?option=com_modules&task=edit'
                                    . '&id=' . $module->id
                                    . '&return=' . $return
                                );
                                $editTitle = Lang::txt('COM_MENUS_EDIT_MODULE_SETTINGS');
                                $modAccess = Lang::txt(
                                    'COM_MENUS_MODULE_ACCESS_POSITION',
                                    $this->escape($module->title),
                                    $this->escape($module->access_title),
                                    $this->escape($module->position)
                                );
                                $modRel = '{handler: \'iframe\', size: {x: 1024, y: 450},'
                                    . ' onClose: function() {window.location.reload()}}';
                                ?>
                                <a class="button"
                                    href="<?php echo $modUrl; ?>"
                                    rel="<?php echo $modRel; ?>"
                                    title="<?php echo $editTitle; ?>">
                                    <?php echo $modAccess; ?>
                                </a>
                            <?php else :?>
                                <?php
                                echo Lang::txt(
                                    'COM_MENUS_MODULE_ACCESS_POSITION',
                                    $this->escape($module->title),
                                    $this->escape($module->access_title),
                                    $this->escape($module->position)
                                );
                                ?>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php elseif ($this->modMenuId) : ?>
                        <?php
                        $addModUrl = Route::url(
                            'index.php?option=com_modules&task=add'
                            . '&eid=' . $this->modMenuId
                            . '&params[menutype]=' . $menutype
                        );
                        ?>
                        <a href="<?php echo $addModUrl; ?>">
                            <?php echo Lang::txt('COM_MENUS_ADD_MENU_MODULE'); ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="priority-5 center">
                    <?php echo $item->get('id'); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo Html::input('token'); ?>
</form>
