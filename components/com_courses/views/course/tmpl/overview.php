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

// Show course overview content (could be custom content or public/private description)
echo $this->course_overview;

// ---------------
// Course Outline
// ---------------

// Member and manager checks
$isMember       = ($this->authorized == 'manager' || $this->authorized == 'member') ? true : false;
$isManager      = ($this->authorized == 'manager') ? true : false;
$isNowOnManager = ($isManager) ? true : false;

// Check to make sure we should display the course outline
if ($isMember && $this->tab == 'overview') : ?>

<div class="course-content-header">
	<h3><?php echo JText::_('COURSES_COURSE_OUTLINE'); ?></h3>
	<div class="course-content-header-extra">
		<a href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn') . '&active=syllabus'); ?>"><?php echo JText::_('VIEW_SYLLABUS'); ?> &rarr;</a>
	</div>
</div>

<?php if(JRequest::getInt('nonadmin') == '1') { $isNowOnManager = false; } ?>

<?php if ($isNowOnManager) : ?>
	<p class="info">You're viewing this page as a course admin, <a href="<?php echo $_SERVER['REQUEST_URI']; ?>?nonadmin=1">click</a> to view it as a student</p>
<?php elseif ($isManager && !$isNowOnManager) : ?>
	<p class="info">You're viewing this page in student view, <a href="<?php echo str_replace('?nonadmin=1', '', $_SERVER['REQUEST_URI']); ?>">click</a> to view it as an admin</p>
<?php endif; ?>

<?php 
	// Get the course units
	$unitsTbl = new CourseUnits($this->database);
	$units    = $unitsTbl->getCourseUnits();

	// Get the current time
	$now = date("Y-m-d H:i:s");
?>

<table cellpadding="0" cellspacing="0" id="course-outline">

<?php foreach ($units as $unit) {
	if($now < $unit->start_date && !$isNowOnManager) { ?>
	<tr class="comingSoon">
		<td class="week"><?php echo $unit->title; ?></td>
		<td><?php echo $unit->description; ?></td>
		<td class="status">Coming soon</td>
	</tr>
<?php } else { ?>
	<tr<?php echo ($now > $unit->start_date && $now < $unit->end_date) ? ' class="open"' : ''; ?>>
		<td class="week"><?php echo $unit->title; ?></td>
		<td><?php echo $unit->description; ?></td>
		<td class="status posted">Posted</td>
	</tr>
	<tr class="details">
		<td colspan="3">
			<div class="detailsWrapper">
				<div class="weeksection">

<?php
	// Get the course asset groups
	$assetGroupsTbl = new CourseAssetGroups($this->database);

	// Get the unique asset group types (this will build our sub-headings)
	$assetGroupTypes = $assetGroupsTbl->getUniqueCourseAssetGroupTypes($filters=array(
		"w"=>array(
			"course_unit_id"=>$unit->id
		)
	));

	// Loop through the asset group types
	foreach($assetGroupTypes as $agt)
	{
		echo "<h3>{$agt['type']}</h3>";

		// Now grab all of the individual asset groups
		$assetGroups = $assetGroupsTbl->getCourseAssetGroups($filters=array(
			"w"=>array(
				"course_unit_id"=>$unit->id
			)
		));

		// Loop through the asset groups
		foreach($assetGroups as $ag)
		{
			if($ag->type == $agt['type'])
			{
				echo "<h4>{$ag->title}</h4>";

				// Get the course assets
				$assetsTbl = new CourseAssets($this->database);
				$assets    = $assetsTbl->getCourseAssets($filters=array(
					"w"=>array(
						"course_asset_scope_id" => $ag->id,
						"course_asset_scope" => "asset_group"
					)
				));

				// Start our list
				echo "<ul>";

				// Loop through the assets
				if(count($assets) > 0)
				{
					foreach($assets as $a)
					{
						echo "<li><a class=\"\" href=\"{$a->url}\">{$a->title}</a></li>";
					}
				}
				else
				{
					echo "<li><small>" . JText::_('COURSES_NO_ASSETS_FOR_GROUPING') . "</small></li>";
				}

				// End the list
				echo "</ul>";
		 	}
		}
	}
?>
				</div>
			</div>
		</td>
	</tr>
<?php } // close else
} // close foreach ?>

</table>

<?php endif; ?>