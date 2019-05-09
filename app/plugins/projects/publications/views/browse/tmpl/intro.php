<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div id="pubintro">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_HOW_IT_WORKS'); ?> <?php if ($this->pub->config('documentation')) { ?>
	<span class="learnmore"><a href="<?php echo $this->pub->config('documentation'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a></span>
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
			<h4><span class="num">3</span> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE'); ?></h4>
			<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE_ABOUT'); ?></p>
		</div>
	</div>
</div>