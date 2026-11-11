<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups.png');

Toolbar::appendButton(
    'Popup',
    'new',
    'COM_GROUPS_NEW',
    Route::url(
        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&gid='
            . $this->filters['gid'],
    ),
    570,
    170,
);

Toolbar::appendButton(
    'Link',
    'unblock',
    'COM_GROUPS_ROLE_ASSIGN',
    'index.php?option=' . $this->option . '&controller=roles&tmpl=component&task=assign&gid=' . $this->filters['gid'],
    400,
    400,
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
                false,
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
                false,
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
                false,
            );
            Toolbar::custom('demote', 'demote', 'COM_GROUPS_MEMBER_DEMOTE', 'COM_GROUPS_MEMBER_DEMOTE', false, false);
        }
        if (
            $canDo->get('core.edit') && \Hubzero\User\Group\Membership::supported()
            && \Hubzero\User\Group\Membership::enabled()
        ) {
            Toolbar::appendButton(
                'Popup',
                'options',
                'COM_GROUPS_MEMBER_TERM',
                Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                        . '&tmpl=component&task=expiration&gid=' . $this->filters['gid'],
                ),
                570,
                300,
            );
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

$this
    ->css()
    ->js();

$view = $this;

// The attribute that marks the current value in the form fields below
$statusSelected = function ($value) use ($view) {
    if ($view->filters['status'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<?php
// Shown when the feature is on, and also whenever something is still past due,
// so switching the feature off does not hide enforcement that is still running.
$expirationPastDue = \Hubzero\User\Group\Membership::supported()
    ? \Hubzero\User\Group\Membership::pastDueCount()
    : 0;
?>
<?php if (
    \Hubzero\User\Group\Membership::supported()
    && (\Hubzero\User\Group\Membership::enabled() || $expirationPastDue > 0)
) : ?>
    <?php
    // Terms are enforced by a cron job, so a stopped job is the difference
    // between end dates being enforced and being decorative. Say so plainly.
    $reaperOk  = \Hubzero\User\Group\Membership::reaperHealthy();
    $lastRun   = \Hubzero\User\Group\Membership::lastRun();
    $pastDue   = $expirationPastDue;
    ?>
    <div class="<?php echo $reaperOk ? 'info' : 'error'; ?>" id="membership-expiration-health">
        <?php if (!$reaperOk) : ?>
            <strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_REAPER_STALLED'); ?></strong>
            <?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_REAPER_STALLED_DESC'); ?>
        <?php else : ?>
            <?php echo Lang::txt(
                'COM_GROUPS_MEMBERSHIP_REAPER_OK',
                Date::of($lastRun->timestamp)->toLocal(Lang::txt('DATE_FORMAT_HZ1') . ' g:ia'),
            ); ?>
        <?php endif; ?>
        <?php if ($pastDue > 0) : ?>
            <?php echo ' ' . Lang::txt('COM_GROUPS_MEMBERSHIP_PAST_DUE', $pastDue); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span8">
                <label for="filter_search"><?php echo Lang::txt('COM_GROUPS_SEARCH'); ?>:</label>
                <input type="text" name="search" id="filter_search" class="filter"
                    value="<?= $this->escape($this->filters['search']) ?>"
                    placeholder="<?= Lang::txt('COM_GROUPS_SEARCH') ?>" />

                <label for="filter-status"><?php echo Lang::txt('COM_GROUPS_MEMBER_STATUS'); ?>:</label>
                <select name="status" id="filter-status" class="filter filter-submit">
                    <option value=""<?= $statusSelected('') ?>><?= Lang::txt('COM_GROUPS_MEMBER_STATUS') ?></option>
                    <!-- <option value="member"<?php //echo ($this->filters['status'] == 'member') ? ' selected="selected"' : '';?>>Member</option> -->
                    <option value="manager"<?= $statusSelected('manager') ?>><?= Lang::txt('Manager') ?></option>
                    <option value="applicant"<?= $statusSelected('applicant') ?>><?= Lang::txt('Applicant') ?></option>
                    <option value="invitee"<?= $statusSelected('invitee') ?>><?= Lang::txt('Invitee') ?></option>
                </select>

                <?php if (!empty($this->expiration_supported)) : ?>
                    <label for="filter-term"><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM'); ?>:</label>
                    <select name="term" id="filter-term" class="filter filter-submit">
                        <option value=""<?= (@$this->filters['term'] == '') ? ' selected="selected"' : '' ?>>
                            <?= Lang::txt('COM_GROUPS_MEMBER_TERM_ANY') ?>
                        </option>
                        <option
                            value="limited"<?= (@$this->filters['term'] == 'limited') ? ' selected="selected"' : '' ?>>
                            <?= Lang::txt('COM_GROUPS_MEMBER_TERM_LIMITED') ?>
                        </option>
                        <?php
                        $selectedAttr = (@$this->filters['term'] == 'perpetual') ? ' selected="selected"' : '';
                        $optionText = Lang::txt('COM_GROUPS_MEMBER_TERM_PERPETUAL');
                        ?>
                        <option value="perpetual"<?= $selectedAttr ?>><?= $optionText ?></option>
                        <?php
                        $selectedAttr = (@$this->filters['term'] == 'expiring') ? ' selected="selected"' : '';
                        $optionText = Lang::txt('COM_GROUPS_MEMBER_TERM_EXPIRING');
                        ?>
                        <option value="expiring"<?= $selectedAttr ?>><?= $optionText ?></option>
                    </select>
                <?php endif; ?>

                <input type="submit" value="<?php echo Lang::txt('COM_GROUPS_GO'); ?>" />
            </div>
            <div class="col span4">
                <?php
                $linkUrl = Route::url(
                    'index.php?option=com_groups&controller=roles&tmpl=component&gid=' . $this->filters['gid'],
                );
                ?>
                <a class="button modal" href="<?= $linkUrl ?>"
                    rel="{size: {width: 570, height: 170}, onClose: function() {}}">
                    <span class="icon-32-new"><?php echo Lang::txt('Roles'); ?></span>
                </a>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <?php
                $linkUrl = Route::url('index.php?option=' . $this->option);
                ?>
                <th colspan="8"><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_GROUPS') ?></a> >
                    (<?= $this->escape(stripslashes($this->group->get('cn'))) ?>)
                    <?= $this->escape(stripslashes($this->group->get('description'))) ?></th>
            </tr>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <th scope="col" class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_GROUPS_USERID',
                        'uidNumber',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_GROUPS_NAME',
                        'name',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_GROUPS_USERNAME',
                        'username',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-5">
                    <?= Html::grid(
                        'sort',
                        'COM_GROUPS_EMAIL',
                        'email',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
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
                    $this->filters['limit'],
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

            $roles = Components\Groups\Helpers\Permissions::getGroupMemberRoles(
                $row->uidNumber,
                $this->group->get('gidNumber'),
            );
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="checkbox" name="id[]" id="cb<?= $i ?>"
                        value="<?= $this->escape((isset($row->uidNumber)) ? $row->uidNumber : $row->email) ?>"
                        class="checkbox-toggle" />
                    <?php
                    $labelText = $this->escape((isset($row->uidNumber)) ? $row->uidNumber : $row->email);
                    ?>
                    <label for="cb<?= $i ?>" class="sr-only visually-hidden"><?= $labelText ?></label>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape($row->uidNumber); ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit') && isset($row->username)) : ?>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=com_members&controller=members&task=edit&id=' . $row->uidNumber,
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
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
                            $r = [];
                            foreach ($roles as $role) :
                                $r[] = '<span class="role">' . $this->escape($role['name']) . ' <a href="'
                                    . Route::url(
                                        'index.php?option=com_groups&controller=roles&task=unassign&gid='
                                            . $this->filters['gid'] . '&id=' . $row->uidNumber . '&roleid='
                                            . $role['id'] . '&return=' . $this->controller,
                                    ) . '" title="' . Lang::txt('COM_GROUPS_UNASSIGN_ROLE') . '">x</a></span>';
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
                    <?php if (!empty($this->expiration_supported) && isset($row->uidNumber)) : ?>
                        <?php $rowExpires = isset($this->expirations[(int) $row->uidNumber])
                            ? $this->expirations[(int) $row->uidNumber]
                            : null; ?>
                        <?php if ($rowExpires) : ?>
                            <br />
                            <?php
                            $termExpiresSoonClass = (strtotime($rowExpires . ' UTC') <= time() + (30 * 86400))
                                ? ' term-expires-soon'
                                : '';
                            ?>
                            <span class="membership-term<?= $termExpiresSoonClass ?>">
                                <?php echo Lang::txt(
                                    'COM_GROUPS_MEMBER_TERM_ENDS',
                                    Date::of($rowExpires)->toLocal(Lang::txt('DATE_FORMAT_HZ1')),
                                ); ?>
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
            <?php if ($canDo->get('core.edit')) { ?>
                <?php
                switch ($status) {
                    case 'invitee':
                    case 'inviteemail':
                        ?>
                        <?php
                        $uninviteUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                . '&task=uninvite&gid=' . $this->filters['gid'] . '&id='
                                . (isset($row->uidNumber) ? $row->uidNumber : $row->email) . '&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="state unpublish"
                            onclick="javascript:if (confirm('Cancel invitation?')){return true;}else{return false;}"
                            href="<?= $uninviteUrl ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_UNINVITE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        break;
                    case 'applicant':
                        ?>
                        <?php
                        $approveUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                . '&task=approve&gid=' . $this->filters['gid'] . '&id=' . $row->uidNumber . '&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="state publish" href="<?= $approveUrl ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_APPROVE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        $denyUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=deny&gid='
                                . $this->filters['gid'] . '&id=' . $row->uidNumber . '&' . Session::getFormToken()
                                . '=1',
                        );
                        ?>
                        <a class="state unpublish"
                            onclick="javascript:if (confirm('Deny membership?')){return true;}else{return false;}"
                            href="<?= $denyUrl ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_DENY'); ?></span>
                        </a>
                        <?php
                        break;
                    case 'manager':
                        ?>
                        <?php
                        $demoteUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                . '&task=demote&gid=' . $this->filters['gid'] . '&id=' . $row->uidNumber . '&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="state demote" href="<?= $demoteUrl ?>">
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
                        $promoteUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                . '&task=promote&gid=' . $this->filters['gid'] . '&id=' . $row->uidNumber . '&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="state promote" href="<?= $promoteUrl ?>">
                            <span><?php echo Lang::txt('COM_GROUPS_MEMBER_PROMOTE'); ?></span>
                        </a>
                    </td>
                    <td>
                        <?php
                        $deleteUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                . '&task=delete&gid=' . $this->filters['gid'] . '&id=' . $row->uidNumber . '&'
                                . Session::getFormToken() . '=1',
                        );
                        ?>
                        <a class="state trash"
                            onclick="javascript:if (confirm('Cancel membership?')){return true;}else{return false;}"
                            href="<?= $deleteUrl ?>">
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
