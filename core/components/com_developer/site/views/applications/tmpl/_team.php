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
?>

<ul class="team-listing cf <?php echo (isset($this->cls)) ? $this->cls : ''; ?>">
    <?php foreach ($this->members as $member) : ?>
        <?php
            $profile = $member->getProfile();
            $me      = ($profile->get('uidNumber') == User::get('id')) ? true : false;
        ?>
        <li <?php echo ($me) ? 'class="me"' : ''; ?>>
            <?php
            $profileName = $profile->get('name');
            $titleText = $profileName . ' ' . (($me) ? '(You)' : '');
            ?>
            <a
                href="<?php echo $profile->link(); ?>"
                class="tooltips"
                title="<?php echo $titleText; ?>"
            >
                <img src="<?php echo $profile->picture(0, true); ?>" alt="" />
                <span><?php echo $profileName; ?></span>
            </a>
            <?php if (!$me) : ?>
                <?php
                $removeConfirm = Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE_CONFIRM');
                $removeUrl = Route::url($member->link('remove'));
                ?>
                <a
                    class="btn btn-danger btn-secondary remove confirm"
                    data-txt-confirm="<?php echo $removeConfirm; ?>"
                    href="<?php echo $removeUrl; ?>"
                >
                    <?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE'); ?>
                </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>