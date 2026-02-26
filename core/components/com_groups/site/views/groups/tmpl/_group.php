<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$group = Hubzero\User\Group::getInstance($this->group->gidNumber);

//get status
$status  = '';
$options = '';

//determine group status
if ($group->get('published') == 1 && !User::isGuest()) {
    $members = $group->get('members');

    if (in_array(User::get('id'), $members)) {
        $status  = 'member';
        $options = '<a class="cancel tooltips" href="' .
            Route::url('index.php?option=' .
            $this->option .
            '&cn=' .
            $group->get('cn') .
            '&task=cancel') .
            '" title="' .
            Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') .
            '">' .
            Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') .
            '</a>';

        $managers = $group->get('managers');
        if (in_array(User::get('id'), $managers)) {
            $status  = 'manager';
            $options = ' <a class="customize tooltips" href="' .
                Route::url('index.php?option=' .
                $this->option .
                '&cn=' .
                $group->get('cn') .
                '&task=edit') .
                '" title="' .
                Lang::txt('COM_GROUPS_TOOLBAR_EDIT') .
                '">' .
                Lang::txt('COM_GROUPS_TOOLBAR_EDIT') .
                '</a>';
        }
    } else {
        $invitees   = $group->get('invitees');
        $applicants = $group->get('applicants');

        if (in_array(User::get('id'), $invitees)) {
            $status  = 'invitee';
            $options = ' <a class="cancel tooltips" href="' .
                Route::url('index.php?option=' .
                $this->option .
                '&cn=' .
                $group->get('cn') .
                '&task=cancel') .
                '" title="' .
                Lang::txt('COM_GROUPS_TOOLBAR_DECLINE') .
                '">' .
                Lang::txt('COM_GROUPS_TOOLBAR_DECLINE') .
                '</a>';
        } elseif (in_array(User::get('id'), $applicants)) {
            $status  = 'pending';
            $options = '<a class="cancel tooltips" href="' .
                Route::url('index.php?option=' .
                $this->option .
                '&cn=' .
                $group->get('cn') .
                '&task=cancel') .
                '" title="' .
                Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') .
                '">' .
                Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') .
                '</a>';
        }
    }
}

$published = ($group->get('published')) ? true : false;
?>
<?php $val = (!$published) ? 'notpublished' : ($group->get('published') == 2 ? 'archived' : 'published'); ?>
<div class="group <?php echo $val; ?>" id="group<?php echo $group->get('gidNumber'); ?>"
    data-id="<?php echo $group->get('gidNumber'); ?>"
    data-status="<?php echo $this->escape($status); ?>"
    data-title="<?php echo $this->escape(stripslashes($group->get('description')) . ' ' . $group->get('cn')); ?>">
    <div class="group-contents">
        <?php if ($published) : ?>
            <?php $url = Route::url(
                'index.php?option=' . $this->option .
                '&cn=' . $group->get('cn')
            ); ?>
            <a class="group-identity" href="<?php echo $url; ?>">
        <?php else : ?>
            <div class="group-identity">
        <?php endif; ?>
            <?php
            $path = PATH_APP . '/site/groups/' . $group->get('gidNumber') . '/uploads/' . $group->get('logo');

            if ($group->get('logo') && is_file($path)) :
                ?>
                <?php $val1 = with(new Hubzero\Content\Moderator($path))->getUrl(); ?>
                <?php $val = $this->escape(stripslashes($group->get('description'))); ?>
                <img src="<?php echo $val1; ?>" alt="<?php echo $val; ?>" />
            <?php else : ?>
                <span><?php echo $this->escape(stripslashes($group->get('description'))); ?></span>
            <?php endif; ?>
        <?php if ($published) : ?>
            </a>
        <?php else : ?>
            </div>
        <?php endif; ?>

        <div class="group-details">
            <span class="group-alias"><?php echo $this->escape($group->get('cn')); ?></span>
            <?php
            $desc = $this->escape(Hubzero\Utility\Str::truncate(
                stripslashes($group->get('description')),
                60
            ));
            ?>
            <?php if ($published) : ?>
                <?php $url = Route::url(
                    'index.php?option=' . $this->option .
                    '&cn=' . $group->get('cn')
                ); ?>
                <a
                    class="group-title"
                    data-id="<?php echo $group->get('gidNumber'); ?>"
                    href="<?php echo $url; ?>">
                    <?php echo $desc; ?>
                </a>
            <?php else : ?>
                <span class="group-title">
                    <?php echo $desc; ?>
                </span>
            <?php endif; ?>

            <?php if ($published && $status) : ?>
                <span class="<?php echo $status; ?> group-membership-status">
                    <?php
                    switch ($status) {
                        case 'manager':
                            echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MANAGER');
                            break;
                        case 'member':
                            echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MEMBER');
                            break;
                        case 'pending':
                            echo Lang::txt('COM_GROUPS_BROWSE_STATUS_PENDING');
                            break;
                        case 'invitee':
                            echo Lang::txt('COM_GROUPS_BROWSE_STATUS_INVITED');
                            break;
                        default:
                            break;
                    }
                    ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (!$published) : ?>
            <div class="group-meta">
                <?php $txt = Lang::txt('COM_GROUPS_STATUS_NOT_PUBLISHED_GROUP'); ?>
                <span class="not-published group-status"><?php echo $txt; ?></span>
            </div>
        <?php else : ?>
            <?php
            $gt = new \Components\Groups\Models\Tags($group->get('gidNumber'));
            echo $gt->render();
            ?>
            <div class="group-meta">
                <?php if ($status) : ?>
                    <?php if ($status == 'pending') : ?>
                        <?php echo Lang::txt('Membership request requires approval.'); ?>
                    <?php elseif ($status == 'invitee') : ?>
                        <?php
                        $acceptUrl = Route::url(
                            'index.php?option=' . $this->option .
                            '&cn=' . $group->get('cn') . '&task=accept'
                        );
                        $acceptTxt = Lang::txt('COM_GROUPS_TOOLBAR_ACCEPT');
                        ?>
                        <a
                            class="btn btn-success accept tooltips"
                            href="<?php echo $acceptUrl; ?>"
                            title="<?php echo $acceptTxt; ?>"><?php echo $acceptTxt; ?></a>
                    <?php else : ?>
                        <div>
                            <div>
                                <?php if ($group->published == 2) : ?>
                                    <span><?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED_HINT'); ?></span>
                                    <?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED'); ?>
                                <?php else : ?>
                                    <span><?php
                                    $activity = \Hubzero\Activity\Recipient::all()
                                        ->including('log')
                                        ->whereEquals('scope', 'group')
                                        ->whereEquals('scope_id', $group->get('gidNumber'))
                                        ->whereEquals('state', 1)
                                        ->ordered()
                                        ->row();
                                    if (!$activity->get('id')) {
                                        $activity->set('created', $group->get('created'));
                                    }
                                    if (
                                        !$activity->get('created') || $activity->
                                        get('created') == '0000-00-00 00:00:00'
                                    ) {
                                        echo Lang::txt('COM_GROUPS_UNKNOWN');
                                    } else {
                                        $dt = Date::of($activity->get('created'));
                                        $ct = Date::of('now');

                                        $lapsed = $ct->toUnix() - $dt->toUnix();

                                        if ($lapsed < 30) {
                                            echo Lang::txt('COM_GROUPS_ACTIVITY_JUST_NOW');
                                        } elseif ($lapsed > 30 && $lapsed < 60) {
                                            echo Lang::txt('COM_GROUPS_ACTIVITY_A_MINUTE_AGO');
                                        } else {
                                            echo $dt->relative('week');
                                        }
                                    }
                                    ?></span>
                                    <?php echo Lang::txt('COM_GROUPS_ACTIVITY_LAST'); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span><?php echo count($members); ?></span>
                                <?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php elseif ($group->published == 2) : ?>
                    <span><?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED_HINT'); ?></span>
                    <?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED'); ?>
                <?php else : ?>
                    <?php if (!$group->get('join_policy') || $group->get('join_policy') == 1) : ?>
                        <div>
                            <div>
                                <?php if (!$group->get('join_policy')) : ?>
                                    <?php $txt = Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?>
                                    <span class="open join-policy"><?php echo $txt; ?></span>
                                <?php elseif ($group->get('join_policy') == 1) : ?>
                                    <?php $txt = Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?>
                                    <span class="open join-policy"><?php echo $txt; ?></span>
                                <?php endif; ?>
                                <?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
                            </div>
                            <div class="join-group">
                                <?php $val1 = Route::url(
                                    'index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=join'
                                ); ?>
                                <?php $val = Lang::txt('COM_GROUPS_TOOLBAR_JOIN'); ?>
                                <a class="btn btn-success" href="<?php echo $val1; ?>"><?php echo $val; ?></a>
                            </div>
                        </div>
                    <?php elseif ($group->get('join_policy') == 3) : ?>
                        <?php $txt = Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?>
                        <span class="closed join-policy"><?php echo $txt; ?></span>
                        <?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
                    <?php elseif ($group->get('join_policy') == 2) : ?>
                        <?php $txt = Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?>
                        <span class="inviteonly join-policy"><?php echo $txt; ?></span>
                        <?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="user-actions">
                <?php echo $options; ?>
            </div>
        <?php endif; ?>
    </div>
</div><!-- / .group -->