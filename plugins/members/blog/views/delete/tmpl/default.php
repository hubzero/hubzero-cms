<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=blog';
?>

<ul id="page_options">
	<li>
		<a class="icon-archive archive btn" href="<?php echo JRoute::_($base); ?>">
			<?php echo JText::_('Archive'); ?>
		</a>
	</li>
</ul>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_($base . '&task=delete&entry=' . $this->entry->get('id')); ?>" method="post" id="hubForm">
		<div class="explaination">
		<?php if ($this->authorized) { ?>
			<p><a class="icon-add add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
		<?php } ?>
		</div>
		<fieldset class="delete-entry">
			<legend><?php echo JText::_('PLG_MEMBERS_BLOG_DELETE_HEADER'); ?></legend>

	 		<p class="warning"><?php echo JText::sprintf('PLG_MEMBERS_BLOG_DELETE_WARNING', $this->escape(stripslashes($this->entry->get('title')))); ?></p>

			<label for="confirmdel">
				<input type="checkbox" class="option" name="confirmdel" id="confirmdel" value="1" /> 
				<?php echo JText::_('PLG_MEMBERS_BLOG_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="id" value="<?php echo $this->entry->get('created_by'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="entry" value="<?php echo $this->entry->get('id'); ?>" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_BLOG_DELETE'); ?>" />
			<a href="<?php echo JRoute::_($this->entry->link()); ?>"><?php echo JText::_('Cancel'); ?></a>
		</p>
	</form>
