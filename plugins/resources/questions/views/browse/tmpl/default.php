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
?>
<h3 class="section-header">
	<a name="questions"></a>
	<?php echo JText::_('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
</h3>
<div class="container">
	<p class="section-options">
		<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=questions&action=new'); ?>"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>
	</p>
	<table class="questions entries" summary="Questions submitted by the community">
		<caption>
			<?php echo JText::_('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?> 
			<span>(<?php echo ($this->rows) ? count($this->rows) : '0'; ?>)</span>
		</caption>
		<tbody>
<?php if ($this->rows) { ?>
	<?php
		$i = 1;

		$juser =& JFactory::getUser();

		foreach ($this->rows as $row)
		{
			if ($i > $this->limit) 
			{
				break;
			}

			$row = new AnswersModelQuestion($row);

			$i++;

			// author name
			$name = JText::_('PLG_RESOURCES_QUESTIONS_ANONYMOUS');
			if (!$row->get('anonymous')) 
			{
				$user =& JUser::getInstance($row->get('created_by'));
				if (is_object($user)) 
				{
					$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $user->get('id')) . '">' . $this->escape(stripslashes($user->get('name'))) . '</a>';
				} 
				else 
				{
					$name = JText::_('PLG_RESOURCES_QUESTIONS_UNKNOWN');
				}
			}

			$cls  = ($row->get('state') == 1) ? 'answered' : '';
			$cls  = ($row->isReported())      ? 'flagged'  : $cls;
			$cls .= ($row->get('created_by') == $juser->get('username')) ? ' mine' : '';
	?>
			<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
				<th>
					<span class="entry-id"><?php echo $row->get('id'); ?></span>
				</th>
				<td>
				<?php if (!$row->isReported()) { ?>
					<a class="entry-title" href="<?php echo JRoute::_($row->link()); ?>"><?php echo $this->escape(stripslashes($row->get('subject'))); ?></a><br />
				<?php } else { ?>
					<span class="entry-title"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></span><br />
				<?php } ?>
					<span class="entry-details">
						<?php echo JText::sprintf('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name); ?> <span class="entry-date-at">@</span> 
						<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> <span class="entry-date-on">on</span> 
						<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-state">
							<?php echo ($row->get('state') == 1) ? JText::_('Closed') : JText::_('Open'); ?>
						</span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo JRoute::_($row->link() . '#answers'); ?>" title="<?php echo JText::sprintf('There are %s responses to this question.', $row->get('rcount')); ?>">
								<?php echo $row->get('rcount'); ?>
							</a>
						</span>
					</span>
				</td>
			<?php if ($this->banking) { ?>
				<td class="reward">
				<?php if ($row->get('reward') == 1 && $this->banking) { ?>
					<span class="entry-reward"><?php echo $row->get('points'); ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo JText::sprintf('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points', 0)); ?>">Points</a></span>
				<?php } ?>
				</td>
			<?php } ?>
				<td class="voting">
					<span class="vote-like">
					<?php if ($juser->get('guest')) { ?>
						<span class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" title="Vote this up :: Please login to vote.">
							<?php echo $row->get('helpful', 0); ?><span> Like</span>
						</span>
					<?php } else { ?>
						<a class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=com_answers&task=vote&id=' . $row->get('id') . '&vote=1'); ?>" title="Vote this up :: <?php echo $row->get('helpful', 0); ?> people liked this">
							<?php echo $row->get('helpful', 0); ?><span> Like</span>
						</a>
					<?php } ?>
					</span>
				</td>
			</tr>
	<?php } ?>
<?php } else { ?>
			<tr class="noresults">
				<td colspan="<?php echo ($this->banking) ? '4' : '3'; ?>">
					<?php echo JText::_('PLG_RESOURCES_QUESTIONS_NO_QUESTIONS_FOUND'); ?>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
	<div class="clearfix"></div>
</div><!-- / .container -->
