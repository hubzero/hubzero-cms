<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$juser =& JFactory::getUser();
//$database =& JFactory::getDBO();

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

$status = SupportHtml::getStatus($this->row->status);

$fstring = urlencode(trim($this->filters['_find']));
?>
<div id="content-header">
	<h2><?php echo $this->title; ?>: #<?php echo $this->row->id; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><?php
		if ($this->row->prev) {
			echo '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=ticket&id='. $this->row->prev.'&find='.$fstring.'&limit='.$this->filters['limit'].'&limitstart='.$this->filters['start']).'">'.JText::_('PREVIOUS_TICKET').'</a>';
		} else {
			echo '<span>'.JText::_('PREVIOUS_TICKET').'</span>';
		}
		?></li>
<?php if (!$juser->get('guest')) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=tickets&find='.$fstring.'&limit='.$this->filters['limit'].'&limitstart='.$this->filters['start']); ?>"><?php echo JText::_('TICKETS'); ?></a></li>
<?php } ?>
		<li class="last"><?php
		if ($this->row->next) {
			echo '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=ticket&id='. $this->row->next.'&find='.$fstring.'&limit='.$this->filters['limit'].'&limitstart='.$this->filters['start']).'">'.JText::_('NEXT_TICKET').'</a>';
		} else {
			echo '<span>'.JText::_('NEXT_TICKET').'</span>';
		}
		?></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php if ($this->getError()) { ?>
<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<div class="main section">
	<div class="aside">
		<div class="ticket-status">
			<p class="<?php echo ($this->row->status == 2) ? 'closed' : 'open'; ?>"><strong><?php echo ($this->row->status == 2) ? JText::_('TICKET_STATUS_CLOSED_TICKET') : JText::_('TICKET_STATUS_OPEN_TICKET'); ?></strong></p>
<?php if ($this->row->status == 2) { ?>
			<p><strong>Note:</strong> To reopen this issue, add a comment below.</p>
<?php } ?>
			<!-- <p class="ticket-number">#<strong><?php echo $this->row->id; ?></strong></p> -->
<?php
/*if ($this->comments) {
	$lc = end($this->comments);
?>
		<p><?php echo JText::_('TICKET_LAST_ACTIVITY'); ?>: <strong><?php echo SupportHtml::timeAgo($lc->created); ?> ago</strong></p>
<?php
}*/
?>
		</div><!-- / .ticket-status -->
	</div><!-- / .aside -->

	<div class="subject">
		<?php
		$unknown = 1;
		$name = JText::_('Unknown');
		$submitter = new Hubzero_User_Profile();
		if ($this->row->login) {
			//$juseri =& JUser::getInstance( $comment->created_by );
			$submitter->load( $this->row->login );
			if (is_object($submitter) && $submitter->get('name')) {
				$name = '<a rel="profile" href="'.JRoute::_('index.php?option=com_members&id='.$submitter->get('uidNumber')).'">'.stripslashes($submitter->get('name')).'</a>';
				$unknown = 0;
			} else {
				$name  = '<a rel="email" href="mailto:'. $this->row->email .'">';
				$name .= ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name;
				$name .= '</a>';
			}
		} else {
			$name  = '<a rel="email" href="mailto:'. $this->row->email .'">';
			$name .= ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name;
			$name .= '</a>';
		}
		?>
		<div class="ticket" id="t<?php echo $this->row->id; ?>">
			<p class="ticket-member-photo">
				<span class="ticket-anchor"><a name="ticket"></a></span>
				<img src="<?php echo SupportHtml::getMemberPhoto($submitter, $unknown); ?>" alt="" />
			</p><!-- / .ticket-member-photo -->
			<div class="ticket-content">
				<p class="ticket-title">
					<strong><?php echo $name; ?></strong> 
					<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=ticket&id='.$this->row->id); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">@ 
						<span class="time"><?php echo JHTML::_('date',$this->row->created, '%I:%M %p', 0); ?></span> on 
						<span class="date"><?php echo JHTML::_('date',$this->row->created, '%d %b, %Y', 0); ?></span>
					</a>
				</p><!-- / .ticket-title -->
				<p><?php echo preg_replace('/  /', ' &nbsp;', $this->row->report); ?></p>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
				<table id="ticket-details" summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
					<caption id="toggle-details"><?php echo JText::_('TICKET_DETAILS'); ?></caption>
					<tbody id="ticket-details-body" class="hide">
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_OS'); ?>:</th>
							<td><?php echo $this->row->os; ?> / <?php echo $this->row->browser; ?> (<?php echo ($this->row->cookies) ? JText::_('COOKIES_ENABLED') : JText::_('COOKIES_DISABLED'); ?>)</td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_IP'); ?>:</th>
							<td><?php echo $this->row->ip; ?> (<?php echo $this->row->hostname; ?>)</td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_REFERRER'); ?>:</th>
							<td><?php echo ($this->row->referrer) ? $this->row->referrer : ' '; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_INSTANCES'); ?>:</th>
							<td><?php echo $this->row->instances; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_UASTRING'); ?>:</th>
							<td><?php echo $this->row->uas; ?></td>
						</tr>
					</tbody>
				</table>
<?php } ?>
			</div><!-- / .ticket-content -->
		</div><!-- / .ticket -->
	</div><!-- / .subject -->
</div><!-- / .main section -->

<?php if ($this->acl->check('read','comments')) { ?>
<div class="below section">
	<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>
			
	<div class="aside">
<?php if ($this->acl->check('create','comments')) { ?>
		<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
<?php } ?>
	</div><!-- / .aside -->

	<div class="subject">
<?php if (count($this->comments) > 0) { ?>
		<ol class="comments">
<?php
			ximport('Hubzero_User_Profile');
			$o = 'even';
			$i = 0;
			foreach ($this->comments as $comment) 
			{
				if (!$this->acl->check('read','private_comments') && $comment->access == 1) {
					continue;
				}
				$i++;
				
				if ($comment->access == 1) { 
					$access = 'private';
				} else {
					$access = 'public';
				}
				if ($comment->created_by == $this->row->login && $comment->access != 1) {
					$access = 'submitter';
				}
				
				$name = JText::_('Unknown');
				if ($comment->created_by) {
					//$juseri =& JUser::getInstance( $comment->created_by );
					$juseri = new Hubzero_User_Profile();
					$juseri->load( $comment->created_by );
					if (is_object($juseri) && $juseri->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$juseri->get('uidNumber')).'">'.stripslashes($juseri->get('name')).'</a>';
					}
				}
				
				$o = ($o == 'odd') ? 'even' : 'odd';
?>
			<li class="comment <?php echo $access.' '.$o; ?>" id="c<?php echo $comment->id; ?>">
				<p class="comment-member-photo">
					<span class="comment-anchor"><a name="c<?php echo $comment->id; ?>"></a></span>
					<img src="<?php echo SupportHtml::getMemberPhoto($juseri, 0); ?>" alt="" />
				</p>
				<div class="comment-content">
					<p class="comment-title">
						<strong><?php echo $name; ?></strong>
						<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=ticket&id='.$this->row->id.'#c'.$comment->id); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">@ 
							<span class="time"><?php echo JHTML::_('date',$comment->created, '%I:%M %p', 0); ?></span> on 
							<span class="date"><?php echo JHTML::_('date',$comment->created, '%d %b, %Y', 0); ?></span>
						</a>
					</p>
<?php 
				if ($comment->comment) {
?>
					<p><?php echo $comment->comment; ?></p>
<?php
				}
?>
					<div class="changelog">
						<?php echo $comment->changelog; ?>
					</div><!-- / .changelog -->
				</div><!-- / .comment-content -->
			</li>
<?php
			}
?>
		</ol>
<?php } else { ?>
		<p class="no-comments">No comments found.</p>
<?php } ?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->
<?php } // ACL can read comments ?>

<?php if ($this->acl->check('create','comments') || $this->acl->check('update','tickets')) { ?>
<div class="below section">
	<h3>
		<?php echo JText::_('COMMENT_FORM'); ?>
	</h3>
	
	<div class="aside">
		<p>Please remember to describe problems in detail, including any steps you may have taken before encountering an error.</p>
	</div><!-- / .aside -->
	
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<span class="comment-anchor"><a name="commentform"></a></span>
			<?php
				if (!$juser->get('guest')) {
					$jxuser = new Hubzero_User_Profile();
					$jxuser->load( $juser->get('id') );
					$thumb = SupportHtml::getMemberPhoto($jxuser, 0);
				} else {
					$config =& JComponentHelper::getParams( 'com_members' );
					$thumb = $config->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$dfthumb;
					}
					$thumb = SupportHtml::thumbit($thumb);
				}
			?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="ticket[id]" id="ticketid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
				<input type="hidden" name="find" value="<?php echo htmlentities(urldecode($fstring), ENT_QUOTES); ?>" />
<?php if (!$this->acl->check('create','private_comments')) { ?>
				<input type="hidden" name="access" value="0" />
<?php } ?>

<?php if ($this->acl->check('update','tickets')) { ?>
				<fieldset>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
					<legend>Ticket Details</legend>
					<label>
						<?php echo JText::_('COMMENT_TAGS'); ?>:<br />
						<?php 
					$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->lists['tags'])) );

					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" id="tags" value="<?php echo $this->lists['tags']; ?>" size="35" />
					<?php } ?>
					</label>

				<div class="grouping">
					<label>
						<?php echo JText::_('COMMENT_GROUP'); ?>:
						<?php 
					$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'ticket[group]', 'acgroup','',$this->row->group,'','ticketowner')) );
					if (count($gc) > 0) {
						echo $gc[0];
					} else { ?>
						<input type="text" name="ticket[group]" value="<?php echo $this->row->group; ?>" id="acgroup" value="" autocomplete="off" />
					<?php } ?>
					</label>

					<label>
						<?php echo JText::_('COMMENT_OWNER'); ?>:
						<?php echo $this->lists['owner']; ?>
					</label>
				</div>
				<div class="clear"></div>

				<div class="grouping">
					<label>
						<?php echo JText::_('COMMENT_SEVERITY'); ?>:
						<?php echo SupportHtml::selectArray('ticket[severity]',$this->lists['severities'],$this->row->severity); ?>
					</label>
<?php } // ACL can update ticket (admin) ?>
					<label>
						<?php echo JText::_('COMMENT_STATUS'); ?>:
						<select name="ticket[resolved]" id="status">
						<?php 
						$html  = '<option value=""';
						if ($this->row->status == 0 || $this->row->resolved == '') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_OPEN').'</option>'."\n";
						$html .= '<option value="1"';
						if ($this->row->status == 1) {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_WAITING').'</option>'."\n";
						$html .= '<optgroup label="Closed">'."\n";
						$html .= "\t".'<option value="noresolution"';
						if ($this->row->status == 2 && $this->row->resolved == 'noresolution') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_CLOSED').'</option>'."\n";
						if (isset($this->lists['resolutions']) && $this->lists['resolutions']!='') {
							foreach ($this->lists['resolutions'] as $anode) 
							{
								$selected = ($anode->alias == $this->row->resolved)
										  ? ' selected="selected"'
										  : '';
								$html .= "\t".'<option value="'.$anode->alias.'"'.$selected.'>'.stripslashes($anode->title).'</option>'."\n";
							}
						}
						$html .= "\t".'</optgroup>'."\n";
						echo $html;
						?>
						</select>
					</label>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
				</div>
<?php } ?>
				<div class="clear"></div>
				</fieldset>

<?php } // ACL can update tickets ?>
<?php if ($this->acl->check('create','comments') || $this->acl->check('create','private_comments')) { ?>
				<fieldset>
					<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
<?php if ($this->acl->check('create','comments') > 0 || $this->acl->check('create','private_comments')) { ?>
					<div class="top grouping">
<?php } ?>
<?php if ($this->acl->check('create','comments') > 0) { ?>
						<label>
							<?php
							$hi = array();
							$o  = '<select name="messages" id="messages">'."\n";
							$o .= "\t".'<option value="mc">'.JText::_('COMMENT_CUSTOM').'</option>'."\n";
							$jconfig =& JFactory::getConfig();
							foreach ($this->lists['messages'] as $message)
							{
								$message->message = str_replace('"','&quot;',$message->message);
								$message->message = str_replace('&quote;','&quot;',$message->message);
								$message->message = str_replace('#XXX','#'.$this->row->id,$message->message);
								$message->message = str_replace('{ticket#}',$this->row->id,$message->message);
								$message->message = str_replace('{sitename}',$jconfig->getValue('config.sitename'),$message->message);
								$message->message = str_replace('{siteemail}',$jconfig->getValue('config.mailfrom'),$message->message);

								$o .= "\t".'<option value="m'.$message->id.'">'.stripslashes($message->title).'</option>'."\n";

								$hi[] = '<input type="hidden" name="m'.$message->id.'" id="m'.$message->id.'" value="'.htmlentities(stripslashes($message->message), ENT_QUOTES).'" />'."\n";
							}
							$o .= '</select>'."\n";
							$hi = implode("\n",$hi);
							echo $o.$hi;
							?>
						</label>
<?php } // ACL can create comment (admin) ?>
<?php if ($this->acl->check('create','private_comments')) { ?>
						<label>
							<input class="option" type="checkbox" name="access" id="make-private" value="1" />
							<?php echo JText::_('COMMENT_PRIVATE'); ?>
						</label>
<?php } // ACL can create private comments ?>
<?php if ($this->acl->check('create','comments') > 0 || $this->acl->check('create','private_comments')) { ?>
					</div>
					<div class="clear"></div>
<?php } // ACL can create comments (admin) or private comments ?>
					<textarea name="comment" id="comment" rows="13" cols="35"></textarea>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('COMMENT_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>
<?php } //if ($this->acl->check('create','comments') || $this->acl->check('create','private_comments')) { ?>
<?php if ($this->acl->check('create','comments') > 0) { ?>
				<fieldset>
					<legend><?php echo JText::_('COMMENT_LEGEND_EMAIL'); ?>:</legend>
					<div class="grouping">
						<label>
							<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" /> 
							<?php echo JText::_('COMMENT_SEND_EMAIL_SUBMITTER'); ?>
						</label>
						<label>
							<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" /> 
							<?php echo JText::_('COMMENT_SEND_EMAIL_OWNER'); ?>
						</label>
					</div>
					<div class="clear"></div>

					<label>
						<?php echo JText::_('COMMENT_SEND_EMAIL_CC'); ?>: <span class="hint"><?php echo JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
						<input type="text" name="cc" value="" size="38" />
					</label>
				</fieldset>
<?php } else { ?>
				<input type="hidden" name="email_submitter" id="email_submitter" value="1" />
				<input type="hidden" name="email_owner" id="email_owner" value="1" />
<?php } // ACL can create comments (admin) ?>
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('SUBMIT_COMMENT'); ?>" />
				</p>
			</fieldset>
		</form>
	</div><!-- / .subject -->
</div><!-- / .section -->
<?php } // ACL can create comments ?>
<div class="clear"></div>
