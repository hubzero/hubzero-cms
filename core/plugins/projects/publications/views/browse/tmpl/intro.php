<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div id="pubintro">
    <?php $howItWorks = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_HOW_IT_WORKS'); ?>
    <?php $learnMore = Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?>
    <h3><?php echo $howItWorks; ?> <?php if ($this->pub->config('documentation')) { ?>
    <span class="learnmore"><a href="<?php echo $this->pub->config('documentation'); ?>"><?php
        echo $learnMore; ?> &raquo;</a></span>
        <?php } ?></h3>

    <div class="grid">
        <div class="col span4 step-one">
            <h4><span class="num">1</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE'); ?></h4>
            <p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE_ABOUT'); ?></p>
        </div>
        <div class="col span4 step-two">
            <h4><span class="num">2</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO'); ?></h4>
            <p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO_ABOUT'); ?></p>
        </div>
        <div class="col span4 omega step-three">
            <?php $stepThree = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE'); ?>
            <h4><span class="num">3</span> <?php echo $stepThree; ?></h4>
            <p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE_ABOUT'); ?></p>
        </div>
    </div>
</div>