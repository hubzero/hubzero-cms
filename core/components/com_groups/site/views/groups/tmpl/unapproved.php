<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
    <div class="group-unapproved">
        <span class="name">
            <?php echo $this->group->get('description'); ?>
        </span>
        <p class="warning"><?php echo Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?></p>

        <?php if (in_array(User::get('id'), $this->group->get('invitees'))) : ?>
            <hr />
            <?php $url = Route::url('index.php?option=com_groups&controller=groups&cn=' . $this->group->get('cn')); ?>
            <a href="<?php echo $url . '&task=accept'; ?>" class="group-invited">
                <?php echo Lang::txt('COM_GROUPS_ACCEPT_INVITE'); ?>
            </a>
            <hr />
        <?php endif; ?>

        <?php $v3 = Route::url('index.php?option=com_groups'); ?>
        <?php $v2 = Lang::txt('COM_GROUPS_ALL_GROUPS'); ?>
        <?php $v1 = Route::url('index.php?option=com_members&task=myaccount&active=groups'); ?>
        <?php $v0 = Lang::txt('COM_GROUPS_MY_GROUPS'); ?>
        <p><a class="all-groups" href="<?php echo $v3; ?>"><?php echo $v2; ?></a>
            | <a class="my-groups" href="<?php echo $v1; ?>"><?php echo $v0; ?></a></p>
    </div>
</section>