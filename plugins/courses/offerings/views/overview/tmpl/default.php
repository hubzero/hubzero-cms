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
			<th>Enrolled</th>
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
		//$url = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias');
		?>
		<tr>
			<th class="offering-title">
				<span>
					<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
				</span>
			</th>
		<?php if ($offering->sections()->total() <= 1) { ?>
			<td>
				<?php if ($this->course->isManager()) { ?>
				--
				<?php } else if ($offering->student(JFactory::getUser()->get('id'))->get('student')) { ?>
				<a class="enter btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
					Access Course
				</a>
				<?php } else { ?>
					<?php if ($offerings->total() > 1 && $offering->isAvailable()) { ?>
					<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
						Enroll in Course
					</a>
					<?php } else { ?>
					--
					<?php } ?>
				<?php } ?>
			</td>
			<td>
				<?php if ($offering->isAvailable()) { ?>
				<span class="accepting enrollment">
					Accepting
				</a>
				<?php } else { ?>
				<span class="closed enrollment">
					Closed
				</span>
				<?php } ?>
			</td>
		<?php } else { ?>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		<?php } ?>
		</tr>
		<?php
		//if ($this->course->isManager()) // || ($this->course->isStudent() && $nonDefault))
		if ($offering->sections()->total() > 1)
		{
			foreach ($offering->sections() as $section) 
			{
				if (!$this->course->isManager() && $section->get('enrollment') == 2)
				{
					continue;
				}
				$offering->section($section->get('id'));
				/*if ($this->course->isStudent())
				{
					if ($section->get('id') != $nonDefault && $section->get('alias') != '__default')
					{
						continue;
					}
				}*/
				//$surl = $url . ($section->get('alias') != '__default' ? ':' . $section->get('alias') : '');
			?>
		<tr>
			<th class="section-title">
				<span>
					<?php echo $this->escape(stripslashes($section->get('title'))); ?>
				</span>
			</th>
			<td>
				<?php if ($this->course->isManager()) { ?>
				<a class="enter btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
					Access Course
				</a>
				<?php } else if ($offering->student(JFactory::getUser()->get('id'))->get('student')) { ?>
				<a class="enter btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
					Access Course
				</a>
				<?php } else { ?>
					<?php if ($offerings->total() > 1 && $offering->isAvailable()) { ?>
					<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
						Enroll in Course
					</a>
					<?php } else { ?>
					--
					<?php } ?>
				<?php } ?>
			</td>
			<td>
				<?php 
				switch ($section->get('enrollment')) 
				{ 
					case 0: 
						?>
						<span class="accepting enrollment">
							Accepting
						</a>
						<?php 
					break;

					case 1:
						?>
						<span class="restricted enrollment">
							Restricted
						</span>
						<?php 
					break;

					case 2:
						?>
						<span class="closed enrollment">
							Closed
						</span>
						<?php 
					break;
				} 
				?>
			</td>
		</tr>
			<?php
			}
		}
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