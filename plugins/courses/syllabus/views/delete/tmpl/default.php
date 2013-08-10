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

$yearFormat = '%Y';
$monthFormat = '%m';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat = 'Y';
	$monthFormat = 'm';
	$tz = null;
}

?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=blog&task=delete&entry='.$this->entry->id); ?>" method="post" id="hubForm">
		<div class="explaination">
<?php if ($this->authorized) { ?>
			<p><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=blog&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo JText::_('PLG_COURSES_BLOG_DELETE_HEADER'); ?></legend>

	 		<p class="warning"><?php echo JText::sprintf('PLG_COURSES_BLOG_DELETE_WARNING',$this->entry->title); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" /> 
				<?php echo JText::_('PLG_COURSES_BLOG_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="gid" value="<?php echo $this->course->cn; ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="entry" value="<?php echo $this->entry->id; ?>" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_COURSES_BLOG_DELETE'); ?>" />
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=blog&scope='.JHTML::_('date',$this->entry->publish_up, $yearFormat, $tz).'/'.JHTML::_('date',$this->entry->publish_up, $monthFormat, $tz).'/'.$this->entry->alias); ?>">Cancel</a>
		</p>
	</form>
