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
defined('_JEXEC') or die( 'Restricted access' );
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="course" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn')); ?>">
				<?php echo JText::_('Back to Course'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<div class="main section">
<form name="coursePages" action="index.php" method="POST" id="hubForm">

<?php 
	// Get the course units
	$unitsTbl = new CourseUnits($this->database);
	$units    = $unitsTbl->getCourseUnits();
?>

<ul class="sortable"><div class="add first"></div>

<?php foreach ($units as $unit) { ?>
	<li>
		<span class="title"><?php echo $unit->title; ?></span>
<?php
	// Get the course asset groups
	$assetGroupsTbl = new CourseAssetGroups($this->database);

	// Get the unique asset group types (this will build our sub-headings)
	$assetGroupTypes = $assetGroupsTbl->getUniqueCourseAssetGroupTypes($filters=array(
		"w"=>array(
			"course_unit_id"=>$unit->id
		)
	));

	echo "<ul class=\"sortable\"><div class=\"add\"></div>";

	if(count($assetGroupTypes > 0))
	{
		// Loop through the asset group types
		foreach($assetGroupTypes as $agt)
		{
			// Now grab all of the individual asset groups
			$assetGroups = $assetGroupsTbl->getCourseAssetGroups($filters=array(
				"w"=>array(
					"course_unit_id"=>$unit->id
				)
			));

			echo "<li><span class=\"title\">{$agt['type']}</span>";
			echo "<ul class=\"sortable\"><div class=\"add\"></div>";

			if(count($assetGroups > 0))
			{
				// Loop through the asset groups
				foreach($assetGroups as $ag)
				{
					if($ag->type == $agt['type'])
					{
						echo "<li><span class=\"title\">{$ag->title}</span>";

						// Get the course assets
						$assetsTbl = new CourseAssets($this->database);
						$assets    = $assetsTbl->getCourseAssets($filters=array(
							"w"=>array(
								"course_asset_scope_id" => $ag->id,
								"course_asset_scope" => "asset_group"
							)
						));

						// Start our list
						echo "<ul class=\"sortable\"><div class=\"add\"></div>";

						// Loop through the assets
						if(count($assets) > 0)
						{
							foreach($assets as $a)
							{
								echo "<li><span class=\"title\">{$a->title}</span>";
								echo "<span class=\"drag-n-drop\">drag file here</span>";
							}
						}
						// End the list
						echo "</ul>";
						echo "</li>";
					}
				}
			}
			echo "</ul>";
			echo "</li>";
		}
	}
	echo "</ul>";
?>

<?php } // close foreach ?>

</ul>
</form>
</div>