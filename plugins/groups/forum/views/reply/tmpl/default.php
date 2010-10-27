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
<div class="below">
<h3><a name="topicform"></a><?php echo $title; ?></h3>
	<div class="aside">
<?php if (!$this->row->parent && $this->row->id) { ?>
		<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></p>
<?php } ?>
		<table class="wiki-reference" summary="Wiki Syntax Reference">
			<caption>Wiki Syntax Reference</caption>
			<tbody>
				<tr>
					<td>'''bold'''</td>
					<td><b>bold</b></td>
				</tr>
				<tr>
					<td>''italic''</td>
					<td><i>italic</i></td>
				</tr>
				<tr>
					<td>__underline__</td>
					<td><span style="text-decoration:underline;">underline</span></td>
				</tr>
				<tr>
					<td>{{{monospace}}}</td>
					<td><code>monospace</code></td>
				</tr>
				<tr>
					<td>~~strike-through~~</td>
					<td><del>strike-through</del></td>
				</tr>
				<tr>
					<td>^superscript^</td>
					<td><sup>superscript</sup></td>
				</tr>
				<tr>
					<td>,,subscript,,</td>
					<td><sub>subscript</sub></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="subject">
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post" id="commentform">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<p class="comment-member-photo">
<?php
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			$jxuser = new Hubzero_User_Profile();
			$jxuser->load( $juser->get('id') );
			$thumb = ForumHelper::getMemberPhoto($jxuser, 0);
		} else {
			$config =& JComponentHelper::getParams( 'com_members' );
			$thumb = $config->get('defaultpic');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$dfthumb;
			}
			$thumb = ForumHelper::thumbit($thumb);
		}
?>
		<img src="<?php echo $thumb; ?>" alt="" />
	</p>
	<fieldset>
<?php if ($this->row->parent) { ?>
		<input type="hidden" name="topic[sticky]" id="forum_sticky" value="<?php echo $this->row->sticky; ?>" />
		<input type="hidden" name="topic[topic]" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" />
<?php } else { ?>
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
		<label>
			<input class="option" type="checkbox" name="topic[sticky]" value="1" id="forum_sticky"<?php if ($this->row->sticky == 1) { echo ' checked="checked"'; } ?> /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_STICKY'); ?>
		</label>	
<?php } else { ?>
		<input type="hidden" name="topic[sticky]" id="forum_sticky" value="<?php echo $this->row->sticky; ?>" />
<?php } ?>
			
		<label>
			<input class="option" type="checkbox" name="topic[access]" id="forum_access"<?php if ($this->row->access != 4) { echo ' checked="checked"'; } ?> /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ACCESS'); ?>
		</label>

		<label>
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_TOPIC'); ?>
			<input type="text" name="topic[topic]" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" size="38" />
		</label>
<?php } ?>
		<label>
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
			<textarea name="topic[comment]" id="forum_comments" rows="15" cols="35"><?php echo stripslashes($this->row->comment); ?></textarea>
		</label>
		
		<label id="comment-anonymous-label">
			<input class="option" type="checkbox" name="topic[anonymous]" id="forum_anonymous" value="1"<?php echo ($this->row->anonymous) ? ' checked="checked"' : ''; ?> /> 
			<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
		</label>
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
		</p>
		
		<div class="sidenote">
			<p>
				<strong>Please keep comments polite and on topic. Offensive posts may be removed.</strong>
			</p>
			<p>
				Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup 400x500">Wiki syntax</a> is supported.
			</p>
		</div>
	</fieldset>
	<input type="hidden" name="topic[created]" value="<?php echo $this->row->created; ?>" />
	<input type="hidden" name="topic[created_by]" value="<?php echo $this->row->created_by; ?>" />
	<input type="hidden" name="topic[parent]" value="<?php echo $this->row->parent; ?>" />
	<input type="hidden" name="topic[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="topic[group]" value="<?php echo $this->group->get('gidNumber'); ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="savetopic" />
	<input type="hidden" name="active" value="forum" />
</form>
</div><!-- / .subject -->
</div>