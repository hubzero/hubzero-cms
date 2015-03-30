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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="course" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias')); ?>"><?php echo JText::_('Back to Course'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
		foreach ($this->notifications as $notification)
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><strong>Are you sure you want to delete?</strong></p>
			<p>Deleting a course will permanently remove the course and all data associated with that course.</p>
			<p>&nbsp;</p>

			<p><strong>Alternative to deleting</strong></p>
			<p>You could set the course join policy to closed to restrict further membership activity and set the discoverability to hidden so the course is hidden to the world but still there later if you decide you want to use the course again.</p>
			<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=edit'); ?>">&raquo; Click here to edit course settings</a></p>
			<!--
			<div class="admin-options">
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&task=view'); ?>"><?php echo JText::_('COURSES_VIEW_COURSE'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&task=edit'); ?>"><?php echo JText::_('COURSES_EDIT_COURSE'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&task=customize'); ?>"><?php echo JText::_('COURSES_CUSTOMIZE_COURSE'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&task=invite'); ?>"><?php echo JText::_('COURSES_INVITE_USERS'); ?></a></p>
			</div>
			-->
		</div>
		<fieldset>
			<h3><?php echo JText::_('COURSES_DELETE_HEADER'); ?></h3>

	 		<p class="warning"><?php echo JText::sprintf('COURSES_DELETE_WARNING',$this->course->get('description')).'<br /><br />'.$this->log; ?></p>

			<label>
				<?php echo JText::_('COURSES_DELETE_MESSAGE'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo $this->escape($this->msg); ?></textarea>
			</label>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" />
				<?php echo JText::_('COURSES_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->course->get('cn'); ?>" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo JText::_('DELETE'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
