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

//get objects
$config   =& JFactory::getConfig();
$database =& JFactory::getDBO();
?>
<div id="content-header">
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>
</div>
<div id="content-header-extra">
	<ul>
		<li>
			<a class="browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
				<?php echo JText::_('Browse courses'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="course section">
	<div class="aside">
<?php /*if ($this->course->isManager($tjis->juser->get('id')) || $this->course->isStudent($tjis->juser->get('id'))) { ?>
		<p><a class="add btn">View course</a></p>
<?php } else {*/ ?>
		<p><a class="add btn">Add to cart</a></p>
<?php //} ?>
		<p>Rating distribution</p>
		<ul>
			<li>5 star</li>
			<li>4 star</li>
			<li>3 star</li>
			<li>2 star</li>
			<li>1 star</li>
		</ul>
	</div>
	
	<div class="subject">
		<div id="sub-menu">
			<ul>
				<li id="sm-1" class="active"><a class="tab" href="#"><span>Overview</span></a></li>
				<li id="sm-3"><a class="tab" href="#"><span>Reviews</span></a></li>
				<li id="sm-2"><a class="tab" href="#"><span>Past Offerings</span></a></li>
			</ul>
		</div><!-- / #sub-menu -->

<?php
		foreach ($this->notifications as $notification) 
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
?>

<?php
		//$gt = new CoursesTags($database);
		//echo $gt->get_tag_cloud(0,0,$this->course->get('gidNumber'));

		echo $this->parser->parse(stripslashes($this->course->get('description')), $this->wikiconfig);
?>
	<table>
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
if ($this->course->offerings())
{
	foreach ($this->course->offerings() as $offering)
	{
?>
			<tr>
				<th>
					<a class="inst-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias')); ?>">
						<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
					</a>
				</th>
				<td>
					<?php echo $this->escape(stripslashes($offering->get('start_date'))); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($offering->get('end_date'))); ?>
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
</div><!-- /.innerwrap -->