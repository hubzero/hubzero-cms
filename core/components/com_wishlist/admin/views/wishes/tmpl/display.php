<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('wish');

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_WISHES'), 'wishlist');
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::spacer();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_WISHLIST_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('wishes');

$this->css();

$formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$sortId = Html::grid('sort', 'COM_WISHLIST_WISH_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']);
$sortSubject = Html::grid(
    'sort',
    'COM_WISHLIST_TITLE',
    'subject',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortWishlist = Html::grid(
    'sort',
    'COM_WISHLIST_WISHLIST_ID',
    'wishlist',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortProposedBy = Html::grid(
    'sort',
    'COM_WISHLIST_PROPOSED_BY',
    'proposed_by',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortProposed = Html::grid(
    'sort',
    'COM_WISHLIST_PROPOSED',
    'proposed',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortStatus = Html::grid(
    'sort',
    'COM_WISHLIST_STATUS',
    'status',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortAccess = Html::grid(
    'sort',
    'COM_WISHLIST_ACCESS',
    'private',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortComments = Html::grid(
    'sort',
    'COM_WISHLIST_COMMENTS',
    'numreplies',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
?>

<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span6">
                <label for="filter_search"><?php echo Lang::txt('COM_WISHLIST_SEARCH'); ?>:</label>
                <?php $searchVal = $this->escape($this->filters['search']); ?>
                <?php $searchPh = Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>
                <input type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchPh; ?>" />
                <input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_GO'); ?>" />
                <button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <div class="col span6">
                <label for="filter-status"><?php echo Lang::txt('COM_WISHLIST_FILTER_STATUS'); ?>:</label>
                <select name="status" id="filter-status" class="filter filter-submit">
                    <?php $fst = $this->filters['status']; ?>
                    <option value="all"<?php echo ($fst == 'all') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_ALL'); ?>
                    </option>
                    <option value="granted"<?php echo ($fst == 'granted') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_GRANTED'); ?>
                    </option>
                    <option value="open"<?php echo ($fst == 'open') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_OPEN'); ?>
                    </option>
                    <option value="accepted"<?php echo ($fst == 'accepted') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_ACCEPTED'); ?>
                    </option>
                    <option value="pending"<?php echo ($fst == 'pending') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_PENDING'); ?>
                    </option>
                    <option value="rejected"<?php echo ($fst == 'rejected') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_REJECTED'); ?>
                    </option>
                    <option value="withdrawn"<?php echo ($fst == 'withdrawn') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_WITHDRAWN'); ?>
                    </option>
                    <option value="deleted"<?php echo ($fst == 'deleted') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_DELETED'); ?>
                    </option>
                    <option value="useraccepted"<?php echo ($fst == 'useraccepted') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_USER_ACCEPTED'); ?>
                    </option>
                    <option value="private"<?php echo ($fst == 'private') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_PRIVATE'); ?>
                    </option>
                    <option value="public"<?php echo ($fst == 'public') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_PUBLIC'); ?>
                    </option>
                    <option value="assigned"<?php echo ($fst == 'assigned') ? ' selected="selected"' : ''; ?>>
                        <?php echo Lang::txt('COM_WISHLIST_STATE_ASSIGNED'); ?>
                    </option>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <?php if ($this->wishlist->id) { ?>
                <tr>
                    <th colspan="<?php echo (!$this->wishlist->id) ? 9 : 8; ?>">
                        (<?php echo $this->escape(stripslashes($this->wishlist->category)); ?>)
                        &nbsp; <?php echo $this->escape(stripslashes($this->wishlist->title)); ?>
                    </th>
                </tr>
            <?php } ?>
            <tr>
                <th scope="col">
                    <input type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle" class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    </label>
                </th>
                <th scope="col" class="priority-5"><?php echo $sortId; ?></th>
                <th scope="col"><?php echo $sortSubject; ?></th>
                <?php if (!$this->wishlist->id) { ?>
                    <th scope="col"><?php echo $sortWishlist; ?></th>
                <?php } ?>
                <th scope="col" class="priority-4"><?php echo $sortProposedBy; ?></th>
                <th scope="col" class="priority-5"><?php echo $sortProposed; ?></th>
                <th scope="col"><?php echo $sortStatus; ?></th>
                <th scope="col" class="priority-3"><?php echo $sortAccess; ?></th>
                <th scope="col" class="priority-2"><?php echo $sortComments; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="<?php echo (!$this->wishlist->id) ? 9 : 8; ?>"><?php
                // Initiate paging
                echo $this->rows->pagination;
                ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            switch ($row->status) {
                case 1:
                    $class = 'granted';
                    $task = 'pending';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_GRANTED');
                    break;
                case 2:
                    $class = 'trashed';
                    $task = 'grant';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_DELETED');
                    break;
                case 3:
                    $class = 'rejected';
                    $task = 'pending';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_REJECTED');
                    break;
                case 4:
                    $class = 'withdrawn';
                    $task = 'pending';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
                    break;
                case 6:
                    $class = 'accepted';
                    $task = 'grant';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
                    break;
                case 7:
                    $class = 'flagged';
                    $task = 'pending';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
                    break;
                case 0:
                default:
                    $class = 'pending';
                    $task = 'grant';
                    $alt = Lang::txt('COM_WISHLIST_STATUS_PENDING');
                    break;
            }

            if ($row->private) {
                $color_access = 'access private';
                $task_access = 'accesspublic';
                $groupname = 'Private';
            } else {
                $color_access = 'access public';
                $task_access = 'accessregistered';
                $groupname = 'Public';
            }

            $token = Session::getFormToken();
            $editUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=edit&id=' . $row->id
            );
            $taskUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=' . $task
                . '&id=' . $row->id
                . '&' . $token . '=1'
            );
            $accessUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=' . $task_access
                . '&id=' . $row->id
                . '&' . $token . '=1'
            );
            $commentsUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=comments&wish=' . $row->id
            );
            $setTaskTxt = Lang::txt('COM_WISHLIST_SET_TASK', $task);
            $changeAccessTxt = Lang::txt('COM_WISHLIST_CHANGE_ACCESS');
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $row->id; ?>"
                        class="checkbox-toggle" />
                    <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden">
                        <?php echo $row->id; ?>
                    </label>
                </td>
                <td class="priority-5">
                    <?php echo $row->id; ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <a href="<?php echo $editUrl; ?>">
                            <span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
                        </a>
                    <?php } else { ?>
                        <span>
                            <span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
                        </span>
                    <?php } ?>
                </td>
                <?php if (!$this->wishlist->id) { ?>
                    <td>
                        <?php echo $row->wishlist; ?>
                    </td>
                <?php } ?>
                <td class="priority-4">
                    <?php echo $this->escape($row->proposer->get('name', Lang::txt('(unknown)'))); ?>
                </td>
                <td class="priority-5">
                    <time datetime="<?php echo $row->proposed; ?>"><?php echo $row->proposed; ?></time>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <a class="state <?php echo $class; ?>"
                            href="<?php echo $taskUrl; ?>"
                            title="<?php echo $setTaskTxt; ?>">
                            <span><?php echo $alt; ?></span>
                        </a>
                    <?php } else { ?>
                        <span class="state <?php echo $class; ?>">
                            <span><?php echo $alt; ?></span>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-3">
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <a href="<?php echo $accessUrl; ?>"
                            class="<?php echo $color_access; ?>"
                            title="<?php echo $changeAccessTxt; ?>">
                            <?php echo $groupname; ?>
                        </a>
                    <?php } else { ?>
                        <span class="<?php echo $color_access; ?>">
                            <?php echo $groupname; ?>
                        </span>
                    <?php } ?>
                </td>
                <td class="priority-2">
                    <a class="glyph comment" href="<?php echo $commentsUrl; ?>">
                        <span><?php echo $this->escape($row->comments()->total()); ?></span>
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

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
    <input type="hidden" name="wishlist" value="<?php echo $this->filters['wishlist']; ?>" />
    <input type="hidden" name="cid" value="<?php echo $this->filters['wishlist']; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden"
        name="filter_order_Dir"
        value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
