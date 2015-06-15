<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
			<span>(<?php echo ($this->rows) ? count($this->rows) : '0'; ?>)</span>
		</caption>
		<tbody>
	<?php if ($this->rows) { ?>
		<?php
		$i = 1;

		foreach ($this->rows as $row)
		{
			if ($i > $this->limit)
			{
				break;
			}

			$row = new \Components\Answers\Models\Question($row);

			$i++;

			$name = Lang::txt('PLG_RESOURCES_QUESTIONS_ANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$name = $this->escape(stripslashes($row->creator('name', $name)));
				if ($row->creator('public'))
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
					<a class="entry-title" href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape($row->subject('clean')); ?></a><br />
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
								<?php echo $row->get('rcount'); ?>
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
						<a class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo Route::url('index.php?option=com_answers&task=vote&id=' . $row->get('id') . '&vote=1'); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP', $row->get('helpful', 0)); ?>">
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
