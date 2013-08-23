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

$dateFormat = '%d %b %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'H:i p';
	$tz = true;
}
//'index.php?option=com_answers&task=new&tag=' . urlencode($tag)
//$tag = ($this->resource->type == 7) ? 'tool:'.$this->resource->alias : 'resource:'.$this->resource->id;
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
<?php
if ($this->rows) {
	$i=1;

	$juser =& JFactory::getUser();
	$database =& JFactory::getDBO();

	require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'tags.php');
	$tagging = new AnswersTags($database);

	// Check for abuse reports on an item
	$ra = new ReportAbuse($database);

	foreach ($this->rows as $row)
	{
		// Incoming
		$filters = array(
			'id'       => $row->id,
			'category' => 'question',
			'state'    => 0
		);

		$row->reports = $ra->getCount($filters);
		$row->points  = $row->points ? $row->points : 0;

		if ($i<= $this->limit) {
			$i++;

			// author name
			$name = JText::_('PLG_RESOURCES_QUESTIONS_ANONYMOUS');
			if ($row->anonymous == 0) {
				$user =& JUser::getInstance($row->created_by);
				if (is_object($user)) {
					$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $user->get('id')) . '">' . $this->escape(stripslashes($user->get('name'))) . '</a>';
				} else {
					$name = JText::_('PLG_RESOURCES_QUESTIONS_UNKNOWN');
				}
			}

			$cls  = ($row->state == 1) ? 'answered' : '';
			$cls  = ($row->reports) ? 'flagged' : $cls;
			$cls .= ($row->created_by == $juser->get('username')) ? ' mine' : '';
?>
			<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
				<th>
					<span class="entry-id"><?php echo $row->id; ?></span>
				</th>
				<td>
<?php					if (!$row->reports) { ?>
					<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id=' . $row->id); ?>"><?php echo $this->escape(stripslashes($row->subject)); ?></a><br />
<?php					} else { ?>
					<span class="entry-title"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></span><br />
<?php					} ?>
					<span class="entry-details">
						<?php echo JText::sprintf('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name); ?> <span class="entry-date-at">@</span> 
						<span class="entry-time"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $timeFormat, $tz); ?></time></span> <span class="entry-date-on">on</span> 
						<span class="entry-date"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-state">
							<?php echo ($row->state==1) ? JText::_('Closed') : JText::_('Open'); ?>
						</span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id=' . $row->id . '#answers'); ?>" title="<?php echo JText::sprintf('There are %s responses to this question.', $row->rcount); ?>">
								<?php echo $row->rcount; ?>
							</a>
						</span>
					</span>
				</td>
<?php if ($this->banking) { ?>
				<td class="reward">
<?php 		if (isset($row->reward) && $row->reward == 1 && $this->banking) { ?>
					<span class="entry-reward"><?php echo $row->points; ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo JText::sprintf('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->points); ?>">Points</a></span>
<?php 		} ?>
				</td>
<?php } ?>
				<td class="voting">
					<span class="vote-like">
<?php if ($juser->get('guest')) { ?>
						<span class="vote-button <?php echo ($row->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" title="Vote this up :: Please login to vote."><?php echo $row->helpful; ?><span> Like</span></span>
<?php } else { ?>
						<a class="vote-button <?php echo ($row->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id=' . $row->id . '&vote=1'); ?>" title="Vote this up :: <?php echo $row->helpful; ?> people liked this"><?php echo $row->helpful; ?><span> Like</span></a>
<?php } ?>
					</span>
				</td>
			</tr>
<?php
		}
	}
?>
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
