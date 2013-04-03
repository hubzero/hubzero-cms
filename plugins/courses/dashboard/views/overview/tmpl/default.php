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

$yearFormat  = '%Y';
$monthFormat = '%m';
$dayFormat   = '%d';
$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat  = 'Y';
	$monthFormat = 'm';
	$dayFormat   = 'd';
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

$now = date('Y-m-d H:i:s', time());

$year  = JHTML::_('date', $now, $yearFormat, $tz);
$month = JHTML::_('date', $now, $monthFormat, $tz);
$day   = JHTML::_('date', $now, $dayFormat, $tz);

$weeklater = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $day+7, $year));

$database = JFactory::getDBO();

$query  = "SELECT sd.* 
		FROM #__courses_offering_section_dates AS sd
		WHERE sd.section_id=" . $this->offering->section()->get('id') . "
		AND (sd.publish_up >= '" . $now . "' AND sd.publish_up <= '" . $weeklater . "') 
		ORDER BY sd.publish_up";

$database->setQuery($query);
$rows = $database->loadObjectList();

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias');
?>
<div class="course_dashboard">
	
	<h3 class="heading">
		<a name="dashboard"></a>
		<?php echo JText::_('PLG_COURSES_DASHBOARD'); ?>
	</h3>
	
	<div class="main section">
	<div class="sub-section">
		<div class="five columns first second third">
			<h3>
				Stats
			</h3>
			<table class="breakdown">
				<tbody>
					<tr>
						<td>
							<strong><?php echo $this->offering->members(array('count' => true)); ?></strong> enrolled
						</td>
						<td>
							<strong>158</strong> passing
						</td>
						<td>
							<strong>46</strong> failing
						</td>
					</tr>
				</tbody>
			</table>
			
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=announcements'); ?>" method="post" id="announcementForm" class="full">
				<fieldset>
					<legend>
						<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
					</legend>

					<label for="field_content">
						<span><?php echo JText::_('Announcement'); ?></span>
						<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('fields[content]', 'field_content', '', 'minimal no-footer', '35', '5');
						?>
					</label>

					<label for="field-priority" id="priority-label">
						<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1" /> 
						<?php echo JText::_('Mark as high priority'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_SUBMIT'); ?>" />
					</p>
				</fieldset>
				<div class="clear"></div>

				<input type="hidden" name="fields[id]" value="" />
				<input type="hidden" name="fields[state]" value="1" />
				<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
				<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
				<input type="hidden" name="active" value="announcements" />
				<input type="hidden" name="action" value="save" />
			</form>
			
		</div><!-- / .subject -->
		<div class="five columns fourth fifth">
			<h3>
				In the next week
			</h3>
			<div class="dashboard-timeline-start">
				<p><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></p>
			</div>
		<?php if ($rows) { ?>
			<ul class="dashboard-timeline">
			<?php foreach ($rows as $i => $row) { ?>
				<li>
					<?php 
					switch ($row->scope)
					{
						case 'unit':
							$obj = new CoursesModelUnit($row->scope_id);
							$url = $base;
						break;
						case 'asset_group':
							$obj = new CoursesModelAssetGroup($row->scope_id);
							$url = $base;
						break;
						case 'asset':
							$obj = new CoursesModelAsset($row->scope_id);
							$url = $base . '&unit=&b=&c=';
						break;
					}
				?>
					<a href="<?php echo JRoute::_($url); ?>">
						<?php echo $this->escape(stripslashes($obj->get('title'))); ?>
					</a>
					<span class="details">
						<time datetime="<?php echo $row->publish_up; ?>"><?php echo JHTML::_('date', $row->publish_up, $dateFormat, $tz); ?></time>
					</span>
				</li>
			<?php 
				if ($i > 0 && $row->scope == 'unit')
				{
					break;
				}
			} ?>
			</ul>
		<?php } else { ?>
			<ul class="dashboard-timeline">
				<li class="noresults">Nothing coming up in the next week.</li>
			</ul>
		<?php } ?>
			<div class="dashboard-timeline-start">
				<p><?php echo JHTML::_('date', $weeklater, $dateFormat, $tz); ?></p>
			</div>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</div>

		<div class="sub-section">
		<h3>
			<a name="discussions"></a>
			<?php echo JText::_('Recent Discussions'); ?>
		</h3>
		<?php 
		if ($this->comments) 
		{ 
			ximport('Hubzero_Wiki_Parser');

			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => 'forum',
				'pagename' => 'forum',
				'pageid'   => $this->post->id,
				'filepath' => '',
				'domain'   => $this->post->id
			);
			$p =& Hubzero_Wiki_Parser::getInstance();

			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => 'dashboard',
					'name'    => 'overview',
					'layout'  => 'list'
				)
			);
			$view->option     = $this->option;
			$view->comments   = $this->comments;
			$view->post       = $this->post;
			$view->unit       = ''; //$this->category->alias;//$this->unit->get('alias');
			$view->lecture    = $this->post->id;
			$view->config     = $this->params;
			$view->depth      = 0;
			$view->cls        = 'odd';
			$view->base       = $base . '&active=forum';
			$view->parser     = $p;
			$view->wikiconfig = $wikiconfig;
			$view->attach     = $this->attach;
			$view->course     = $this->course;
			$view->offering   = $this->offering;
			$view->display();
		}
			?>
		</div>
		
	</div>
</div><!--/ #course_members -->
