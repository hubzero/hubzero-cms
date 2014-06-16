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
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') .'&active=collections&action=savesettings'); ?>" method="post" id="hubForm" class="full">

		<fieldset class="settings">
			<legend><?php echo JText::_('PLG_GROUPS_COLLECTIONS'); ?></legend>

			<label for="param-posting">
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS'); ?>
				<select name="params[create_collection]" id="param-create_collection">
					<option value="0"<?php if (!$this->params->get('create_collection', 1)) { echo ' selected="selected"'; }?>><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_ALL'); ?></option>
					<option value="1"<?php if ($this->params->get('create_collection', 1) == 1) { echo ' selected="selected"'; }?>><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_MANAGERS'); ?></option>
				</select>
			</label>

			<p class="info">
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_INFO'); ?>
			</p>
		</fieldset>
		<fieldset class="settings">
			<legend><?php echo JText::_('PLG_GROUPS_COLLECTIONS_POSTS'); ?></legend>

			<label for="param-posting">
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS'); ?>
				<select name="params[create_post]" id="param-create_post">
					<option value="0"<?php if (!$this->params->get('create_post', 0)) { echo ' selected="selected"'; }?>><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_ALL'); ?></option>
					<option value="1"<?php if ($this->params->get('create_post', 0) == 1) { echo ' selected="selected"'; }?>><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_MANAGERS'); ?></option>
				</select>
			</label>

			<p class="info">
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_INFO'); ?>
			</p>

			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<input type="hidden" name="settings[folder]" value="groups" />
			<input type="hidden" name="settings[element]" value="collections" />
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="collections" />
		<input type="hidden" name="action" value="savesettings" />

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=collections'); ?>"><?php echo JText::_('PLG_GROUPS_COLLECTIONS_CANCEL'); ?></a>
		</p>
	</form>
