<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
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
            <?php
            $answersUrl = Route::url('index.php?option=com_answers');
            $forumUrl = Route::url('index.php?option=com_forum');
            $groupsUrl = Route::url('index.php?option=com_groups');
            ?>
            <ul>
                <li>
                    <a href="<?php echo $answersUrl; ?>">
                        <?php echo Lang::txt('COM_FEEDBACK_LINK_ANSWERS'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $forumUrl; ?>">
                        <?php echo Lang::txt('COM_FEEDBACK_LINK_FORUM'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $groupsUrl; ?>">
                        <?php echo Lang::txt('COM_FEEDBACK_LINK_GROUPS'); ?>
                    </a>
                </li>
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
                <?php
                $storyUrl = Route::url('index.php?option=' . $this->option . '&task=success_story');
                $troubleUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
                ?>
                <div class="col span6">
                    <div class="story">
                        <h3>
                            <a href="<?php echo $storyUrl; ?>">
                                <?php echo Lang::txt('COM_FEEDBACK_STORY_HEADER'); ?>
                            </a>
                        </h3>
                        <p><?php echo Lang::txt('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
                        <p>
                            <a class="more btn" href="<?php echo $storyUrl; ?>">
                                <?php echo Lang::txt('COM_FEEDBACK_STORY_BUTTON'); ?>
                            </a>
                        </p>
                    </div>
                </div><!-- / .col span6 -->
                <div class="col span6 omega">
                    <div class="report">
                        <h3>
                            <a href="<?php echo $troubleUrl; ?>">
                                <?php echo Lang::txt('COM_FEEDBACK_TROUBLE_HEADER'); ?>
                            </a>
                        </h3>
                        <p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_INTRO'); ?></p>
                        <p>
                            <a class="more btn" href="<?php echo $troubleUrl; ?>">
                                <?php echo Lang::txt('COM_FEEDBACK_TROUBLE_BUTTON'); ?>
                            </a>
                        </p>
                    </div>
                </div><!-- / .col span6 omega -->
            </div><!-- / .grid -->
            <?php if ($this->wishlist || !empty($this->xpoll)) { ?>
                <div class="grid">
                    <div class="col span6">
                    <?php if ($this->wishlist) { ?>
                        <?php $wishlistUrl = Route::url('index.php?option=com_wishlist'); ?>
                        <div class="wish">
                            <h3>
                                <a href="<?php echo $wishlistUrl; ?>">
                                    <?php echo Lang::txt('COM_FEEDBACK_WISHLIST_HEADER'); ?>
                                </a>
                            </h3>
                            <p><?php echo Lang::txt('COM_FEEDBACK_WISHLIST_DESCRIPTION'); ?></p>
                            <p>
                                <a class="more btn" href="<?php echo $wishlistUrl; ?>">
                                    <?php echo Lang::txt('COM_FEEDBACK_WISHLIST_BUTTON'); ?>
                                </a>
                            </p>
                        </div>
                    <?php } ?>
                    </div><!-- / .col span6 -->
                    <div class="col span6 omega">
                    <?php if ($this->poll) { ?>
                        <?php $pollUrl = Route::url('index.php?option=' . $this->option . '&task=poll'); ?>
                        <div class="poll">
                            <h3>
                                <a href="<?php echo $pollUrl; ?>">
                                    <?php echo Lang::txt('COM_FEEDBACK_POLL_HEADER'); ?>
                                </a>
                            </h3>
                            <p><?php echo Lang::txt('COM_FEEDBACK_POLL_DESCRIPTION'); ?></p>
                            <p>
                                <a class="more btn" href="<?php echo $pollUrl; ?>">
                                    <?php echo Lang::txt('COM_FEEDBACK_POLL_BUTTON'); ?>
                                </a>
                            </p>
                        </div>
                    <?php } ?>
                    </div><!-- / .col span6 omega -->
                </div><!-- / .grid -->
            <?php } ?>
        </div><!-- / .col span9 omega -->
    </div><!-- / .grid -->
</section><!-- / .section -->
