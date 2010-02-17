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
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<div class="aside">
		<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></p>
	</div><!-- / .aside -->
	<div class="subject">
		<h3><?php echo stripslashes($this->forum->topic); ?></h3>
		<ol class="comments">
<?php
		if ($this->rows) {
			ximport('wiki.parser');

			$p = new WikiParser( $this->group->get('cn'), $this->option, 'group'.DS.'forum', 'group', $this->group->get('gidNumber'), '' );
			
			$o = 'even';
			
			foreach ($this->rows as $row) 
			{
				$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
				if (!$row->anonymous) {
					$juser =& JUser::getInstance( $row->created_by );
					if (is_object($juser) && $juser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.stripslashes($juser->get('name')).'</a>';
					}
				}
				
				$comment = $p->parse( "\n".stripslashes($row->comment) );
				
				$o = ($o == 'odd') ? 'even' : 'odd';
?>
			<li class="comment <?php echo $o; ?>" id="c<?php echo $row->id; ?>">
				<a name="c<?php echo $row->id; ?>"></a>
				<dl class="comment-details">
					<dt class="type"><span class="plaincomment"><span><?php echo JText::_('PLG_GROUPS_FORUM_COMMENT'); ?></span></span></dt>
					<dd class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y', 0); ?></dd>
					<dd class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></dd>
				</dl>
				<div class="cwrap">
					<p class="name"><strong><?php echo $name; ?></strong> <?php echo JText::_('PLG_GROUPS_FORUM_SAID'); ?>:</p>
					<p><?php echo $comment; ?></p>
				</div>
			</li>
<?php
			}
		} else {
?>
			<li>
				<p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p>
			</li>
<?php
		}
?>
		</ol>
		<?php echo $this->pageNav->getListFooter(); ?>
	</div><!-- / .subject -->
</form>

<div class="clear"></div>
<hr />

<div class="aside">
	<p>Comments support <a href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki Formatting</a>. Please keep comments polite and on topic. Offensive posts may be removed.</p>
</div><!-- / .aside -->
<div class="subject">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post" id="hubForm">
		<fieldset>
			<h4><a name="commentform"></a><?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?></h4>
			<label>
				<input class="option" type="checkbox" name="anonymous" id="forum_anonymous" value="1" /> 
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
			</label>
			<label>
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
				<textarea name="comment" id="forum_comments" rows="15" cols="35"></textarea>
			</label>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="savetopic" />
			<input type="hidden" name="parent" value="<?php echo $this->forum->id; ?>" />
			<input type="hidden" name="topic_id" value="" />
			<input type="hidden" name="active" value="forum" />
			<p class="submit"><input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" /></p>
		</fieldset>
	</form>
</div><!-- / .subject -->