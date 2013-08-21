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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
	<li id="author_<?php echo $this->id; ?>">
		<span class="handle"><?php echo JText::_('DRAG HERE'); ?></span> 
		<a class="state trash" data-parent="author_<?php echo $this->id; ?>" href="#" onclick="HUB.Resources.removeAuthor('author_<?php echo $this->id; ?>');return false;"><span><?php echo JText::_('remove'); ?></span></a>
		<?php echo $this->escape(stripslashes($this->name)); ?> (<?php echo $this->id; ?>)
		<br /><?php echo JText::_('Affiliation'); ?>: <input type="text" name="<?php echo $this->id; ?>_organization" value="<?php echo $this->escape(stripslashes($this->org)); ?>" />
		
		<select name="<?php echo $this->id; ?>_role">
			<option value=""<?php if ($this->role == '') { echo ' selected="selected"'; }?>><?php echo JText::_('Author'); ?></option>
<?php 
	if ($this->roles)
	{
		foreach ($this->roles as $role)
		{
?>
			<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($this->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php
		}
	}
?>
		</select>
		<input type="hidden" class="authid" name="<?php echo $this->id; ?>authid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="<?php echo $this->id; ?>_name" value="<?php echo $this->escape(stripslashes($this->name)); ?>" />
	</li>