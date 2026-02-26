<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('citation');

Toolbar::title(Lang::txt('CITATIONS'), 'citation');
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_citations', 600, 800);
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('citations');

//set the escape callback
$this->setEscape("htmlentities");

$formAction = Route::url(
    'index.php?option=' . $this->option
);
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span6">
                <?php
                $searchVal = $this->escape($this->filters['search']);
                $searchPlaceholder = Lang::txt(
                    'COM_CITATIONS_FILTER_SEARCH_PLACEHOLDER'
                );
                ?>
                <label for="filter_search">
                    <?php echo Lang::txt('JSEARCH_FILTER'); ?>:
                </label>
                <input
                    type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPlaceholder; ?>"
                />

                <input
                    type="submit"
                    name="filter_submit"
                    id="filter_submit"
                    value="<?php echo Lang::txt('GO'); ?>"
                />
                <button type="button" class="filter-clear">
                    <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>
            <div class="col span6">
                <?php
                // Sort dropdown (disabled):
                // <label for="sort">Sort:</label>
                // <select name="sort" id="sort">
                //   <option value="created DESC">DATE</option>
                //   <option value="year">YEAR</option>
                //   <option value="type">TYPE</option>
                //   <option value="author ASC">AUTHORS</option>
                //   <option value="title ASC">TITLE</option>
                //   <option value="scope_id ASC">SCOPE_ID</option>
                // </select>
                ?>

                <label for="scope">
                    <?php echo Lang::txt('SCOPE'); ?>:
                </label>
                <?php
                $scopeFilter = $this->filters['scope'];
                $allSel = ($scopeFilter == 'all')
                    ? ' selected="selected"' : '';
                $hubSel = ($scopeFilter == 'hub')
                    ? ' selected="selected"' : '';
                $grpSel = ($scopeFilter == 'group')
                    ? ' selected="selected"' : '';
                $memSel = ($scopeFilter == 'member')
                    ? ' selected="selected"' : '';
                ?>
                <select
                    name="scope"
                    id="scope"
                    class="filter filter-submit"
                >
                    <option value="all"<?php echo $allSel; ?>>
                        <?php echo Lang::txt('- Scope -'); ?>
                    </option>
                    <option value="hub"<?php echo $hubSel; ?>>
                        <?php echo Lang::txt('HUB'); ?>
                    </option>
                    <option value="group"<?php echo $grpSel; ?>>
                        <?php echo Lang::txt('GROUP'); ?>
                    </option>
                    <option value="member"<?php echo $memSel; ?>>
                        <?php echo Lang::txt('MEMBER'); ?>
                    </option>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                    />
                    <label
                        for="checkall-toggle"
                        class="sr-only visually-hidden"
                    >
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    </label>
                </th>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sort = @$this->filters['sort'];
                ?>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'ID', 'id', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-2">
                    <?php echo Html::grid('sort', 'TYPE', 'type', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Lang::txt('TITLE'); ?> / <?php echo Lang::txt('AUTHORS'); ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo Html::grid('sort', 'PUBLISHED', 'published', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo Html::grid('sort', 'YEAR', 'year', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'AFFILIATED', 'affiliated', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'FUNDED_BY', 'fundedby', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'SCOPE', 'scope', $sortDir, $sort); ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo Html::grid('sort', 'SCOPE_ID', 'scope_id', $sortDir, $sort); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="10">
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
        $baseUrl = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller;
        $token = Session::getFormToken();
        foreach ($this->rows as $row) {
            if ($row->published == 1) :
                $cls = 'publish';
                $alt = Lang::txt('UNPUBLISH');
            elseif ($row->published == 0) :
                $cls = 'unpublish';
                $alt = Lang::txt('PUBLISH');
            elseif ($row->published == 2) :
                $cls = 'delete';
                $alt = Lang::txt('DELETED');
            endif;
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input
                        type="checkbox"
                        name="id[]"
                        id="cb<?php echo $row->id; ?>"
                        value="<?php echo $row->id; ?>"
                        class="checkbox-toggle"
                    />
                    <label
                        for="cb<?php echo $row->id; ?>"
                        class="sr-only visually-hidden"
                    >
                        <?php echo $row->id; ?>
                    </label>
                </td>
                <td class="priority-4">
                    <?php echo $row->get('id'); ?>
                </td>
                <td class="priority-2">
                    <?php
                        $type = $row->relatedType->get('type_title');
                        echo ($type) ? $type : Lang::txt('GENERIC');
                    ?>
                </td>
                <td>
                    <?php
                        $title = html_entity_decode($row->title);
                        $author = html_entity_decode($row->author ?? '');
                    if (function_exists('mbstring')) {
                        $title = (!preg_match('!\S!u', $title)) ? mbstring($title) : $title;
                        $author = (!preg_match('!\S!u', $author)) ? mbstring($author) : $author;
                    }
                    $editUrl = Route::url(
                        $baseUrl . '&task=edit&id=' . $row->id
                    );
                    ?>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape($title); ?>
                        </a>
                        <br />
                        <small><?php echo $this->escape($author); ?></small>
                    <?php } else { ?>
                        <span>
                            <?php echo $this->escape($title); ?></a><br />
                            <small><?php echo $this->escape($author); ?></small>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php
                        if ($row->published == 1) :
                            $pubUrl = Route::url(
                                $baseUrl . '&task=unpublish&id='
                                . $row->id . '&' . $token . '=1'
                            );
                        elseif ($row->published == 0) :
                            $pubUrl = Route::url(
                                $baseUrl . '&task=publish&id='
                                . $row->id . '&' . $token . '=1'
                            );
                        elseif ($row->published == 2) :
                            $pubUrl = Route::url(
                                $baseUrl . '&task=publish&id='
                                . $row->id . '&' . $token . '=1'
                            );
                        endif;
                        ?>
                        <a href="<?php echo $pubUrl; ?>">
                            <span class="state <?php echo $cls; ?>">
                                <span><?php echo $alt; ?></span>
                            </span>
                        </a>
                    <?php } else { ?>
                        <span class="state <?php echo $cls; ?>">
                            <span><?php echo $alt; ?></span>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-3">
                    <?php echo $this->escape($row->year); ?>
                </td>
                <td class="priority-4">
                    <?php
                    $affUrl = Route::url(
                        $baseUrl . '&task=affiliate&id='
                        . $row->id . '&' . $token . '=1'
                    );
                    ?>
                    <?php if ($row->affiliated == 1) : ?>
                        <a href="<?php echo $affUrl; ?>">
                            <span class="state publish">
                                <span><?php echo Lang::txt('NO'); ?></span>
                            </span>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $affUrl; ?>">
                            <span class="state unpublish">
                                <span><?php echo Lang::txt('YES'); ?></span>
                            </span>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="priority-4">
                    <?php
                    $fundUrl = Route::url(
                        $baseUrl . '&task=fund&id='
                        . $row->id . '&' . $token . '=1'
                    );
                    ?>
                    <?php if ($row->fundedby == 1) : ?>
                        <a href="<?php echo $fundUrl; ?>">
                            <span class="state publish">
                                <span><?php echo Lang::txt('NO'); ?></span>
                            </span>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $fundUrl; ?>">
                            <span class="state unpublish">
                                <span><?php echo Lang::txt('YES'); ?></span>
                            </span>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="priority-4">
                    <?php echo ($row->scope == '') ? Lang::txt('Hub') : $this->escape($row->scope); ?>
                </td>
                <td class="priority-4">
                    <?php echo ($row->scope_id == 0) ? Lang::txt('N/A') : $this->escape($row->scope_id); ?>
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
    <input type="hidden" name="boxchecked" value="0" />
    <input
        type="hidden"
        name="filter_order"
        value="<?php echo $this->escape($this->filters['sort']); ?>"
    />
    <input
        type="hidden"
        name="filter_order_Dir"
        value="<?php echo $this->escape($this->filters['sort_Dir']); ?>"
    />

    <?php echo Html::input('token'); ?>
</form>
