<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canAdmin = User::authorise('core.admin', 'com_content');
$canCreate = User::authorise('core.create', 'com_content');
$canEdit = User::authorise('core.edit', 'com_content');
$canChangeState = User::authorise('core.edit.state', 'com_content');
$canDelete = User::authorise('core.delete', 'com_content');

Html::behavior('multiselect');
Toolbar::title(Lang::txt('COM_CONTENT_ARTICLES_TITLE'), 'content');

if ($canCreate) {
    Toolbar::addNew();
}
if ($canEdit) {
    Toolbar::editList();
}
Toolbar::spacer();
if ($canChangeState) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
    Toolbar::archiveList();
    Toolbar::checkin();
}
if ($canDelete) {
    Toolbar::deleteList('', 'trash');
}
if ($canAdmin) {
    Toolbar::spacer();
    Toolbar::preferences($this->option, '550');
}
Toolbar::spacer();
Toolbar::help('articles');

Html::behavior('tooltip');

$this->js();

$userId    = User::get('id');
$listOrder = $this->filters['sort'];
$listDirn  = $this->filters['sort_Dir'];
$saveOrder = $listOrder == 'ordering';

$formAction = Route::url('index.php?option=com_content&view=articles');
$searchVal = $this->escape($this->filters['search']);
$searchPlaceholder = Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC');
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search">
                <?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?>
            </label>
            <input
                type="text"
                name="filter_search"
                id="filter_search"
                class="filter"
                value="<?php echo $searchVal; ?>"
                placeholder="<?php echo $searchPlaceholder; ?>"
            />

            <button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" class="btn filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <?php $selectPublished = Lang::txt('JOPTION_SELECT_PUBLISHED'); ?>
            <label for="filter_published"><?php echo $selectPublished; ?></label>
            <select
                name="filter_published"
                id="filter_published"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectPublished; ?></option>
                <?php
                echo Html::select(
                    'options',
                    Html::grid('publishedOptions'),
                    'value',
                    'text',
                    $this->filters['published'],
                    true
                );
                ?>
            </select>

            <?php $selectCategory = Lang::txt('JOPTION_SELECT_CATEGORY'); ?>
            <label for="filter_category_id"><?php echo $selectCategory; ?></label>
            <select
                name="filter_category_id"
                id="filter_category_id"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectCategory; ?></option>
                <?php
                echo Html::select(
                    'options',
                    Html::category('options', 'com_content'),
                    'value',
                    'text',
                    $this->filters['category_id']
                );
                ?>
            </select>

            <?php $selectLevels = Lang::txt('JOPTION_SELECT_MAX_LEVELS'); ?>
            <label for="filter_level"><?php echo $selectLevels; ?></label>
            <select
                name="filter_level"
                id="filter_level"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectLevels; ?></option>
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

            <?php $selectAccess = Lang::txt('JOPTION_SELECT_ACCESS'); ?>
            <label for="filter_access"><?php echo $selectAccess; ?></label>
            <select
                name="filter_access"
                id="filter_access"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectAccess; ?></option>
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

            <?php $selectAuthor = Lang::txt('JOPTION_SELECT_AUTHOR'); ?>
            <label for="filter_author_id"><?php echo $selectAuthor; ?></label>
            <select
                name="filter_author_id"
                id="filter_author_id"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectAuthor; ?></option>
                <?php
                echo Html::select(
                    'options',
                    $this->authors,
                    'value',
                    'text',
                    $this->filters['author_id']
                );
                ?>
            </select>

            <?php $selectLang = Lang::txt('JOPTION_SELECT_LANGUAGE'); ?>
            <label for="filter_language"><?php echo $selectLang; ?></label>
            <select
                name="filter_language"
                id="filter_language"
                class="filter filter-submit"
            >
                <option value=""><?php echo $selectLang; ?></option>
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

    <table class="adminlist">
        <thead>
            <tr>
                <?php $checkAll = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                <th>
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                        title="<?php echo $checkAll; ?>"
                    />
                    <label
                        for="checkall-toggle"
                        class="sr-only visually-hidden"
                    ><?php echo $checkAll; ?></label>
                </th>
                <th>
                    <?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo Html::grid('sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
                </th>
                <?php /*<th class="priority-4">
                    <?php echo Html::grid('sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
                </th>*/ ?>
                <th class="priority-2">
                    <?php echo Html::grid('sort', 'JCATEGORY', 'catid', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-3">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
                    <?php if ($saveOrder) :?>
                        <?php echo Html::grid('order', $this->items, 'filesave.png', 'saveorder'); ?>
                    <?php endif; ?>
                </th>
                <th class="priority-4">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-6">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_CREATED_BY', 'created_by', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-5">
                    <?php echo Html::grid('sort', 'JDATE', 'created', $listDirn, $listOrder); ?>
                </th>
                <!--
                [!] HUBZERO - (zooley) Removing hit counter as it can
                contribute to performance issues. Need a better way.
                <th>
                    <?php echo Html::grid('sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
                </th>
                -->
                <th class="priority-6">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th class="priority-5 nowrap">
                    <?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="10">
                    <?php echo $this->pagination; ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) :
            $item->max_ordering = 0; //??
            $ordering   = ($listOrder == 'ordering');
            $canCreate  = User::authorise('core.create', 'com_content.category.' . $item->catid);
            $canEdit    = User::authorise('core.edit', 'com_content.article.' . $item->id);
            $canCheckin = User::authorise('core.manage', 'com_checkin')
                || $item->checked_out == $userId
                || $item->checked_out == 0;
            $canEditOwn = User::authorise('core.edit.own', 'com_content.article.' . $item->id)
                && $item->created_by == $userId;
            $canChange  = User::authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo Html::grid('id', $i, $item->id); ?>
                </td>
                <td>
                    <?php if ($item->checked_out) : ?>
                        <?php
                        echo Html::grid(
                            'checkedout',
                            $i,
                            $item->editor->name,
                            $item->checked_out_time,
                            'articles.',
                            $canCheckin
                        );
                        ?>
                    <?php endif; ?>
                    <?php if ($canEdit || $canEditOwn) : ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&id=' . $item->id
                        );
                        ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape($item->title); ?></a>
                    <?php else : ?>
                        <?php echo $this->escape($item->title); ?>
                    <?php endif; ?>
                    <p class="smallsub">
                        <?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    </p>
                </td>
                <td class="center">
                    <?php
                    echo Html::grid(
                        'published',
                        $item->get('state'),
                        $i,
                        'articles.',
                        $canChange,
                        'cb',
                        $item->publish_up,
                        $item->publish_down
                    );
                    ?>
                </td>
                <?php /*<td class="priority-4 center">
                    <?php echo Html::contentadministrator('featured', $item->featured, $i, $canChange); ?>
                </td>*/ ?>
                <td class="priority-2 center">
                    <?php echo $this->escape($item->category->title); ?>
                </td>
                <td class="priority-3 order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                            <?php if ($listDirn == 'asc') : ?>
                                <?php
                                $prevMatch = ($item->catid == @$this->items[$i - 1]->catid);
                                $nextMatch = ($item->catid == @$this->items[$i + 1]->catid);
                                $upIcon = $this->pagination->orderUpIcon(
                                    $i,
                                    $prevMatch,
                                    'orderup',
                                    'JLIB_HTML_MOVE_UP',
                                    $ordering
                                );
                                $downIcon = $this->pagination->orderDownIcon(
                                    $i,
                                    $this->pagination->total,
                                    $nextMatch,
                                    'articles.orderdown',
                                    'JLIB_HTML_MOVE_DOWN',
                                    $ordering
                                );
                                ?>
                                <span><?php echo $upIcon; ?></span>
                                <span><?php echo $downIcon; ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <?php
                                $upIcon = $this->pagination->orderUpIcon(
                                    $i,
                                    $prevMatch,
                                    'orderdown',
                                    'JLIB_HTML_MOVE_UP',
                                    $ordering
                                );
                                $downIcon = $this->pagination->orderDownIcon(
                                    $i,
                                    $this->pagination->total,
                                    $nextMatch,
                                    'articles.orderup',
                                    'JLIB_HTML_MOVE_DOWN',
                                    $ordering
                                );
                                ?>
                                <span><?php echo $upIcon; ?></span>
                                <span><?php echo $downIcon; ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <?php
                        $catid = $item->get('catid', 0);
                        $itemId = $item->get('id', 0);
                        ?>
                        <input
                            type="text"
                            name="order[<?php echo $catid; ?>][<?php echo $itemId; ?>]"
                            size="5"
                            value="<?php echo $item->ordering; ?>"
                            <?php echo $disabled; ?>
                            class="text-area-order"
                        />
                    <?php else : ?>
                        <?php echo $item->ordering; ?>
                    <?php endif; ?>
                </td>
                <td class="priority-4 center">
                    <?php echo $this->escape($item->accessLevel->title); ?>
                </td>
                <td class="priority-6 center">
                    <?php
                    $unknown = Lang::txt('JUNKNOWN');
                    $authorName = $this->escape(
                        $item->author->get('name', $unknown)
                    );
                    ?>
                    <?php if ($item->created_by_alias) : ?>
                        <?php echo $authorName; ?>
                        <p class="smallsub">
                            <?php
                            $aliasEsc = $this->escape(
                                $item->created_by_alias
                            );
                            echo Lang::txt(
                                'JGLOBAL_LIST_ALIAS',
                                $aliasEsc
                            );
                            ?>
                        </p>
                    <?php else : ?>
                        <?php
                        echo $item->created_by
                            ? $authorName
                            : $unknown;
                        ?>
                    <?php endif; ?>
                </td>
                <td class="priority-5 center nowrap">
                    <?php echo Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')); ?>
                </td>
                <!--
                [!] HUBZERO - (zooley) Removing hit counter as it
                can contribute to performance issues.
                <td class="center">
                    <?php echo (int) $item->hits; ?>
                </td>
                -->
                <td class="priority-6 center">
                    <?php if ($item->language == '*') :?>
                        <?php echo Lang::txt('JALL', 'language'); ?>
                    <?php else :?>
                        <?php
                        echo $item->language_title
                            ? $this->escape($item->language_title)
                            : Lang::txt('JUNDEFINED');
                        ?>
                    <?php endif;?>
                </td>
                <td class="priority-5 center">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php //Load the batch processing form. ?>
    <?php
    $canBatch = User::authorise('core.create', 'com_content')
        && User::authorise('core.edit', 'com_content')
        && User::authorise('core.edit.state', 'com_content');
    ?>
    <?php if ($canBatch) : ?>
        <?php echo $this->loadTemplate('batch'); ?>
    <?php endif;?>

    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo Html::input('token'); ?>
</form>
