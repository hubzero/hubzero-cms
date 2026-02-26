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

$this->css('introduction.css', 'system')
     ->css();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if (User::isGuest()) { ?>
        <div id="content-header-extra">
            <p>
                <?php $href = Route::url('index.php?option=com_members&controller=register'); ?>
                <a
                    class="icon-add add btn"
                    href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEMBERS_REGISTER_NOW'); ?></a>
            </p>
        </div><!-- / #content-header-extra -->
    <?php } ?>
</header>

<section id="introduction" class="section">
    <div class="grid">
        <div class="col span9">
            <div class="grid">
                <div class="col span6">
                    <h3><?php echo Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER'); ?></h3>
                    <p><?php echo Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER_EXPLANATION'); ?></p>
                </div><!-- / .col span6 -->
                <div class="col span6 omega">
                    <h3><?php echo Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER'); ?></h3>
                    <p><?php echo Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER_EXPLANATION'); ?></p>
                </div><!-- / .col span6 -->
            </div>
        </div>
        <div class="col span3 omega">
            <ul>
                <li>
                    <?php $href = Route::url('index.php?option=com_members&view=credentials&layout=remind'); ?>
                    <a href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEMBERS_FORGOT_USERNAME'); ?></a>
                </li>
                <li>
                    <?php $href = Route::url('index.php?option=com_members&view=credentials&layout=reset'); ?>
                    <a href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEMBERS_FORGOT_PASSWORD'); ?></a>
                </li>
                <li>
                    <?php $href = Route::url('index.php?option=com_help&component=members'); ?>
                    <a class="popup" href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEMBERS_NEED_HELP'); ?></a>
                </li>
                <li>
                    <?php $href = Route::url('index.php?option=com_groups'); ?>
                    <a href="<?php echo $href; ?>"><?php echo Lang::txt('COM_GROUPS'); ?></a>
                </li>
            </ul>
        </div>
    </div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

    <div class="grid">
        <div class="col span3">
            <h2><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS'); ?></h2>
        </div><!-- / .col span3 -->
        <div class="col span9 omega">
            <div class="grid">
                <div class="col span6">
                    <?php $formAction = Route::url('index.php?option=' . $this->option . '&task=browse'); ?>
                    <form action="<?php echo $formAction; ?>" method="get" class="search">
                        <fieldset>
                            <p>
                                <?php $text = Lang::txt('COM_MEMBERS_FIND_MEMBERS_SEARCH_LABEL'); ?>
                                <label for="gsearch"><?php echo $text; ?></label>
                                <input type="text" name="search" id="gsearch" value="" />
                                <input type="submit" value="<?php echo Lang::txt('Search'); ?>" />
                            </p>
                            <p>
                                <?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_SEARCH'); ?>
                            </p>
                        </fieldset>
                    </form>
                </div><!-- / .col span6 -->
                <div class="col span6 omega">
                    <div class="browse">
                        <?php $val = Route::url('index.php?option=' . $this->option . '&task=browse'); ?>
                        <?php $val1 = Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_BROWSING'); ?>
                        <p><a href="<?php echo $val; ?>"><?php echo $val1; ?></a></p>
                        <p><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_LISTING'); ?></p>
                    </div><!-- / .browse -->
                </div><!-- / .col span6 -->
            </div><!-- / .grid -->
        </div><!-- / .col span9 omega -->
    </div><!-- / .grid -->

    <?php /*if ($this->contribution_counting) { ?>
        <div class="grid">
            <div class="col span3">
                <h2><?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR'); ?></h2>
            </div><!-- / .col span3 -->
            <div class="col span9 omega">
                <div class="grid">
                    <?php
                    $rows = \Components\Members\Models\Member::all()
                        ->whereEquals('block', 0)
                        ->whereEquals('activation', 1)
                        ->where('approved', '>', 0)
                        ->order('contributions', 'desc')
                        ->limit(4)
                        ->rows();

                    if ($rows->count())
                    {
                        $i = 0;
                        foreach ($rows as $contributor)
                        {
                            if ($i == 2)
                            {
                                $i = 0;
                            }

                            switch ($i)
                            {
                                case 2: $cls = ''; break;
                                case 1: $cls = 'omega'; break;
                                case 0:
                                default: $cls = ''; break;
                            }
                            ?>
                            <div class="col span-half <?php echo $cls; ?>">
                                <div class="contributor">
                                    <p class="contributor-photo">
                                        <a href="<?php echo Route::url($contributor->link()); ?>">
                                            <?php $val = $contributor->picture(); ?>
                                            <?php
                                            $contribAlt = Lang::txt(
                                                'COM_MEMBERS_TOP_CONTRIBUTOR_PICTURE',
                                                $this->escape(stripslashes($contributor->get('name')))
                                            );
                                            ?>
                                            <img
                                                src="<?php echo $val; ?>"
                                                alt="<?php echo $contribAlt; ?>" />
                                        </a>
                                    </p>
                                    <div class="contributor-content">
                                        <h4 class="contributor-name">
                                            <a href="<?php echo Route::url($contributor->link()); ?>">
                                                <?php echo $this->escape(stripslashes($contributor->get('name'))); ?>
                                            </a>
                                        </h4>
                                        <?php if ($org = $contributor->get('organization')) { ?>
                                            <p class="contributor-org">
                                                <?php echo $this->escape(stripslashes($org)); ?>
                                            </p>
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                    </div>
                                    <p class="course-instructor-bio">
                                        <?php if ($bio = $contributor->get('bio')) { ?>
                                            <?php echo Hubzero\Utility\Str::truncate(strip_tags($bio), 200); ?>
                                        <?php } else { ?>
                                            <em><?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR_NO_BIO'); ?></em>
                                        <?php } ?>
                                    </p>
                                </div>
                            </div><!-- / .col span-third -->
                            <?php if ($i == 1) { ?>
                            </div><!-- / .grid -->
                            <div class="grid">
                            <?php } ?>
                            <?php
                            $i++;
                        }
                    }
                    else
                    {
                        ?>
                        <?php
                        $val = Lang::txt(
                            'COM_MEMBERS_TOP_CONTRIBUTOR_NO_RESULTS',
                            Route::url('index.php?option=com_resources&task=new')
                        );
                        ?>
                        <p><?php echo $val; ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div><!-- / .col span9 omega -->
        </div><!-- / .grid -->
    <?php }*/ ?>
</section><!-- / .section -->
