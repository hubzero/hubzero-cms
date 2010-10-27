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
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog&task=savesettings'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p>Privacy settings can be set for individual posts when creating/editing them.</p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('Blog Settings'); ?></h3>
			
			<label>
				RSS Feed of entries
				<select name="params[feeds_enabled]">
					<option value="0"<?php if (!$this->config->get('feeds_enabled')) { echo ' selected="selected"'; }?>>Disabled</option>
					<option value="1"<?php if ($this->config->get('feeds_enabled') == 1) { echo ' selected="selected"'; }?>>Enabled</option>
				</select>
			</label>
			
			<label>
				The length of RSS feed entries
				<select name="params[feed_entries]">
					<option value="full"<?php if ($this->config->get('feed_entries') == 'full') { echo ' selected="selected"'; }?>>Full</option>
					<option value="partial"<?php if ($this->config->get('feed_entries') == 'partial') { echo ' selected="selected"'; }?>>Partial</option>
				</select>
			</label>
			
			<p class="help">
				<strong>Note:</strong> Feeds will only contain content marked as <strong>public</strong>. This is because there is currently no way to generate secure or private feeds.
			</p>
			
			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->member->get('uidNumber'); ?>" />
			<input type="hidden" name="settings[folder]" value="members" />
			<input type="hidden" name="settings[element]" value="blog" />
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="action" value="savesettings" />
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_BLOG_SAVE'); ?>" />
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog'); ?>">Cancel</a>
		</p>
	</form>