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

$now = JFactory::getDate();

$year  = JHTML::_('date', $now, 'Y');
$month = JHTML::_('date', $now, 'm');
$day   = JHTML::_('date', $now, 'd');

$weeklater = JFactory::getDate(mktime(0, 0, 0, $month, $day+7, $year));

$database = JFactory::getDBO();

$query  = "SELECT sd.* 
		FROM #__courses_offering_section_dates AS sd
		WHERE sd.section_id=" . $this->offering->section()->get('id') . "
		AND (sd.publish_up >= " . $database->Quote($now) . " AND sd.publish_up <= " . $database->Quote($weeklater) . ") 
		AND sd.scope!='asset'
		ORDER BY sd.publish_up LIMIT 20";

$database->setQuery($query);
$rows = $database->loadObjectList();

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '');
?>

	<h3 class="heading">
		<a name="dashboard"></a>
		<?php echo JText::_('PLG_COURSES_DASHBOARD'); ?>
	</h3>
	
	<div class="sub-section">
		<div class="sub-section-overview">
			<h3>
				Overview
			</h3>
			<p>This is a quick overview of how students are doing and what's coming up.</p>
		</div>
		<div class="sub-section-content">
			<div class="grid">
			<div class="col span3">
				<table class="breakdown">
					<tbody>
						<tr>
							<td>
								<span>
									<strong><?php echo $this->offering->members(array('count' => true, 'student'=>1)); ?></strong> enrolled
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-passing">
								<span>
									<strong><?php echo $this->offering->gradebook()->countPassing(); ?></strong> passing
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-failing">
								<span>
									<strong><?php echo $this->offering->gradebook()->countFailing(); ?></strong> failing
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col span9 omega">
				<div class="dashboard-timeline-start">
					<p><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAT_HZ1')); ?></p>
				</div>
			<?php if ($rows) { ?>
				<ul class="dashboard-timeline">
				<?php foreach ($rows as $i => $row) { 

					switch ($row->scope)
					{
						case 'unit':
							$obj = CoursesModelUnit::getInstance($row->scope_id);
							$url = $base . '&active=outline';
						break;
						case 'asset_group':
							$obj = new CoursesModelAssetGroup($row->scope_id);
							$unit = CoursesModelUnit::getInstance($obj->get('unit_id'));
							$url = $base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $obj->get('alias');
						break;
						case 'asset':
							$obj = new CoursesModelAsset($row->scope_id);
							$url = $base . '&active=outline&unit=&b=&c=';
						break;
					}
					if (!$obj->exists() || !$obj->isPublished() || ($row->scope == 'asset_group' && !$obj->get('parent')))
					{
						// skip containers
						continue;
					}
					?>
					<li>
						<a href="<?php echo JRoute::_($url); ?>">
							<?php echo $this->escape(stripslashes($obj->get('title'))); ?>
						</a>
						<span class="details">
							<time datetime="<?php echo $row->publish_up; ?>"><?php echo JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_HZ1')); ?></time>
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
					<p><?php echo JHTML::_('date', $weeklater, JText::_('DATE_FORMAT_HZ1')); ?></p>
				</div>
			</div>
			</div><!-- / .grid -->
		</div>
		<div class="clear"></div>
	</div>

<?php
	JPluginHelper::importPlugin('courses');
	$dispatcher = JDispatcher::getInstance();

	$after = $dispatcher->trigger('onCourseDashboard', array($this->course, $this->offering));
	echo implode("\n", $after);
/*
	<div class="sub-section discussions">
		<div class="sub-section-overview">
			<h3>
				<a name="discussions"></a>
				<?php echo JText::_('Discussions'); ?>
			</h3>
			<p>These are the latest discussions posts, ordered newest to oldest.</p>
		</div>
		<div class="sub-section-content">
		
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
		<div class="clear"></div>
	</div>
*/?>

