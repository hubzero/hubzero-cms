<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Menus\Helpers\Menus::getActions();

Toolbar::title(Lang::txt('COM_MENUS_VIEW_ITEMS_TITLE'), 'menumgr');

if ($canDo->get('core.create')) {
    Toolbar::addNew('item.add');
}
if ($canDo->get('core.edit')) {
    Toolbar::editList('item.edit');
}
if ($canDo->get('core.edit.state')) {
    Toolbar::divider();
    Toolbar::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
    Toolbar::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
}
if (User::authorise('core.admin')) {
    Toolbar::divider();
    Toolbar::checkin('items.checkin', 'JTOOLBAR_CHECKIN', true);
}

if ($this->filters['published'] == -2 && $canDo->get('core.delete')) {
    Toolbar::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
} elseif ($canDo->get('core.edit.state')) {
    Toolbar::trash('items.trash');
}

if ($canDo->get('core.edit.state')) {
    Toolbar::makeDefault('items.setDefault', 'COM_MENUS_TOOLBAR_SET_HOME');
    Toolbar::divider();
}
if (User::authorise('core.admin')) {
    Toolbar::custom(
        'items.rebuild',
        'refresh.png',
        'refresh_f2.png',
        'JToolbar_Rebuild',
        false
    );
    Toolbar::divider();
}
Toolbar::help('items');

// Include the component HTML helpers.
Html::addIncludePath(Component::path($this->option) . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$this->js();

$userId    = User::get('id');
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$ordering  = ($listOrder == 'lft');
$canOrder  = User::authorise('core.edit.state', $this->option);
$saveOrder = ($listOrder == 'lft' && $listDirn == 'asc');

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$filterLabel = Lang::txt('JSEARCH_FILTER_LABEL');
$searchPlaceholder = Lang::txt('COM_MENUS_ITEMS_SEARCH_FILTER');
?>
<?php //Set up the filter bar. ?>
<form action="<?php echo $formAction; ?>"
    method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search">
                <?php echo $filterLabel; ?>
            </label>
            <input type="text" name="filter_search" id="filter_search"
                class="filter"
                value="<?php echo $this->escape($this->filters['search']); ?>"
                placeholder="<?php echo $searchPlaceholder; ?>" />
            <button type="submit">
                <?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>
            </button>
            <button type="button" class="filter-clear">
                <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
            </button>
        </div>
        <div class="filter-select fltrt">
            <select name="menutype" class="inputbox filter filter-submit">
                <?php
                echo Html::select(
                    'options',
                    Html::menu('menus'),
                    'value',
                    'text',
                    $this->filters['menutype']
                );
                ?>
            </select>

            <select name="filter_level" class="inputbox filter filter-submit">
                <option value="">
                    <?php echo Lang::txt('COM_MENUS_OPTION_SELECT_LEVEL'); ?>
                </option>
                <?php
                echo Html::select(
                    'options',
                    $this->f_levels,
                    'value',
                    'text',
                    $this->filters['level']
                );
                ?>
            </select>

            <select name="filter_published" class="inputbox filter filter-submit">
                <option value="">
                    <?php echo Lang::txt('JOPTION_SELECT_PUBLISHED'); ?>
                </option>
                <?php
                echo Html::select(
                    'options',
                    Html::grid('publishedOptions', array('archived' => false)),
                    'value',
                    'text',
                    $this->filters['published'],
                    true
                );
                ?>
            </select>

            <select name="filter_access" class="inputbox filter filter-submit">
                <option value="">
                    <?php echo Lang::txt('JOPTION_SELECT_ACCESS'); ?>
                </option>
                <?php
                echo Html::select(
                    'options',
                    Html::access('assetgroups'),
                    'value',
                    'text',
                    $this->filters['access']
                );
                ?>
            </select>

            <select name="filter_language" class="inputbox filter filter-submit">
                <option value="">
                    <?php echo Lang::txt('JOPTION_SELECT_LANGUAGE'); ?>
                </option>
                <?php
                echo Html::select(
                    'options',
                    Html::contentlanguage('existing', true, true),
                    'value',
                    'text',
                    $this->filters['language']
                );
                ?>
            </select>
        </div>
    </fieldset>

<?php //Set up the grid heading. ?>
    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <?php $checkAllTitle = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <input type="checkbox" name="checkall-toggle" value=""
                        title="<?php echo $checkAllTitle; ?>"
                        class="checkbox-toggle toggle-all" />
                </th>
                <th class="title">
                    <?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-3">
                    <?php echo Html::grid('sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'lft', $listDirn, $listOrder); ?>
                    <?php if ($canOrder && $saveOrder) :?>
                        <?php echo Html::grid('order', $this->rows, 'filesave.png', 'items.saveorder'); ?>
                    <?php endif; ?>
                </th>
                <th class="priority-4">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-5">
                    <?php echo Lang::txt('JGRID_HEADING_MENU_ITEM_TYPE'); ?>
                </th>
                <th class="priority-2">
                    <?php echo Html::grid('sort', 'COM_MENUS_HEADING_HOME', 'home', $listDirn, $listOrder); ?>
                </th>
                <?php
                $assoc = App::has('menu_associations')
                    ? App::get('menu_associations') : 0;
                if ($assoc) :
                    ?>
                    <th>
                        <?php
                        echo Html::grid(
                            'sort',
                            'COM_MENUS_HEADING_ASSOCIATION',
                            'association',
                            $listDirn,
                            $listOrder
                        );
                        ?>
                    </th>
                <?php endif; ?>
                <th class="priority-6">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap priority-6">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->rows->pagination; ?>
                </td>
            </tr>
        </tfoot>
        <?php // Grid layout ?>
        <tbody>
        <?php
        $originalOrders = array();
        $i = 0;
        foreach ($this->rows as $item) :
            $orderkey = array_search(
                $item->get('id'),
                $this->ordering[$item->get('parent_id')]
            );
            $canCreate  = User::authorise('core.create', $this->option);
            $canEdit    = User::authorise('core.edit', $this->option);
            $canCheckin = User::authorise('core.manage', 'com_checkin')
                || $item->get('checked_out') == User::get('id')
                || $item->get('checked_out') == 0;
            $canChange  = User::authorise('core.edit.state', $this->option)
                && $canCheckin;
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo Html::grid('id', $i, $item->get('id')); ?>
                </td>
                <td>
                    <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->get('level') - 1) ?>
                    <?php if ($item->get('checked_out')) : ?>
                        <?php
                        echo Html::grid(
                            'checkedout',
                            $i,
                            $item->get('editor'),
                            $item->get('checked_out_time'),
                            'items.',
                            $canCheckin
                        );
                        ?>
                    <?php endif; ?>
                    <?php if ($canEdit) : ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&cid[]=' . (int) $item->get('id')
                        );
                        ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape($item->get('title')); ?>
                        </a>
                    <?php else : ?>
                        <?php echo $this->escape($item->get('title')); ?>
                    <?php endif; ?>
                    <?php $itemPath = $this->escape($item->get('path')); ?>
                    <p class="smallsub" title="<?php echo $itemPath; ?>">
                        <?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item->get('level') - 1) ?>
                        <?php if ($item->get('type') != 'url') : ?>
                            <?php if (empty($item->get('note'))) : ?>
                                <?php
                                $aliasText = $this->escape($item->get('alias'));
                                echo Lang::txt('JGLOBAL_LIST_ALIAS', $aliasText);
                                ?>
                            <?php else : ?>
                                <?php
                                $aliasText = $this->escape($item->get('alias'));
                                $noteText = $this->escape($item->get('note'));
                                echo Lang::txt(
                                    'JGLOBAL_LIST_ALIAS_NOTE',
                                    $aliasText,
                                    $noteText
                                );
                                ?>
                            <?php endif; ?>
                        <?php elseif ($item->get('type') == 'url' && $item->get('note')) : ?>
                            <?php
                            $noteText = $this->escape($item->get('note'));
                            echo Lang::txt('JGLOBAL_LIST_NOTE', $noteText);
                            ?>
                        <?php endif; ?>
                    </p>
                </td>
                <td class="center priority-3">
                    <?php echo Html::menus('state', $item->get('published'), $i, $canChange, 'cb'); ?>
                </td>
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) : ?>
                            <?php
                            $parentId = $item->parent_id;
                            $hasUp = isset($this->ordering[$parentId][$orderkey - 1]);
                            $hasDown = isset($this->ordering[$parentId][$orderkey + 1]);
                            ?>
                            <span>
                                <?php echo $this->rows->pagination->orderUpIcon(
                                    $i,
                                    $hasUp,
                                    'items.orderup',
                                    'JLIB_HTML_MOVE_UP',
                                    $ordering
                                ); ?>
                            </span>
                            <span>
                                <?php echo $this->rows->pagination->orderDownIcon(
                                    $i,
                                    $this->rows->pagination->total,
                                    $hasDown,
                                    'items.orderdown',
                                    'JLIB_HTML_MOVE_DOWN',
                                    $ordering
                                ); ?>
                            </span>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5"
                            value="<?php echo $orderkey + 1; ?>"
                            <?php echo $disabled ?>
                            class="text-area-order" />
                        <?php $originalOrders[] = $orderkey + 1; ?>
                    <?php else : ?>
                        <?php echo $orderkey + 1;?>
                    <?php endif; ?>
                </td>
                <td class="center priority-4">
                    <?php echo $this->escape($item->get('access_level')); ?>
                </td>
                <td class="nowrap priority-5">
                    <?php
                    $typeDesc = $item->get('item_type_desc');
                    $typeTitle = $typeDesc
                        ? htmlspecialchars(
                            $this->escape($typeDesc),
                            ENT_COMPAT,
                            'UTF-8'
                        )
                        : '';
                    ?>
                    <span title="<?php echo $typeTitle; ?>">
                        <?php echo $this->escape($item->get('item_type')); ?>
                    </span>
                </td>
                <td class="center priority-2">
                    <?php if ($item->get('type') == 'component') : ?>
                        <?php if ($item->get('language') == '*' || $item->get('home') == '0') : ?>
                            <?php
                            $isDefault = ($item->get('language') != '*'
                                || !$item->get('home')) && $canChange;
                            echo Html::grid(
                                'isdefault',
                                $item->get('home'),
                                $i,
                                'items.',
                                $isDefault
                            );
                            ?>
                        <?php elseif ($canChange) : ?>
                            <?php
                            $unsetUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=unsetDefault&cid[]=' . $item->get('id')
                                . '&' . Session::getFormToken() . '=1'
                            );
                            $langTitle = $item->get('language_title');
                            $imgPath = 'mod_languages/' . $item->get('image') . '.gif';
                            $unsetTitle = Lang::txt(
                                'COM_MENUS_GRID_UNSET_LANGUAGE',
                                $langTitle
                            );
                            ?>
                            <a href="<?php echo $unsetUrl; ?>">
                                <?php
                                echo Html::asset(
                                    'image',
                                    $imgPath,
                                    $langTitle,
                                    array('title' => $unsetTitle),
                                    true
                                );
                                ?>
                            </a>
                        <?php else : ?>
                            <?php
                            $langTitle = $item->get('language_title');
                            $imgPath = 'mod_languages/'
                                . $item->get('image') . '.gif';
                            echo Html::asset('image', $imgPath, $langTitle, array('title' => $langTitle), true);
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <?php
                $assoc = App::has('menu_associations')
                    ? App::get('menu_associations') : 0;
                if ($assoc) :
                    ?>
                <td class="center">
                    <?php if ($item->get('association')) : ?>
                        <?php echo Html::menus('association', $item->get('id')); ?>
                    <?php endif;?>
                </td>
                <?php endif;?>
                <td class="center priority-6">
                    <?php if ($item->get('language') == '') : ?>
                        <?php echo Lang::txt('JDEFAULT'); ?>
                    <?php elseif ($item->get('language') == '*') : ?>
                        <?php echo Lang::txt('JALL', 'language'); ?>
                    <?php else :?>
                        <?php
                        $langTitleText = $item->get('language_title');
                        echo $langTitleText
                            ? $this->escape($langTitleText)
                            : Lang::txt('JUNDEFINED');
                        ?>
                    <?php endif;?>
                </td>
                <td class="center priority-6">
                    <?php
                    $lftRgt = sprintf(
                        '%d-%d',
                        $item->get('lft'),
                        $item->get('rgt')
                    );
                    ?>
                    <span title="<?php echo $lftRgt; ?>">
                        <?php echo (int) $item->get('id'); ?>
                    </span>
                </td>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>
    <?php //Load the batch processing form if user is allowed ?>
    <?php
    $canBatch = User::authorise('core.create', 'com_menus')
        && User::authorise('core.edit', 'com_menus')
        && User::authorise('core.edit.state', 'com_menus');
    if ($canBatch) :
        ?>
        <?php echo $this->loadTemplate('batch'); ?>
    <?php endif; ?>

    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <input type="hidden" name="original_order_values"
        value="<?php echo implode(',', $originalOrders); ?>" />
    <?php echo Html::input('token'); ?>
</form>
