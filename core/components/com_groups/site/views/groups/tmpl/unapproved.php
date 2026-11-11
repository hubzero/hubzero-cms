<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{group: mixed, title: mixed} $this
 */

$this->css();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
    <div class="group-unapproved">
        <span class="name">
            <?php echo $this->escape($this->group->get('description')); ?>
        </span>
        <p class="warning"><?php echo Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?></p>

        <?php if (in_array(User::get('id'), $this->group->get('invitees'))) : ?>
            <hr />
            <?php
            $acceptUrl = Route::url(
                'index.php?option=com_groups&controller=groups&cn=' . $this->group->get('cn') . '&task=accept',
            );
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $acceptUrl ?>" class="group-invited">
                <?php echo Lang::txt('COM_GROUPS_ACCEPT_INVITE'); ?>
            </button>
            <hr />
        <?php endif; ?>

        <?php
        $myaccountUrl = Route::url('index.php?option=com_members&task=myaccount&active=groups');
        $linkUrl = Route::url('index.php?option=com_groups');
        ?>
        <p><a class="all-groups" href="<?= $linkUrl ?>"><?= Lang::txt('COM_GROUPS_ALL_GROUPS') ?></a> | <a
            class="my-groups" href="<?= $myaccountUrl ?>"><?= Lang::txt('COM_GROUPS_MY_GROUPS') ?></a></p>
    </div>
</section>