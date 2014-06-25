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

$base = $this->offering->link();
?>

	<h3 class="heading">
		<a name="dashboard"></a>
		<?php echo JText::_('PLG_COURSES_DASHBOARD'); ?>
	</h3>

	<div class="sub-section">
		<div class="sub-section-overview">
			<h3>
				<?php echo JText::_('PLG_COURSES_DASHBOARD_OVERVIEW'); ?>
			</h3>
			<p><?php echo JText::_('PLG_COURSES_DASHBOARD_OVERVIEW_ABOUT'); ?></p>
		</div>
		<div class="sub-section-content">
			<div class="grid">
			<div class="col span3">
				<table class="breakdown">
					<tbody>
						<tr>
							<td>
								<span>
									<?php echo JText::sprintf('PLG_COURSES_DASHBOARD_ENROLLED', '<strong>' . $this->offering->members(array('count' => true, 'student'=>1)) . '</strong>'); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-passing">
								<span>
									<?php echo JText::sprintf('PLG_COURSES_DASHBOARD_PASSING', '<strong>' . $this->offering->gradebook()->countPassing() . '</strong>'); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-failing">
								<span>
									<?php echo JText::sprintf('PLG_COURSES_DASHBOARD_FAILING', '<strong>' . $this->offering->gradebook()->countFailing() . '</strong>'); ?>
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
					<?php
					foreach ($rows as $i => $row)
					{
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
					}
					?>
					</ul>
				<?php } else { ?>
					<ul class="dashboard-timeline">
						<li class="noresults"><?php echo JText::_('PLG_COURSES_DASHBOARD_NOTHING_HAPPENING'); ?></li>
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
