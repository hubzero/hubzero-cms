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

$juser =& JFactory::getUser();
?>
<div class="below">
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<h3><?php echo stripslashes($this->forum->topic); ?></h3>
	<div class="aside">
		<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></p>
	</div><!-- / .aside -->
	<div class="subject">
		<ol class="comments">
<?php
		if ($this->rows) {
			ximport('wiki.parser');
			ximport('Hubzero_User_Profile');

			$p = new WikiParser( $this->group->get('cn'), $this->option, 'group'.DS.'forum', 'group', $this->group->get('gidNumber'), '' );
			
			$o = 'even';
			$k = 0;
			foreach ($this->rows as $row) 
			{
				$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
				if (!$row->anonymous) {
					//$juser =& JUser::getInstance( $row->created_by );
					$huser = new Hubzero_User_Profile();
					$huser->load( $row->created_by );
					if (is_object($huser) && $huser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.stripslashes($huser->get('name')).'</a>';
					}
				}
				
				$comment = $p->parse( "\n".stripslashes($row->comment) );
				
				$o = ($o == 'odd') ? 'even' : 'odd';
?>
			<li class="comment <?php echo $o; if ($k == 0) { echo ' author'; } ?>" id="c<?php echo $row->id; ?>">
				<a name="c<?php echo $row->id; ?>"></a>
				<p class="comment-member-photo">
					<img src="<?php echo ForumHelper::getMemberPhoto($huser, $row->anonymous); ?>" alt="" />
				</p>
				<div class="comment-content">
					<p class="comment-title">
						<strong><?php echo $name; ?></strong> 
						<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$this->forum->id.'#c'.$row->id); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_PERMALINK'); ?>">@ 
							<span class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on 
							<span class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y', 0); ?></span>
<?php if ($row->modified && $row->modified != '0000-00-00 00:00:00') { ?>
							&mdash; <?php echo JText::_('Edited @'); ?>
							<span class="time"><?php echo JHTML::_('date',$row->modified, '%I:%M %p', 0); ?></span> on 
							<span class="date"><?php echo JHTML::_('date',$row->modified, '%d %b, %Y', 0); ?></span>
<?php } ?>
						</a>
					</p>
					<?php echo $comment; ?>
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager' || $juser->get('id') == $row->created_by) { ?>
					<p class="comment-options">
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
						<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id.'&task=deletetopic'); ?>">Delete</a> | 
<?php } ?>
						<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id.'&task=edittopic'); ?>">Edit</a>
					</p>
<?php } ?>
				</div>
				<?php /*if ($k == 0) { ?>
				<ol class="comments">
				<?php } else if ($k == (count($this->rows) - 1)) { ?>
					</li>
				</ol>
			</li>
				<?php } else { ?>
			</li>
				<?php }*/ ?>
			</li>
<?php
				$k++;
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
                <?php 
                    // @FIXME: Nick's Fix Based on Resources View
                    echo '<input type="hidden" name="topic" value="' . $this->forum->id . '" />';
                    $pf = $this->pageNav->getListFooter();
                    $nm = str_replace('com_','',$this->option);
                    $pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?topic='. $this->forum->id . '&',$pf);
                    echo $pf;
                    //echo $this->pageNav->getListFooter(); 
                    // @FIXME: End Nick's Fix
                ?>

	</div><!-- / .subject -->
</form>

<div class="clear"></div>

<h3><a name="commentform"></a><?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?></h3>

<div class="aside">
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
</div><!-- / .aside -->
<div class="subject">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post" id="commentform">
		<p class="comment-member-photo">
<?php
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
			<label>
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
				<textarea name="topic[comment]" id="forum_comments" rows="15" cols="35"></textarea>
			</label>
			
			<label id="comment-anonymous-label">
				<input class="option" type="checkbox" name="topic[anonymous]" id="forum_anonymous" value="1" /> 
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
			</label>
			
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
			</p>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="savetopic" />
			<input type="hidden" name="topic[parent]" value="<?php echo $this->forum->id; ?>" />
			<input type="hidden" name="topic[id]" value="" />
			<input type="hidden" name="active" value="forum" />
			
			<div class="sidenote">
				<p>
					<strong>Please keep comments polite and on topic. Offensive posts may be removed.</strong>
				</p>
				<p>
					Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup 400x500">Wiki syntax</a> is supported.
				</p>
			</div>
		</fieldset>
	</form>
</div><!-- / .subject -->
</div>
