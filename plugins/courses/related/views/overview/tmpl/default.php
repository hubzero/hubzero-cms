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

ximport('Hubzero_Document');
Hubzero_Document::addPluginStylesheet('courses', $this->name);
//Hubzero_Document::addPluginScript('courses', $this->_name);

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

?>
<div id="related-courses" class="after section">
	<?php if (count($this->ids) > 1) { ?>
	<h3>Other courses by these instructors</h3>
	<?php } else { ?>
	<h3>Other courses by this instructor</h3>
	<?php } ?>
<?php foreach ($this->courses as $course) { 
	$course = new CoursesModelCourse($course);
	?>
	<div class="course-block">
		<h4>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $course->get('alias')); ?>">
				<?php echo $this->escape(stripslashes($course->get('title'))); ?>
			</a>
		</h4>
		<div class="content">
			<div class="description">
				<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($course->get('blurb')), 500); ?>
			</div>
			<p class="action">
				<a class="btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $course->get('alias') . '&active=overview'); ?>">
					<?php echo JText::_('Overview'); ?>
				</a>
			</p>
		</div>
	</div>
<?php } ?>
	<div class="clear"></div>
</div>