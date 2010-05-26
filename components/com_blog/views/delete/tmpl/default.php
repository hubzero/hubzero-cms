<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<p><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('Archive'); ?></a></p>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&entry='.$this->entry->id); ?>" method="post" id="hubForm">
		<div class="explaination">
<?php if ($this->authorized) { ?>
			<p><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
<?php } ?>
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_BLOG_DELETE_HEADER'); ?></h3>

	 		<p class="warning"><?php echo JText::sprintf('COM_BLOG_DELETE_WARNING',$this->entry->title); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" /> 
				<?php echo JText::_('COM_BLOG_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="id" value="<?php echo $this->entry->id; ?>" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_BLOG_DELETE'); ?>" /></p>
	</form>
</div><!-- / .main section -->