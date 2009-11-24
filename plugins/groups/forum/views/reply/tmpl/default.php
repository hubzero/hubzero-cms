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

if ($this->row->parent) {
	$title = JText::_('PLG_GROUPS_FORUM_ADD_REPLY_TO_TOPIC');
} else {
	if ($this->row->id) {
		$title = JText::_('PLG_GROUPS_FORUM_EDIT_TOPIC');
	} else {
		$this->row->access = 4;
		$title = JText::_('PLG_GROUPS_FORUM_NEW_TOPIC');
	}
}
?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post" id="hubForm">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<div class="explaination">
<?php if (!$this->row->parent && $this->row->id) { ?>
		<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></p>
<?php } ?>
		<p>Comments support <a href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki Formatting</a>. Please keep comments polite and on topic. Offensive posts may be removed.</p>
	</div>
	<fieldset>
		<h3><a name="topicform"></a><?php echo $title; ?></h3>

<?php if ($this->row->parent) { ?>
		<input type="hidden" name="sticky" id="forum_sticky" value="<?php echo $this->row->sticky; ?>" />
		<input type="hidden" name="topic" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" />
<?php } else { ?>
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
		<label>
			<input class="option" type="checkbox" name="sticky" id="forum_sticky"<?php if ($this->row->sticky == 1) { echo ' checked="checked"'; } ?> /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_STICKY'); ?>
		</label>	
<?php } else { ?>
		<input type="hidden" name="sticky" id="forum_sticky" value="<?php echo $this->row->sticky; ?>" />
<?php } ?>
			
		<label>
			<input class="option" type="checkbox" name="access" id="forum_access"<?php if ($this->row->access != 4) { echo ' checked="checked"'; } ?> /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ACCESS'); ?>
		</label>
			
		<label>
			<input class="option" type="checkbox" name="anonymous" id="forum_anonymous" value="1" /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
		</label>

		<label>
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_TOPIC'); ?>
			<input type="text" name="topic" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" size="38" />
		</label>
<?php } ?>
		<label>
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
			<textarea name="comment" id="forum_comments" rows="15" cols="35"><?php echo stripslashes($this->row->comment); ?></textarea>
		</label>
	</fieldset><div class="clear"></div>
	<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
	<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
	<input type="hidden" name="parent" value="<?php echo $this->row->parent; ?>" />
	<input type="hidden" name="topic_id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="group" value="<?php echo $this->group->get('gidNumber'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="savetopic" />
	<input type="hidden" name="active" value="forum" />
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
	</p>
</form>