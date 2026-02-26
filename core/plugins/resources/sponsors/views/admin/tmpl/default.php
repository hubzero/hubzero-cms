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

Toolbar::addNew();
Toolbar::deleteList();

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=manage&plugin=sponsors'
);
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm">

    <table class="adminlist">
        <caption><?php echo Lang::txt('PLG_RESOURCES_SPONSORS'); ?></caption>
        <thead>
            <tr>
                <th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sortCol = @$this->filters['sort'];
                ?>
                <th scope="col"><?php
                    echo Html::grid(
                        'sort',
                        'PLG_RESOURCES_SPONSORS_COL_ID',
                        'id',
                        $sortDir,
                        $sortCol
                    );
                    ?></th>
                <th scope="col"><?php
                    echo Html::grid(
                        'sort',
                        'PLG_RESOURCES_SPONSORS_COL_TITLE',
                        'title',
                        $sortDir,
                        $sortCol
                    );
                    ?></th>
                <th scope="col"><?php
                    echo Html::grid(
                        'sort',
                        'PLG_RESOURCES_SPONSORS_COL_ALIAS',
                        'alias',
                        $sortDir,
                        $sortCol
                    );
                    ?></th>
                <th scope="col"><?php
                    echo Html::grid(
                        'sort',
                        'PLG_RESOURCES_SPONSORS_COL_STATE',
                        'state',
                        $sortDir,
                        $sortCol
                    );
                    ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5"><?php
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
            switch ($row->state) {
                case '2':
                    $task = 'publish';
                    $alt = Lang::txt('JTRASHED');
                    $cls = 'trashed';
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
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $row->id; ?>"
                        class="checkbox-toggle"/>
                </td>
                <td>
                    <?php echo $row->id; ?>
                </td>
                <?php
                $editUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=manage&plugin=sponsors'
                    . '&action=edit&id=' . $row->id
                );
                ?>
                <td>
                    <a href="<?php echo $editUrl; ?>">
                        <?php echo $this->escape($row->title); ?>
                    </a>
                </td>
                <td>
                    <?php echo $this->escape($row->alias); ?>
                </td>
                <?php
                $stateUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=manage&plugin=sponsors'
                    . '&action=' . $task
                    . '&id=' . $row->id
                    . '&' . Session::getFormToken() . '=1'
                );
                $stateTitle = Lang::txt(
                    'PLG_RESOURCES_SPONSORS_SET_TO',
                    $task
                );
                ?>
                <td>
                    <a class="state <?php echo $cls; ?>"
                        href="<?php echo $stateUrl; ?>"
                        title="<?php echo $stateTitle; ?>">
                        <span><?php echo $alt; ?></span>
                    </a>
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
    <input type="hidden" name="task" value="manage" />
    <input type="hidden" name="plugin" value="sponsors" />
    <input type="hidden" name="action" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="sort_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>