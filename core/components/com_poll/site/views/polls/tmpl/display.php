<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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

defined('_HZEXEC_') or die();

$this->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_POLL'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-stats btn" href="<?php echo Route::url('index.php?option=com_poll&view=latest'); ?>">
				<?php echo Lang::txt('COM_POLL_TAKE_LATEST_POLL'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section polls">
	<div class="grid">
	<?php
	$i = 0;
	foreach ($this->polls as $poll) { ?>
		<div class="col span4<?php if ($i == 2) { echo ' omega'; } ?>">
			<div class="poll">
				<div class="details">
					<h3><?php echo $this->escape($poll->get('title')); ?></h3>

					<?php if ($poll->get('open')) { ?>
						<form id="poll<?php echo $poll->get('id'); ?>" method="post" action="<?php echo Route::url('index.php?option=com_poll&task=vote'); ?>">
							<fieldset>
								<ul class="poll-options">
									<?php foreach ($poll->options()->where('text', '!=', '')->ordered()->rows() as $option) : ?>
										<li>
											<input type="radio" name="voteid" id="voteid<?php echo $option->id; ?>" value="<?php echo $this->escape($option->id); ?>" />
											<label for="voteid<?php echo $option->id; ?>">
												<?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
								<p>
									<input type="submit" name="task_button" class="button" value="<?php echo Lang::txt('COM_POLL_VOTE'); ?>" />
									 &nbsp;
									<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->get('id'))); ?>"><?php echo Lang::txt('COM_POLL_RESULTS'); ?></a>
								</p>

								<input type="hidden" name="option" value="com_poll" />
								<input type="hidden" name="task" value="vote" />
								<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
								<?php echo Html::input('token'); ?>
							</fieldset>
						</form>
					<?php } else { ?>
						<ul class="poll-results">
							<?php $i = 1; ?>
							<?php foreach ($poll->options()->where('text', '!=', '')->ordered()->rows() as $option) : ?>
								<?php
								$option->percent = ($poll->voters ? round(100 * $option->hits / $poll->voters, 1) : 0);
								$option->class   = 'polls_color_' . $i;
								$i++;
								?>
								<li>
									<span class="optn"><?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?></span>
									<span class="hits"><?php echo $this->escape($option->percent); ?>%</span>
									<div class="graph">
										<strong class="bar <?php echo $option->class; ?>" style="width: <?php echo $this->escape($option->percent); ?>%;"><span><?php echo $this->escape($option->hits); ?>%</span></strong>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php } ?>
				</div>

				<div class="meta">
					<div class="grid">
						<div class="col span6">
							<span class="opt icon-votes"><?php echo Lang::txt('COM_POLL_VOTES', $poll->dates()->total()); ?></span>
						</div>
						<div class="col span6 omega">
							<span class="status <?php echo $poll->get('open') ? Lang::txt('open') : Lang::txt('closed'); ?>"><?php echo $poll->get('open') ? Lang::txt('open') : Lang::txt('closed'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
		if ($i == 2)
		{
			echo '</div><div class="grid">';
		}
		$i++;
	}
	?>
	</div>
</section><!-- / .main section -->