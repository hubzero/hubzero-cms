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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
</h3>
<div class="container">
	<p class="section-options">
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=questions&action=new'); ?>"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>
	</p>
	<table class="questions entries">
		<caption>
			<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
			<span>(<?php echo $this->rows->count(); ?>)</span>
		</caption>
		<tbody>
	<?php if ($this->rows) { ?>
		<?php
		$i = 1;

		foreach ($this->rows as $row)
		{
			$i++;

			$name = Lang::txt('PLG_RESOURCES_QUESTIONS_ANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$name = $this->escape(stripslashes($row->creator()->get('name', $name)));
				if ($row->creator()->get('public'))
				{
					$name = '<a href="' . Route::url($row->creator()->getLink()) . '">' . $name . '</a>';
				}
			}

			$cls  = ($row->get('state') == 1) ? 'answered' : '';
			$cls  = ($row->isReported())      ? 'flagged'  : $cls;
			$cls .= ($row->get('created_by') == User::get('username')) ? ' mine' : '';
			?>
			<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
				<th>
					<span class="entry-id"><?php echo $row->get('id'); ?></span>
				</th>
				<td>
				<?php if (!$row->isReported()) { ?>
					<a class="entry-title" href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a><br />
				<?php } else { ?>
					<span class="entry-title"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></span><br />
				<?php } ?>
					<span class="entry-details">
						<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name); ?>
						<span class="entry-date-at"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_AT'); ?></span>
						<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
						<span class="entry-date-on"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ON'); ?></span>
						<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-state">
							<?php echo ($row->get('state') == 1) ? Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_CLOSED') : Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_OPEN'); ?>
						</span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_NUM_RESPONSES', $row->get('rcount')); ?>">
								<?php echo $row->responses->count(); ?>
							</a>
						</span>
					</span>
				</td>
			<?php if ($this->banking) { ?>
				<td class="reward">
				<?php if ($row->get('reward') == 1 && $this->banking) { ?>
					<span class="entry-reward"><?php echo $row->get('points'); ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo Lang::txt('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points', 0)); ?>"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_POINTS'); ?></a></span>
				<?php } ?>
				</td>
			<?php } ?>
				<td class="voting">
					<span class="vote-like">
					<?php if (User::isGuest()) { ?>
						<span class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP_LOGIN'); ?>">
							<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $row->get('helpful', 0)); ?>
						</span>
					<?php } else { ?>
						<a class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo Route::url('index.php?option=com_answers&task=vote&id=' . $row->get('id') . '&category=question&vote=yes'); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP', $row->get('helpful', 0)); ?>">
							<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $row->get('helpful', 0)); ?>
						</a>
					<?php } ?>
					</span>
				</td>
			</tr>
		<?php } ?>
	<?php } else { ?>
			<tr class="noresults">
				<td colspan="<?php echo ($this->banking) ? '4' : '3'; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_NO_QUESTIONS_FOUND'); ?>
				</td>
			</tr>
	<?php } ?>
		</tbody>
	</table>
	<div class="clearfix"></div>
</div><!-- / .container -->
