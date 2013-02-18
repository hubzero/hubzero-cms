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

$query  = "SELECT sd.publish_up, sd.publish_down, cag.* 
		FROM #__courses_asset_groups AS cag 
		LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope_id=cag.id AND sd.scope='asset_group'
		WHERE (sd.publish_up >= '" . $now . "' OR sd.publish_down <= '" . $weeklater . "') 
		AND cag.parent>0 
		ORDER BY sd.publish_up";

$database->setQuery($query);
$rows = $database->loadObjectList();
?>
<div class="course_dashboard">
	
	<h3 class="heading">
		<a name="dashboard"></a>
		<?php echo JText::_('PLG_COURSES_DASHBOARD'); ?>
	</h3>
	
	<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=dashboard'); ?>" method="post">
		<div class="two columns first">
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
			<!-- <table>
				<tbody>
					<tr>
						<td>
							<strong><?php echo $this->offering->members(array('count' => true)); ?></strong> enrolled
						</td>
					</tr>
					<tr>
						<td>
							<strong>158</strong> passing
						</td>
					</tr>
					<tr>
						<td>
							<strong>46</strong> failing
						</td>
					</tr>
				</tbody>
			</table> -->
		</div><!-- / .subject -->
		<div class="two columns second">
			<h3>
				In the next week
			</h3>
			<div class="timeline-start">
				<p><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></p>
			</div>
			<ul class="timeline">
				<li>
					Lecture 1
				</li>
				<li>
					Lecture 2
				</li>
				<li>
					Lecture 3
				</li>
				<li>
					Lecture 4
				</li>
				<li>
					Lecture 5
				</li>
			</ul>
			<div class="timeline-start">
				<p><?php echo JHTML::_('date', $weeklater, $dateFormat, $tz); ?></p>
			</div>
		</div><!-- / .subject -->
		<div class="clear"></div>
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
			$view->base       = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum';
			$view->parser     = $p;
			$view->wikiconfig = $wikiconfig;
			$view->attach     = $this->attach;
			$view->course     = $this->course;
			$view->offering   = $this->offering;
			$view->display();
		}
			?>

		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias'); ?>" />
		<input type="hidden" name="active" value="dashboard" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
	</form>
	</div>
</div><!--/ #course_members -->
