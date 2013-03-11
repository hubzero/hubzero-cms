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
Hubzero_Document::addPluginStylesheet('courses', 'offerings');

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
<div class="container">
<table class="entries">
	<thead>
		<tr>
			<th>Offering</th>
			<th>Starts</th>
			<th>Ends</th>
			<th>Enrollment</th>
		</tr>
	</thead>
	<tbody>
<?php
$offerings = $this->course->offerings(array('state' => 1, 'sort_Dir' => 'ASC'), true);
if ($offerings->total() > 0)
{
	foreach ($offerings as $offering)
	{
?>
		<tr>
			<th>
				<a class="inst-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias')); ?>">
					<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
				</a>
			</th>
			<td>
				<time datetime="<?php echo $offering->get('publish_up'); ?>"><?php echo JHTML::_('date', $offering->get('publish_up'), $dateformat, $tz); ?></time>
			</td>
			<td>
				<time datetime="<?php echo $offering->get('publish_down'); ?>"><?php echo ($offering->get('publish_down') == '0000-00-00 00:00:00') ? JText::_('(never)') : JHTML::_('date', $offering->get('publish_down'), $dateformat, $tz); ?></time>
			</td>
			<td>
				<?php if ($offering->isAvailable()) { ?>
				accepting
				<?php } else { ?>
				closed
				<?php } ?>
			</td>
		</tr>
<?php
	}
}
else
{
?>
		<tr>
			<td><?php echo JText::_('No offerings found'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>
</div>