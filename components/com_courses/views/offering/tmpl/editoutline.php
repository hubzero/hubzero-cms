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

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="course btn" href="<?php echo JRoute::_($base); ?>">
				<?php echo JText::sprintf('MY_COURSE', $this->course->get('title')); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach($this->notifications as $notification)
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<div class="outline-main">
	<form name="editoutline" action="index.php" method="POST" id="">

		<ul class="unit sortable">
			<div class="add first"></div>
<?php 
			foreach ($this->course->offering->units() as $unit)
			{
?>
			<li class="unit-item">
				<div class="title unit-title"><?php echo $unit->title; ?>: <?php echo $unit->description; ?></div>
				<div class="progress-container">
					<div class="progress-indicator"></div>
				</div>
				<div class="clear"></div>
				<ul class="asset-group-type-list sortable">
					<div class="add"></div>
<?php
				foreach($unit->assetgroups() as $agt)
				{
?>
					<li>
						<div class="asset-group-title title"><?php echo $agt->get('title'); ?></div>
						<div class="clear"></div>
						<ul class="asset-group sortable">
							<div class="add"></div>
<?php
					foreach($agt->children() as $ag)
					{
?>
							<li class="asset-group-item">
								<div class="asset-group-item-title editable title"><?php echo $ag->get('title'); ?></div>
								<div class="uploadfiles">Drag files here to upload</div>
								<div class="clear"></div>
<?php
						// Loop through the assets
						if ($ag->assets()->total())
						{
?>
								<ul class="sortable">
<?php
								foreach ($ag->assets() as $a)
								{
									$href = $a->path($this->course->get('id'));
									if ($a->get('type') == 'video')
									{
										$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
									}
									echo '<li class="asset-item asset ' . $a->get('type') . '">' . $this->escape(stripslashes($a->get('title'))) . ' (<a class="" href="' . $href . '">preview</a>)</li>';
								}
?>
								</ul>
<?php
						}
?>
							</li>
<?php
					}
?>
						</ul>
<?php
					if ($agt->assets()->total())
					{
?>
						<ul class="sortable">
<?php
						foreach ($agt->assets() as $a)
						{
							$href = $a->path($this->course->get('id'));
							if ($a->get('type') == 'video')
							{
								$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $agt->get('alias'));
							}
							echo '<li><a class="asset ' . $a->get('type') . '" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
						}
?>
						</ul>
<?php
					}
?>
					</li>
<?php
				}
?>
				</ul>
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
							$href = JRoute::_($base . '&active=outline&a=' . $unit->get('alias'));
						}
						echo '<li><a class="asset ' . $a.get('type') . '" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
					}
?>
					</ul>
<?php
				}
?>
			</li>
<?php
			}
?>
			<li class="add-new unit-item">Add a new unit</li>
		</ul>

	</form>
</div>