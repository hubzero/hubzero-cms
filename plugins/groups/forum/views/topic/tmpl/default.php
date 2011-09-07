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

ximport('Hubzero_Wiki_Editor');
ximport('Hubzero_Wiki_Parser');
ximport('Hubzero_User_Profile');

$p =& Hubzero_Wiki_Parser::getInstance();
$editor =& Hubzero_Wiki_Editor::getInstance();
$juser =& JFactory::getUser();

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'group'.DS.'forum',
	'pagename' => 'group',
	'pageid'   => $this->group->get('gidNumber'),
	'filepath' => '',
	'domain'   => ''
);
?>
<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post">
		<h3 class="title"><?php echo stripslashes($this->forum->topic); ?></h3>
		<div class="aside">
			<?php if(in_array($this->juser->get('id'),$this->members)) { ?>
				<p class="add">
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>">
						<?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?>
					</a>
				</p>
			<?php } ?>
		</div><!-- / .aside -->
		
		<div class="subject">
			<ol class="comments">
			<?php
				if ($this->rows) {
					$o = 'even';
					$k = 0;
					foreach ($this->rows as $row)
					{
						$huser = "";
						$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
						if (!$row->anonymous) {
							$huser = new Hubzero_User_Profile();
							$huser->load( $row->created_by );
							if (is_object($huser) && $huser->get('name')) {
								$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.stripslashes($huser->get('name')).'</a>';
							}
						}

						$comment = $p->parse( "\n".stripslashes($row->comment), $wikiconfig );

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
			</li>

			<?php
						$k++;
					}
				} else {
			?>
				<li>
					<p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p>
				</li>
			<?php } ?>
			</ol>
                <?php 
                    // @FIXME: Nick's Fix Based on Resources View
                    echo '<input type="hidden" name="topic" value="' . $this->forum->id . '" />';
                    $pf = $this->pageNav->getListFooter();
                    $nm = str_replace('com_','',$this->option);
                    $pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?topic='. $this->forum->id . '&',$pf);
                    //echo $pf;
                    //echo $this->pageNav->getListFooter(); 
                    // @FIXME: End Nick's Fix
                ?>
			<br class="clear" />
	</div><!-- / .subject -->
</form>

<div class="clear"></div>

<h3 class="title">
	<a name="commentform"></a>
	<?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?>
</h3>
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
			<?php if(in_array($this->juser->get('id'),$this->members)) { ?>
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
					<?php echo $editor->display('topic[comment]', 'forum_comments', '', '', '35', '15'); ?>
				</label>
			
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="topic[anonymous]" id="forum_anonymous" value="1" /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
				</label>
			
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } else { ?>
				<?php
					if ($this->juser->get('guest')) {
						$rtrn = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$this->forum->id);
						echo "<p class=\"warning\">".JText::sprintf('PLG_GROUPS_FORUM_MUST_LOGIN_AND_MEMBER', base64_encode($rtrn), 'comment on a topic')."</p>";
					} else {
						echo "<p class=\"warning\">".JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'comment on a topic')."</p>";
					}
				?>
			<?php } ?>
			
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

