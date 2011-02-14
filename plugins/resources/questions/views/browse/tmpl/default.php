<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<h3 class="plugin-header">
	<a name="questions"></a>
	<?php echo JText::_('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?> 
</h3>
<div class="container">
	<table class="questions entries" summary="Questions submitted by the community">
		<caption>
			<?php echo JText::_('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?> 
			<span>(<?php echo ($this->rows) ? count($this->rows) : '0'; ?>)</span>
			<a class="add" href="/answers/question/new/?tag=tool:<?php echo $this->resource->alias; ?>"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION_ABOUT_TOOL'); ?></a>
		</caption>
		<tbody>
<?php
if ($this->rows) {
	$i=1;
	
	$juser =& JFactory::getUser();
	$database =& JFactory::getDBO();

	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'helpers'.DS.'tags.php' );
	$tagging = new AnswersTags( $database );
	
	// Check for abuse reports on an item
	$ra = new ReportAbuse( $database );
	
	foreach ($this->rows as $row) 
	{
		// Incoming
		$filters = array();
		$filters['id']  = $row->id;
		$filters['category']  = 'question';
		$filters['state']  = 0;
		
		$row->reports = $ra->getCount( $filters );
		$row->points = $row->points ? $row->points : 0;
		
		if ($i<= $this->limit) {
			$i++;
			
			// author name
			$name = JText::_('PLG_RESOURCES_QUESTIONS_ANONYMOUS');
			if ($row->anonymous == 0) {
				$user =& JUser::getInstance( $row->created_by );
				if (is_object($user)) {
					$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$user->get('id')).'">'.stripslashes($user->get('name')).'</a>';
				} else {
					$name = JText::_('PLG_RESOURCES_QUESTIONS_UNKNOWN');
				}
			}
			
			$cls = ($row->state == 1) ? 'answered' : '';
			$cls = ($row->reports) ? 'flagged' : $cls;
			$cls .= ($row->created_by == $juser->get('username')) ? ' mine' : '';
?>
			<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
				<th>
					<span class="entry-id"><?php echo $row->id; ?></span>
				</th>
				<td>
<?php					if (!$row->reports) { ?>
					<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a><br />
<?php					} else { ?>
					<span class="entry-title"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></span><br />
<?php					} ?>
					<span class="entry-details">
						<?php echo JText::sprintf('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name); ?> @ 
						<span class="entry-time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on 
						<span class="entry-date"><?php echo JHTML::_('date',$row->created, '%d %b %Y', 0); ?></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-state">
							<?php echo ($row->state==1) ? JText::_('Closed') : JText::_('Open'); ?>
						</span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'#answers'); ?>" title="<?php echo JText::sprintf('There are %s responses to this question.', $row->rcount); ?>">
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
						<a class="vote-button <?php echo ($row->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'&vote=1'); ?>" title="Vote this up :: <?php echo $row->helpful; ?> people liked this"><?php echo $row->helpful; ?><span> Like</span></a>
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