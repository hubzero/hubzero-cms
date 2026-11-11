<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View $this
 */

// Where to turn when the knowledge base does not answer the question. A
// template that routes help elsewhere overrides this file alone.
?>
<?php if (Component::isEnabled('com_answers')) { ?>
    <div class="container">
        <h3><?php echo Lang::txt('COM_KB_COMMUNITY'); ?></h3>
        <p>
            <?php
            $tryAnswersText = Lang::txt(
                'COM_KB_COMMUNITY_TRY_ANSWERS',
                '<a href="' . Route::url('index.php?option=com_answers') . '">' . Lang::txt('COM_ANSWERS') . '</a>',
            );
            ?>
            <?= Lang::txt('COM_KB_COMMUNITY_CANT_FIND') ?> <?= $tryAnswersText ?>
        </p>
    </div><!-- / .container -->
<?php } ?>
<?php if (Component::isEnabled('com_wishlist')) { ?>
    <div class="container">
        <h3><?php echo Lang::txt('COM_KB_FEATURE_REQUEST'); ?></h3>
        <p>
            <?= Lang::txt('COM_KB_HAVE_A_FEATURE_REQUEST') ?> <a
                href="<?= Route::url('index.php?option=com_wishlist') ?>"><?= Lang::txt('COM_KB_FEATURE_TELL_US') ?></a>
        </p>
    </div><!-- / .container -->
<?php } ?>
<?php if (Component::isEnabled('com_support')) { ?>
    <div class="container">
        <h3><?php echo Lang::txt('COM_KB_TROUBLE_REPORT'); ?></h3>
        <p>
            <?php
            $newUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
            ?>
            <?= Lang::txt('COM_KB_TROUBLE_FOUND_BUG') ?> <a
                href="<?= $newUrl ?>"><?= Lang::txt('COM_KB_TROUBLE_TELL_US') ?></a>
        </p>
    </div><!-- / .container -->
<?php } ?>
