<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
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