<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$plugins = Event::trigger('courses.onCourse', array(
    $this->course,
    $this->offering,
    true
));
?>
<div id="guide-overlay"
    class="guide-wrap"
    data-action="<?php echo Route::url($this->offering->link() . '&active=' . $this->plugin . '&unit=mark'); ?>">
    <div class="guide-content">

        <div class="grid">
            <div class="col span-half">
                <div class="guide-nav">
                    <ul>
                        <?php
                        foreach ($plugins as $k => $plugin) {
                            //do we want to show category in menu?
                            if (!$plugin->get('display_menu_tab')) {
                                continue;
                            }
                            ?>
                            <li>
                                <?php
                                $pluginName = $plugin->get('name');
                                $pluginTitle = $plugin->get('title');
                                $blurbKey = 'PLG_COURSES_'
                                    . strtoupper($pluginName) . '_BLURB';
                                ?>
                                <strong class="<?php echo $pluginName; ?>">
                                    <?php echo $pluginTitle; ?>
                                </strong>
                                <span>
                                    <?php echo Lang::txt($blurbKey); ?>
                                </span>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col span-half omega">
                <div class="guide-about">
                    <h3><?php echo Lang::txt('Welcome to the course!'); ?></h3>
                    <p>
                        <?php
                        echo Lang::txt(
                            'We\'ve tried to organize things to group related'
                            . ' content and make it easier to find what you need.'
                            . ' Feel free to explore the various menu options.'
                        );
                        ?>
                    </p>
                    <p>
                        <?php
                        $overviewLink = '<a href="'
                            . Route::url($this->course->link())
                            . '">Course overview</a>';
                        echo Lang::txt(
                            'You can always get back to the %s by clicking'
                            . ' the link found under the title of this course.',
                            $overviewLink
                        );
                        ?>
                    </p>
                    <p class="guide-dismiss">
                        <?php echo Lang::txt('Click anywhere to dismiss this guide and get started!'); ?>
                    </p>
                </div>

                <div class="guide-onemorething">
                    <p><?php echo Lang::txt('Oh, and one more thing:'); ?></p>
                    <p class="guide-luck"><?php echo Lang::txt('Good Luck!'); ?></p>
                </div>
            </div>
        </div>

    </div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->
