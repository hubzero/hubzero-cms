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

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';
?>
<ul id="page_options">
	<li>
		<a class="icon-archive archive btn" href="<?php echo JRoute::_($base); ?>" title="<?php echo JText::_('Archive'); ?>">
			<?php echo JText::_('Archive'); ?>
		</a>
	</li>
</ul>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_($base . '&action=savesettings'); ?>" method="post" id="hubForm" class="full">
		<fieldset class="settings">
			<legend><?php echo JText::_('Posts'); ?></legend>
			<p>Privacy settings can be set for individual posts when creating/editing them.</p>
		</fieldset>
		<fieldset class="settings">
			<legend><?php echo JText::_('Blog Settings'); ?></legend>

			<label for="param-posting">
				Who can post to this blog?
				<select name="params[posting]" id="param-posting">
					<option value="0"<?php if (!$this->config->get('posting', 0)) { echo ' selected="selected"'; }?>>All group members</option>
					<option value="1"<?php if ($this->config->get('posting', 0) == 1) { echo ' selected="selected"'; }?>>Group managers only</option>
				</select>
			</label>
			
			<p class="help">
				Group managers can add/edit/delete any post. Standard group members can only edit/delete <strong>their own</strong> posts.
			</p>
		</fieldset>
		<fieldset class="settings">
			<legend><?php echo JText::_('Feeds'); ?></legend>

			<label for="param-feeds_enabled">
				RSS Feed of entries
				<select name="params[feeds_enabled]" id="param-feeds_enabled">
					<option value="0"<?php if (!$this->config->get('feeds_enabled', 1)) { echo ' selected="selected"'; }?>>Disabled</option>
					<option value="1"<?php if ($this->config->get('feeds_enabled', 1) == 1) { echo ' selected="selected"'; }?>>Enabled</option>
				</select>
			</label>
			
			<label for="param-feeds_entries">
				The length of RSS feed entries
				<select name="params[feed_entries]" id="param-feeds_entries">
					<option value="full"<?php if ($this->config->get('feed_entries', 'partial') == 'full') { echo ' selected="selected"'; }?>>Full</option>
					<option value="partial"<?php if ($this->config->get('feed_entries', 'partial') == 'partial') { echo ' selected="selected"'; }?>>Partial</option>
				</select>
			</label>
			
			<p class="help">
				<strong>Note:</strong> Feeds will only contain content marked as <strong>public</strong>. This is because there is currently no way to generate secure or private feeds.
			</p>
			
			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<input type="hidden" name="settings[folder]" value="groups" />
			<input type="hidden" name="settings[element]" value="blog" />
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="action" value="savesettings" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_GROUPS_BLOG_SAVE'); ?>" />
			<a href="<?php echo JRoute::_($base); ?>">Cancel</a>
		</p>
	</form>
