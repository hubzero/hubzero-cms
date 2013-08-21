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

$document =& JFactory::getDocument();
//$document->addScript('components/'.$this->option.'/assets/js/xsortables.js');
//$document->addScript('components/'.$this->option.'/assets/js/resources.js');

$authIDs = array();
?>
<label for="authid"><?php echo JText::_('User ID or username:'); ?></label> 
<input type="text" name="authid" id="authid" value="" />
<select name="authrole" id="authrole">
	<option value=""><?php echo JText::_('Author'); ?></option>
<?php 
if ($this->roles)
{
	foreach ($this->roles as $role)
	{
?>
	<option value="<?php echo $this->escape($role->alias); ?>"><?php echo $this->escape($role->title); ?></option>
<?php
	}
}
?>
</select>
<input type="button" name="addel" id="addel" onclick="HUB.Resources.addAuthor();" value="<?php echo JText::_('Add'); ?>" />

<ul id="author-list">
<?php 
if ($this->authnames != NULL) 
{
	foreach ($this->authnames as $authname)
	{
		if ($authname->name) 
		{
			$name = $authname->name;
		} 
		else 
		{
			$name = $authname->givenName . ' ';
			if ($authname->middleName != null) 
			{
				$name .= $authname->middleName . ' ';
			}
			$name .= $authname->surname;
		}

		$authIDs[] = $authname->authorid;

		$org = ($authname->organization) ? $this->escape($authname->organization) : $this->attribs->get($authname->authorid, '');
?>
	<li id="author_<?php echo $authname->authorid; ?>">
		<span class="handle"><?php echo JText::_('DRAG HERE'); ?></span> 
		<a class="state trash" data-parent="author_<?php echo $authname->authorid; ?>" href="#" onclick="HUB.Resources.removeAuthor('author_<?php echo $authname->authorid; ?>');return false;"><span><?php echo JText::_('remove'); ?></span></a>
		<?php echo $this->escape(stripslashes($name)); ?> (<?php echo $authname->authorid; ?>)
		<br /><?php echo JText::_('Affiliation'); ?>: <input type="text" name="<?php echo $authname->authorid; ?>_organization" value="<?php echo $org; ?>" />
		
		<select name="<?php echo $authname->id; ?>_role">
			<option value=""<?php if ($authname->role == '') { echo ' selected="selected"'; }?>><?php echo JText::_('Author'); ?></option>
<?php 
	if ($this->roles)
	{
		foreach ($this->roles as $role)
		{
?>
			<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($authname->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php
		}
	}
?>
		</select>
		<input type="hidden" name="<?php echo $authname->authorid; ?>_name" value="<?php echo $this->escape($name); ?>" />
	</li>
<?php
	}
}
?>
</ul>
<input type="hidden" name="old_authors" id="old_authors" value="<?php echo implode(',', $authIDs); ?>" />
<input type="hidden" name="new_authors" id="new_authors" value="<?php echo implode(',', $authIDs); ?>" />

<script src="/media/system/js/jquery.js"></script>
<script src="/media/system/js/jquery.ui.js"></script>
<script src="/media/system/js/jquery.noconflict.js"></script>
<script src="components/com_resources/assets/js/authors.jquery.js"></script>
