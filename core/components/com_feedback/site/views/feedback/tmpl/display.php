<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this
     ->css('introduction.css', 'system')
     ->css();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section id="introduction" class="section">
    <div class="grid">
        <div class="col span8">
            <h3><?php echo Lang::txt('COM_FEEDBACK_HAVE_SOMETHING_TO_SAY'); ?></h3>
            <p><?php echo Lang::txt('COM_FEEDBACK_INTRO', Config::get('sitename')); ?></p>
        </div>
        <div class="col span4 omega">
            <h3><?php echo Lang::txt('COM_FEEDBACK_PARTICIPATE'); ?></h3>
            <ul>
                <?php
                $linkUrl = Route::url('index.php?option=com_answers');
                ?>
                <li><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_FEEDBACK_LINK_ANSWERS') ?></a></li>
                <?php
                $linkUrl = Route::url('index.php?option=com_forum');
                ?>
                <li><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_FEEDBACK_LINK_FORUM') ?></a></li>
                <?php
                $linkUrl = Route::url('index.php?option=com_groups');
                ?>
                <li><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_FEEDBACK_LINK_GROUPS') ?></a></li>
            </ul>
        </div>
    </div>
</section><!-- / #introduction.section -->

<section class="section">
    <div class="grid">
        <div class="col span3">
            <h2><?php echo Lang::txt('COM_FEEDBACK_WAYS_TO_SUBMIT'); ?></h2>
        </div><!-- / .col span3 -->
        <div class="col span9 omega">
            <div class="grid">
                <div class="col span6">
                    <div class="story">
                        <?php
                        $successStoryUrl = Route::url('index.php?option=' . $this->option . '&task=success_story');
                        ?>
                        <h3><a href="<?= $successStoryUrl ?>"><?= Lang::txt('COM_FEEDBACK_STORY_HEADER') ?></a></h3>
                        <p><?php echo Lang::txt('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
                        <?php
                        $successStoryUrl = Route::url('index.php?option=' . $this->option . '&task=success_story');
                        ?>
                        <p><a class="more btn" href="<?= $successStoryUrl ?>"><?= Lang::txt('COM_FEEDBACK_STORY_BUTTON') ?></a></p>
                    </div>
                </div><!-- / .col span6 -->
                <div class="col span6 omega">
                    <div class="report">
                        <?php
                        $newUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
                        ?>
                        <h3><a href="<?= $newUrl ?>"><?= Lang::txt('COM_FEEDBACK_TROUBLE_HEADER') ?></a></h3>
                        <p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_INTRO'); ?></p>
                        <?php
                        $newUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
                        ?>
                        <p><a class="more btn" href="<?= $newUrl ?>"><?= Lang::txt('COM_FEEDBACK_TROUBLE_BUTTON') ?></a></p>
                    </div>
                </div><!-- / .col span6 omega -->
            </div><!-- / .grid -->
            <?php if ($this->wishlist || $this->poll) { ?>
                <div class="grid">
                    <div class="col span6">
                    <?php if ($this->wishlist) { ?>
                        <div class="wish">
                            <?php
                            $linkUrl = Route::url('index.php?option=com_wishlist');
                            ?>
                            <h3><a href="<?= $linkUrl ?>"><?= Lang::txt('COM_FEEDBACK_WISHLIST_HEADER') ?></a></h3>
                            <p><?php echo Lang::txt('COM_FEEDBACK_WISHLIST_DESCRIPTION'); ?></p>
                            <?php
                            $linkUrl = Route::url('index.php?option=com_wishlist');
                            ?>
                            <p><a class="more btn" href="<?= $linkUrl ?>"><?= Lang::txt('COM_FEEDBACK_WISHLIST_BUTTON') ?></a></p>
                        </div>
                    <?php } ?>
                    </div><!-- / .col span6 -->
                    <div class="col span6 omega">
                    <?php if ($this->poll) { ?>
                        <div class="poll">
                            <?php
                            $pollUrl = Route::url('index.php?option=' . $this->option . '&task=poll');
                            ?>
                            <h3><a href="<?= $pollUrl ?>"><?= Lang::txt('COM_FEEDBACK_POLL_HEADER') ?></a></h3>
                            <p><?php echo Lang::txt('COM_FEEDBACK_POLL_DESCRIPTION'); ?></p>
                            <?php
                            $pollUrl = Route::url('index.php?option=' . $this->option . '&task=poll');
                            ?>
                            <p><a class="more btn" href="<?= $pollUrl ?>"><?= Lang::txt('COM_FEEDBACK_POLL_BUTTON') ?></a></p>
                        </div>
                    <?php } ?>
                    </div><!-- / .col span6 omega -->
                </div><!-- / .grid -->
            <?php } ?>
        </div><!-- / .col span9 omega -->
    </div><!-- / .grid -->
</section><!-- / .section -->
