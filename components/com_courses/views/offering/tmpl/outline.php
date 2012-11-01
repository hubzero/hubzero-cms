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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// ---------------
// Course Outline
// ---------------

// Member and manager checks
$isMember       = $this->course->access('view'); //$this->config->get('access-view-course');
$isManager      = $this->course->access('manage'); //$this->config->get('access-manage-course');
$isNowOnManager = ($isManager) ? true : false;

$this->database = JFactory::getDBO();

if (!$this->course->offering()->access('view')) {
?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php 
} else {
// Check to make sure we should display the course outline
/*
?>

<div class="course-content-header">
	<h3><?php echo JText::_('COURSES_COURSE_OUTLINE'); ?></h3>
	<div class="course-content-header-extra">
		<a href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn') . '&active=syllabus'); ?>"><?php echo JText::_('VIEW_SYLLABUS'); ?> &rarr;</a>
	</div>
</div>

<?php */ /*if(JRequest::getInt('nonadmin') == '1') { $isNowOnManager = false; } ?>

<?php if ($isNowOnManager) : ?>
	<p class="info">You're viewing this page as a course admin, <a href="<?php echo $_SERVER['REQUEST_URI']; ?>?nonadmin=1">click</a> to view it as a student</p>
<?php elseif ($isManager && !$isNowOnManager) : ?>
	<p class="info">You're viewing this page in student view, <a href="<?php echo str_replace('?nonadmin=1', '', $_SERVER['REQUEST_URI']); ?>">click</a> to view it as an admin</p>
<?php endif;*/ ?>



<?php 
	// Get the current time
	$now = date("Y-m-d H:i:s");
	
	$i = 0;
?>

	<!-- <ol id="timeline" class="instance"> -->
	<div id="course-outline">
<?php foreach ($this->course->offering->units() as $unit) { ?>
		<div class="unit<?php echo ($i == 0) ? ' active' : ''; ?>">
			<div class="unit-wrap">
				<div class="unit-content<?php echo ($unit->available()) ? ' open' : ''; ?>">
					<h3>
						<span><?php echo $this->escape(stripslashes($unit->title)); ?></span> 
						<?php echo $this->escape(stripslashes($unit->description)); ?>
					</h3>
<?php if (!$unit->started()) { ?>
					<div class="comingSoon">
						<p class="status">Coming soon</p>
<?php } else { ?>
					<div>
						<p class="status posted">Posted</p>

						<div class="details">
							<div class="detailsWrapper">
<?php
	// Loop through the asset group types
	foreach ($unit->assetgroups() as $agt)
	{
?>
		<div class="weeksection">
			<h4>
				<?php echo $this->escape(stripslashes($agt->get('title'))); ?>
			</h4>
<?php 
		// Loop through the child asset groups
		foreach ($agt->children() as $ag)
		{
?>
			<h5>
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline&a=' . $unit->get('alias') . '&b=' . $ag->get('alias')); ?>">
					<?php echo $this->escape(stripslashes($ag->get('title'))); ?>
				</a>
			</h5>
			<ul>
<?php
				// Loop through the assets
				//if (count($assets) > 0)
				if ($ag->assets()->total())
				{
					foreach ($ag->assets() as $a)
					{
						$href = $a->path($this->course->get('id'));
						if ($a->get('type') == 'video')
						{
							$href = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline&a=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
						}
						echo '<li><a class="" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
					}
				}
				else
				{
					echo "<li><small>" . JText::_('COURSES_NO_ASSETS_FOR_GROUPING') . "</small></li>";
				}
?>
			</ul>
<?php
		}
?>
		</div><!-- / .weekSection -->
<?php
	}
	$i++;
?>
						</div><!-- / .detailsWrapper -->
					</div><!-- / .details -->
					<div class="clear"></div>
<?php
					if ($unit->assets()->total())
					{
?>
						<ul>
<?php
						foreach ($unit->assets() as $a)
						{
							$href = $a->path($this->course->get('id'));
							if ($a->get('type') == 'video')
							{
								$href = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline&a=' . $unit->get('alias'));
							}
							echo '<li><a class="" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
						}
?>
						</ul>
<?php
					}
?>
<?php } // close else ?>
</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
<?php } // close foreach ?>
	</div>
<?php
}
?>