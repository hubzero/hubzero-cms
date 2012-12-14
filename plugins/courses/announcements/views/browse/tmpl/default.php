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

$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

$juser = JFactory::getUser();
//$offering = $this->course->offering();
$rows = $this->offering->announcements($this->filters);
$manager = $this->offering->access('manage');
?>
<div class="course_members">
	<a name="members"></a>
	<h3 class="heading"><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS'); ?></h3>
		
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&offering=' . $this->offering->get('alias') . '&active=announcements'); ?>" method="post">
		<div class="subject">
			<div class="container">

				<div class="entries-search">
					<fieldset>
						<input type="text" name="q" value="<?php echo $this->escape($this->filters['search']); ?>" />
						<input type="submit" name="search_members" value="" />
					</fieldset>
				</div>
				<div class="clearfix"></div>

				<table class="courses entries" summary="Members of this course">
					<caption>
						<?php 
							if ($this->filters['search']) 
							{
								echo 'Search: "' . $this->escape($this->filters['search']) . '" in ';
							} 
						?>
						Announcements
					</caption>
					<tbody>
<?php if ($rows->total() > 0) { ?>
	<?php foreach ($rows as $row) { ?>
						<tr class="odd">
							<td class="entry-content">
								<?php echo stripslashes($row->get('content')); ?>
								<span class="entry-details">
								<?php if ($manager) { ?>
									<span class="entry-author">
										<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
									</span>
								<?php } ?>
									<span class="entry-date-at">@</span> 
									<span class="time">
										<time datetime="<?php echo $row->publish_up; ?>">
											<?php echo JHTML::_('date', $row->get('created'), $timeFormat, $tz); ?>
										</time>
									</span>
									<span class="entry-date-on">on</span> 
									<span class="date">
										<time datetime="<?php echo $row->publish_up; ?>">
											<?php echo JHTML::_('date', $row->get('created'), $dateFormat, $tz); ?>
										</time>
									</span>
								</span>
							</td>
						<?php if ($manager) { ?>
							<td class="entry-options">
								<?php if ($juser->get('id') == $row->get('created_by')) { ?>
														<a class="edit" href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=announcements&task=edit&entry='.$row->get('id')); ?>" title="<?php echo JText::_('Edit'); ?>">
															<?php echo JText::_('Edit'); ?>
														</a>
														<a class="delete" href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=announcements&task=delete&entry='.$row->get('id')); ?>" title="<?php echo JText::_('Delete'); ?>">
															<?php echo JText::_('Delete'); ?>
														</a>
								<?php } ?>
							</td>
						<?php } ?>
						</tr>
	<?php } ?>
<?php } else { ?>
						<tr class="odd">
							<td><?php echo JText::_('PLG_COURSES_MEMBERS_NO_RESULTS'); ?></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
			
			<?php 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				0, 
				$this->filters['start'], 
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'announcements');
			echo $pageNav->getListFooter();
			?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	
		
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias'); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="filter" value="<?php echo $this->filter; ?>" />
	</form>
</div><!--/ #course_members -->
