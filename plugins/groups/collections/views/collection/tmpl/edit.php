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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

if (!$this->entry->exists())
{
	$legend = 'New collection';
}
else
{
	$legend = 'Edit collection';
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo JRoute::_($base . '&scope=save'); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo JText::_($legend); ?></legend>

		<label for="field-access">
			<?php echo JText::_('Privacy'); ?>
			<select name="fields[access]" id="field-access">
				<option value="0"<?php if ($this->entry->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public (anyone can see this collection)'); ?></option>
				<option value="1"<?php if ($this->entry->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Only registered users of the hub'); ?></option>
				<option value="4"<?php if ($this->entry->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('Only group members'); ?></option>
			</select>
		</label>

		<label for="field-title"<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
			<?php echo JText::_('Title'); ?>
			<input type="text" name="fields[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
		</label>

		<label for="field-description">
			<?php echo JText::_('Description'); ?> <span class="optional"><?php echo JText::_('optional'); ?></span>
			<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->entry->get('description'))); ?></textarea>
		</label>
	</fieldset>

	<input type="hidden" name="fields[id]" value="<?php echo $this->entry->get('id'); ?>" />
	<input type="hidden" name="fields[object_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
	<input type="hidden" name="fields[object_type]" value="group" />
	<input type="hidden" name="fields[created]" value="<?php echo $this->entry->get('created'); ?>" />
	<input type="hidden" name="fields[created_by]" value="<?php echo $this->entry->get('created_by'); ?>" />
	<input type="hidden" name="fields[state]" value="<?php echo $this->entry->get('state'); ?>" />
	<input type="hidden" name="fields[access]" value="<?php echo $this->params->get('access-plugin'); ?>" />

	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="action" value="savecollection" />
		
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_SAVE'); ?>" />
	</p>
</form>
