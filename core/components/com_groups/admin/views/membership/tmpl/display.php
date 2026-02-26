<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups.png');

Toolbar::appendButton('Popup', 'new', 'COM_GROUPS_NEW', Route::url('index.php?option=' . $this->
    option . '&controller=' . $this->controller . '&tmpl=component&task=new&gid=' . $this->filters['gid']), 570, 170);

Toolbar::appendButton(
    'Link',
    'unblock',
    'COM_GROUPS_ROLE_ASSIGN',
    'index.php?option=' . $this->option . '&controller=roles&tmpl=component&task=assign&gid=' . $this->filters['gid'],
    400,
    400
);

Toolbar::spacer();
switch ($this->filters['status']) {
    case 'invitee':
        //if ($canDo->get('core.edit'))
        //{
            //Toolbar::custom('accept', 'publish', Lang::txt('Accept'), Lang::txt('Accept'), false, false);
        //}
        if ($canDo->get('core.delete')) {
            Toolbar::custom(
                'uninvite',
                'unpublish',
                'COM_GROUPS_MEMBER_UNINVITE',
                'COM_GROUPS_MEMBER_UNINVITE',
                false,
                false
            );
        }
        break;
    case 'applicant':
        if ($canDo->get('core.edit')) {
            Toolbar::custom(
                'approve',
                'publish',
                'COM_GROUPS_MEMBER_APPROVE',
                'COM_GROUPS_MEMBER_APPROVE',
                false,
                false
            );
        }
        if ($canDo->get('core.delete')) {
            Toolbar::custom('deny', 'unpublish', 'COM_GROUPS_MEMBER_DENY', 'COM_GROUPS_MEMBER_DENY', false, false);
        }
        break;
    default:
        if ($canDo->get('core.edit')) {
            Toolbar::custom(
                'promote',
                'promote',
                'COM_GROUPS_MEMBER_PROMOTE',
                'COM_GROUPS_MEMBER_PROMOTE',
                false,
                false
            );
            Toolbar::custom('demote', 'demote', 'COM_GROUPS_MEMBER_DEMOTE', 'COM_GROUPS_MEMBER_DEMOTE', false, false);
        }
        if ($canDo->get('core.delete')) {
            Toolbar::deleteList('COM_GROUPS_MEMBER_DELETE', 'delete');
        }
        break;
}
Toolbar::spacer();
Toolbar::help('membership');

$database = App::get('db');

Html::behavior('tooltip');

$this->css()
    ->js();
?>

<?php $url = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $url; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span8">
                <label for="filter_search"><?php echo Lang::txt('COM_GROUPS_SEARCH'); ?>:</label>
                <?php $v1 = $this->escape($this->filters['search']); ?>
                <?php $v0 = Lang::txt('COM_GROUPS_SEARCH'); ?>
                <input
                    type="text"
                    name="search"
                    id="filter_search"
                    ckass="filter"
                    value="<?php echo $v1; ?>" placeholder="<?php echo $v0; ?>" />

                <label for="filter-status"><?php echo Lang::txt('COM_GROUPS_MEMBER_STATUS'); ?>:</label>
                <select name="status" id="filter-status" class="filter filter-submit">
                    <?php $val1 = ($this->filters['status'] == '') ? ' selected="selected"' : ''; ?>
                    <?php $val = Lang::txt('COM_GROUPS_MEMBER_STATUS'); ?>
                    <option value=""<?php echo $val1; ?>><?php echo $val; ?></option>
                    <!--
                    <option value="member"<?php //echo ($this->filters['status'] == 'member')
                        //? ' selected="selected"' : ''; ?>>Member</option>
                    -->
                    <?php $val = ($this->filters['status'] == 'manager') ? ' selected="selected"' : ''; ?>
                    <option value="manager"<?php echo $val; ?>><?php echo Lang::txt('Manager'); ?></option>
                    <?php $val = ($this->filters['status'] == 'applicant') ? ' selected="selected"' : ''; ?>
                    <option value="applicant"<?php echo $val; ?>><?php echo Lang::txt('Applicant'); ?></option>
                    <?php $val = ($this->filters['status'] == 'invitee') ? ' selected="selected"' : ''; ?>
                    <option value="invitee"<?php echo $val; ?>><?php echo Lang::txt('Invitee'); ?></option>
                </select>

                <input type="submit" value="<?php echo Lang::txt('COM_GROUPS_GO'); ?>" />
            </div>
            <div class="col span4">
                <?php
                $rolesUrl = Route::url(
                    'index.php?option=com_groups&controller=roles' .
                    '&tmpl=component&gid=' . $this->filters['gid']
                );
                $rolesRel = '{size: {width: 570, height: 170}, onClose: function() {}}';
                ?>
                <a
                    class="button modal"
                    href="<?php echo $rolesUrl; ?>"
                    rel="<?php echo $rolesRel; ?>">
                    <span class="icon-32-new"><?php echo Lang::txt('Roles'); ?></span>
                </a>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <?php $v3 = Route::url('index.php?option=' . $this->option); ?>
                <?php $v2 = Lang::txt('COM_GROUPS'); ?>
                <?php $v1 = $this->escape(stripslashes($this->group->get('cn'))); ?>
                <?php $v0 = $this->escape(stripslashes($this->group->get('description'))); ?>
                <th colspan="8"><a href="<?php echo $v3; ?>"><?php echo $v2; ?></a> >
                    (<?php echo $v1; ?>) <?php echo $v0; ?></th>
            </tr>
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
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_USERID',
                    'uidNumber',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col" class="priority-4"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_NAME',
                    'name',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_USERNAME',
                    'username',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col" class="priority-3"><?php echo $val; ?></th>
                <?php $val = Html::grid(
                    'sort',
                    'COM_GROUPS_EMAIL',
                    'email',
                    @$this->filters['sort_Dir'],
                    @$this->filters['sort']
                ); ?>
                <th scope="col" class="priority-5"><?php echo $val; ?></th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MEMBER_STATUS'); ?></th>
                <th scope="col" colspan="2"><?php echo Lang::txt('COM_GROUPS_MEMBER_ACTION'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8"><?php
                echo $this->pagination(
                    $this->total,
                    $this->filters['start'],
                    $this->filters['limit']
                );
                ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            if (isset($row->username)) {
                $reason = new \Components\Groups\Tables\Reason($database);
                $reason->loadReason($row->username, $this->filters['gidNumber']);
                $reasonforjoin = '';
                if ($reason) {
                    $reasonforjoin = stripslashes($reason->reason == null ? '' : $reason->reason);
                }
            }

            $status = $row->role;
            if (in_array($row->uidNumber, $this->group->get('managers'))) {
                $status = 'manager';
            }

            $roles = \Components\Groups\Helpers\Permissions::getGroupMemberRoles($row->uidNumber, $this->group->
                get('gidNumber'));
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php $val = $i;?>" value="<?php echo (isset($row->uidNumber)) ? $row->uidNumber : $row->email; ?>
                    <input type="checkbox" name="id[]" id="cb<?php echo $val; ?>" class="checkbox-toggle" />
                    <?php $v0 = (isset($row->uidNumber)) ? $row->uidNumber : $row->email; ?>
                    <?php $val = $i;?>" class="sr-only visually-hidden"><?php echo $v0; ?>
                    <label for="cb<?php echo $val; ?></label>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape($row->uidNumber); ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit') && isset($row->username)) : ?>
                        <?php
                        $url = Route::url(
                            'index.php?option=com_members&controller=members&task=edit&id=' .
                            $row->uidNumber
                        );
                        ?>
                        <a href="<?php echo $url; ?>">
                            <?php echo $this->escape(stripslashes($row->name)); ?>
                        </a>
                    <?php else : ?>
                        <span>
                            <?php echo $this->escape(stripslashes($row->name)); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($roles) : ?>
                        <br />
                        <span class="roles">
                            <?php
                            //echo Lang::txt('COM_GROUPS_ROLES') . ': ';
                            $r = array();
                            foreach ($roles as $role) :
                                $r[] = '<span class="role">' .
                                    $role['name'] .
                                    ' <a href="' .
                                    Route::url('index.php?option=com_groups&controller=roles&task=unassign&gid=' .
                                    $this->filters['gid'] .
                                    '&id=' .
                                    $row->uidNumber .
                                    '&roleid=' .
                                    $role['id'] .
                                    '&return=' .
                                    $this->controller) .
                                    '" title="' .
                                    Lang::txt('COM_GROUPS_UNASSIGN_ROLE') .
                                    '">x</a></span>';
                            endforeach;
                            echo implode(', ', $r);
                            ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="priority-3">
                    <span>
                        <?php echo $this->escape(stripslashes($row->username) ?? ''); ?>
                    </span>
                </td>
                <td class="priority-5">
                    <span>
                        <?php echo $this->escape(stripslashes($row->email ?? '')); ?>
                    </span>
                </td>
                <td>
                    <span class="status <?php echo $status; ?>">
                        <?php echo $status; ?>
                    </span>
                </td>
                <td>
            <?php if ($canDo->get('core.edit')) { ?>
                <?php
                switch ($status) {
                    case 'invitee':
                    case 'inviteemail':
                        ?>
                        <?php
                        $memberId = isset($row->uidNumber)
                            ? $row->uidNumber : $row->email;
                        $uninviteUrl = Route::url(
                            'index.php?option=' . $this->option .
                            '&controller=' . $this->controller .
                            '&task=uninvite&gid=' .
                            $this->filters['gid'] .
                            '&id=' . $memberId .
                            '&' . Session::getFormToken() . '=1'
                        );
                        $confirmJs = "javascript:if (confirm('Cancel invitation?'))"
                            . "{return true;}else{return false;}";
                        ?>
                        <a
                            class="state unpublish"
                            onclick="<?php echo $confirmJs; ?>"
                            href="<?php echo $uninviteUrl; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_UNINVITE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        break;
                    case 'applicant':
                        ?>
                        <?php
                        $url = Route::url(
                            'index.php?option=' .
                            $this->option .
                            '&controller=' .
                            $this->controller .
                            '&task=approve&gid=' .
                            $this->filters['gid'] .
                            '&id=' .
                            $row->uidNumber .
                            '&' .
                            Session::getFormToken() . '=1'
                        );
                        ?>
                        <a class="state publish" href="<?php echo $url; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_APPROVE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        $denyUrl = Route::url(
                            'index.php?option=' . $this->option .
                            '&controller=' . $this->controller .
                            '&task=deny&gid=' .
                            $this->filters['gid'] .
                            '&id=' . $row->uidNumber .
                            '&' . Session::getFormToken() . '=1'
                        );
                        $denyJs = "javascript:if (confirm('Deny membership?'))"
                            . "{return true;}else{return false;}";
                        ?>
                        <a
                            class="state unpublish"
                            onclick="<?php echo $denyJs; ?>"
                            href="<?php echo $denyUrl; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_DENY'); ?></span>
                        </a>
                        <?php
                        break;
                    case 'manager':
                        ?>
                        <?php
                        $url = Route::url(
                            'index.php?option=' .
                            $this->option .
                            '&controller=' .
                            $this->controller .
                            '&task=demote&gid=' .
                            $this->filters['gid'] .
                            '&id=' .
                            $row->uidNumber .
                            '&' .
                            Session::getFormToken() . '=1'
                        );
                        ?>
                        <a class="state demote" href="<?php echo $url; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_DEMOTE'); ?></span>
                        </a>
                    </td>
                    <td>
                        &nbsp;
                        <?php
                        break;
                    default:
                    case 'member':
                        ?>
                        <?php
                        $url = Route::url(
                            'index.php?option=' .
                            $this->option .
                            '&controller=' .
                            $this->controller .
                            '&task=promote&gid=' .
                            $this->filters['gid'] .
                            '&id=' .
                            $row->uidNumber .
                            '&' .
                            Session::getFormToken() . '=1'
                        );
                        ?>
                        <a class="state promote" href="<?php echo $url; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_PROMOTE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        $deleteUrl = Route::url(
                            'index.php?option=' . $this->option .
                            '&controller=' . $this->controller .
                            '&task=delete&gid=' .
                            $this->filters['gid'] .
                            '&id=' . $row->uidNumber .
                            '&' . Session::getFormToken() . '=1'
                        );
                        $deleteJs = "javascript:if (confirm('Cancel membership?'))"
                            . "{return true;}else{return false;}";
                        ?>
                        <a
                            class="state trash"
                            onclick="<?php echo $deleteJs; ?>"
                            href="<?php echo $deleteUrl; ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_REMOVE'); ?></span>
                        </a>
                        <?php
                        break;
                }
                ?>
            <?php } ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="gid" value="<?php echo $this->filters['gid']; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />

    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>