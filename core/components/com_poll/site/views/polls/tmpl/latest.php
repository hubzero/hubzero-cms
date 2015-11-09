<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header" class="full">
	<h2><?php echo Lang::txt('COM_POLL') . ': ' . Lang::txt('COM_POLL_LATEST'); ?></h2>

	<div id="content-header-extra">
		<p><a class="icon-browse btn" href="<?php echo Route::url('index.php?option=com_poll'); ?>"><?php echo Lang::txt('COM_POLL_BROWSE'); ?></a></p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php if ($this->options->count() > 0) { ?>
		<div class="poll">
			<div class="details">
				<h3><?php echo stripslashes($this->poll->title); ?></h3>

				<form id="pollform" method="post" action="<?php echo Route::url('index.php?option=com_poll&task=vote'); ?>">
					<fieldset>
						<ul class="poll-options">
							<?php foreach ($this->options as $option) { ?>
								<li>
									<input type="radio" name="voteid" id="voteid<?php echo $option->id; ?>" value="<?php echo $option->id; ?>" alt="<?php echo $option->id; ?>" />
									<label for="voteid<?php echo $option->id; ?>"><?php echo $option->text; ?></label>
								</li>
							<?php } ?>
						</ul>
						<p>
							<input type="submit" name="task_button" value="<?php echo Lang::txt('COM_POLL_VOTE'); ?>" />
							 &nbsp;
							<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->poll->id .':' . $this->poll->alias); ?>"><?php echo Lang::txt('COM_POLL_RESULTS'); ?></a>
						</p>

						<input type="hidden" name="option" value="com_poll" />
						<input type="hidden" name="task" value="vote" />
						<input type="hidden" name="id" value="<?php echo $this->poll->id; ?>" />
						<?php echo Html::input('token'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	<?php } else { ?>
		<p><?php echo Lang::txt('COM_POLL_NO_RESULTS'); ?></p>
	<?php } ?>
</section><!-- / .main section -->
