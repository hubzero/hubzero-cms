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
//$database =& JFactory::getDBO();

$status = SupportHtml::getStatus($this->row->status);

$fstring = urlencode(trim($this->filters['_find']));
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
<?php
$targetuser = null;
if ($this->row->login) {
	$targetuser =& JUser::getInstance($this->row->login);
}
if (is_object($targetuser) && $targetuser->id) {
	?>
	<h3><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$this->row->created, '%d %b, %Y',0).' '.JText::_('AT').' '.JHTML::_('date', $this->row->created, '%I:%M %p',0).' '.JText::_('BY'); ?> <a href="<?php echo JRoute::_('index.php?option=com_members&id='.$targetuser->id); ?>"><?php echo ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name; ?></a></h3>
	<?php
} else {
	?>
	<h3><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$this->row->created, '%d %b, %Y',0).' '.JText::_('AT').' '.JHTML::_('date', $this->row->created, '%I:%M %p',0).' '.JText::_('BY'); ?> <a href="mailto:<?php echo $this->row->email; ?>"><?php echo ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name; ?></a></h3>
	<?php
}
?>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><?php
		if ($this->row->prev) {
			echo '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=ticket&id='. $this->row->prev).'?find='.$fstring.'">'.JText::_('PREVIOUS_TICKET').'</a>';
		} else {
			echo '<span>'.JText::_('PREVIOUS_TICKET').'</span>';
		}
		?></li>
<?php if (!$juser->get('guest')) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=tickets').'?'.$fstring; ?>"><?php echo JText::_('TICKETS'); ?></a></li>
<?php } ?>
		<li class="last"><?php
		if ($this->row->next) {
			echo '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=ticket&id='. $this->row->next) .'?find='.$fstring.'">'.JText::_('NEXT_TICKET').'</a>';
		} else {
			echo '<span>'.JText::_('NEXT_TICKET').'</span>';
		}
		?></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php if ($this->getError()) { ?>
<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<div class="overview section">
	<div class="aside">
		<p><?php echo ($this->row->status == 2) ? '<strong class="closed">'.JText::_('TICKET_STATUS_CLOSED_TICKET').'</strong>' : '<strong class="open">'.JText::_('TICKET_STATUS_OPEN_TICKET').'</strong>'; ?></p>
		<p><?php echo JText::_('TICKET'); ?> #: <strong><?php echo $this->row->id; ?></strong></p>
<?php
if ($this->comments) {
	$lc = end($this->comments);
?>
		<p><?php echo JText::_('TICKET_LAST_ACTIVITY'); ?>: <strong><?php echo SupportHtml::timeAgo($lc->created); ?> ago</strong></p>
<?php
}
?>
	</div><!-- / .aside -->

	<div class="subject">
		<?php 
		if ($this->row->summary) {
			echo '<h4>'.$this->row->summary.'</h4>'."\n";
		}
		?>
		<blockquote cite="<?php echo ($this->row->login) ? $this->row->login : $this->row->name; ?>">
			<p><?php echo $this->row->report; ?></p>
		</blockquote>

		<table id="ticket-details" summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
			<caption id="toggle-details"><?php echo JText::_('TICKET_DETAILS'); ?></caption>
			<tbody id="ticket-details-body" class="hide">
				<tr>
					<th><?php echo JText::_('TICKET_DETAILS_EMAIL'); ?>:</th>
					<td><?php echo $this->row->email; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_TAGS'); ?>:</td>
					<td><?php echo $this->lists['tagcloud']; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('TICKET_DETAILS_SEVERITY'); ?>:</th>
					<td><?php echo $this->row->severity; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('TICKET_DETAILS_OWNER'); ?>:</th>
					<td><?php echo ($this->row->owner) ? $this->row->owner : 'none'; ?></td>
				</tr>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
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
<?php } ?>
			</tbody>
		</table>
	</div><!-- / .subject -->
</div><!-- / .section -->

<?php if ($this->acl->check('read','comments')) { ?>
<div class="main section">
	<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>		
	<div class="aside">
<?php if ($this->acl->check('create','comments')) { ?>
		<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
<?php } ?>
	</div><!-- / .aside -->

	<div class="subject">
		<ol class="comments">
<?php 
		if (count($this->comments) > 0) { 
			$o = 'even';
			$i = 0;
			$html = '';
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
				
				$name = 'Unknown';
				if ($comment->created_by) {
					$juseri =& JUser::getInstance( $comment->created_by );
					if (is_object($juseri)) {
						$name = $juseri->get('name');
					}
				}
				
				$o = ($o == 'odd') ? 'even' : 'odd';
				
				$html .= t.t.t.t.t.'<li class="';
				$html .= $access.' comment '.$o.'" id="c'.$comment->id.'">'.n;
				$html .= t.t.t.t.t.t.'<dl class="comment-details">'.n;
				$html .= t.t.t.t.t.t.t.'<dt class="type"><span><span>'.$access.' comment</span></span></dt>'.n;
				$html .= t.t.t.t.t.t.t.'<dd class="date">'.JHTML::_('date',$comment->created, '%d %b, %Y',0).'</dd>'.n;
				$html .= t.t.t.t.t.t.t.'<dd class="time">'.JHTML::_('date',$comment->created, '%I:%M %p',0).'</dd>'.n;
				$html .= t.t.t.t.t.t.'</dl>'.n;
				$html .= t.t.t.t.t.t.'<div class="cwrap">'.n;
				$html .= t.t.t.t.t.t.t.'<p class="name"><strong>'. $name.' ('.$comment->created_by .')</strong></p>'.n;
				if ($comment->comment) {
					/*$comment->comment = str_replace("<br />","",$comment->comment);
					$comment->comment = htmlentities(stripslashes($comment->comment));
					$comment->comment = nl2br($comment->comment);
					$comment->comment = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$comment->comment);*/
					
					$html .= t.t.t.t.t.t.t.'<blockquote cite="'. $comment->created_by .'">'.n;
					if (strstr( $comment->comment, '</p>' ) || strstr( $comment->comment, '<pre class="wiki">' )) {
						$html .= t.t.t.t.t.t.t.t.$comment->comment.n;
					} else {
						$html .= t.t.t.t.t.t.t.t.'<p>'.$comment->comment.'</p>'.n;
					}
					$html .= t.t.t.t.t.t.t.'</blockquote>'.n;
				}
				$html .= '<div class="changelog">'.$comment->changelog.'</div>';
				$html .= t.t.t.t.t.t.'</div>'.n;
				$html .= t.t.t.t.t.'</li>'.n;
			}
			echo $html;
			if ($i == 0) {
?>
			<li>
				<p>No comments found.</p>
			</li>
<?php
			}
		} else { ?>
			<li>
				<p>No comments found.</p>
			</li>
<?php } ?>
		</ol>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
<?php } // ACL can read comments ?>

<?php if ($this->acl->check('create','comments') || $this->acl->check('update','tickets')) { ?>
<div class="section">
	<div class="aside">
		<p>Please remember to describe problems in detail, including any steps you may have taken before encountering an error.</p>
	</div><!-- / .aside -->
	
	<div class="subject">
		<form action="index.php" method="post" id="hubForm" enctype="multipart/form-data">
			<h4><a name="commentform"></a><?php echo JText::_('COMMENT_FORM'); ?></h4>
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="ticket[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
				<input type="hidden" name="find" value="<?php echo htmlentities(urldecode($fstring), ENT_QUOTES); ?>" />
<?php if (!$this->acl->check('create','private_comments')) { ?>
				<input type="hidden" name="access" value="0" />
<?php } ?>

<?php if ($this->acl->check('update','tickets')) { ?>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
				<div class="group">
					<label>
						<?php echo JText::_('COMMENT_TAGS'); ?>:<br />
<?php 
JPluginHelper::importPlugin( 'tageditor' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$this->lists['tags'],'')) );

if (count($tf) > 0) {
	echo $tf[0];
} else { ?>
						<input type="text" name="tags" id="tags" value="<?php echo $this->lists['tags']; ?>" size="35" />
<?php } ?>
					</label>
					<label>
						<?php echo JText::_('COMMENT_SEVERITY'); ?>:
						<?php echo SupportHtml::selectArray('ticket[severity]',$this->lists['severities'],$this->row->severity); ?>
					</label>
				</div>
				<div class="clear"></div>

				<div class="group threeup">
					<label>
						<?php echo JText::_('COMMENT_GROUP'); 
						$document =& JFactory::getDocument();
						$document->addScript('components'.DS.'com_support'.DS.'observer.js');
						$document->addScript('components'.DS.'com_support'.DS.'autocompleter.js');
						$document->addStyleSheet('components'.DS.'com_support'.DS.'autocompleter.css');
						?>:
						<input type="text" name="ticket[group]" value="<?php echo $this->row->group; ?>" id="acgroup" value="" autocomplete="off" />
					</label>

					<label>
						<?php echo JText::_('COMMENT_OWNER'); ?>:
						<?php echo $this->lists['owner']; ?>
					</label>
<?php } // ACL can update ticket (admin) ?>
					<label>
						<?php echo JText::_('COMMENT_STATUS'); ?>:
						<select name="ticket[resolved]" id="status">
						<?php 
						$html  = t.'<option value=""';
						if ($this->row->status == 0 || $this->row->resolved == '') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_OPEN').'</option>'.n;
						$html .= t.'<option value="1"';
						if ($this->row->status == 1) {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_WAITING').'</option>'.n;
						if ($this->acl->check('update','tickets') > 0) {
							$html .= t.'<optgroup label="Closed">'.n;
						}
						$html .= t.t.'<option value="noresolution"';
						if ($this->row->status == 2 && $this->row->resolved == 'noresolution') {
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('COMMENT_OPT_CLOSED').'</option>'.n;
						if ($this->acl->check('update','tickets') > 0) {
							if (isset($this->lists['resolutions']) && $this->lists['resolutions']!='') {
								foreach ($this->lists['resolutions'] as $anode) 
								{
									$selected = ($anode->alias == $this->row->resolved)
										  ? ' selected="selected"'
										  : '';
									$html .= t.t.'<option value="'.$anode->alias.'"'.$selected.'>'.stripslashes($anode->title).'</option>'.n;
								}
							}
							$html .= t.'</optgroup>'.n;
						}
						echo $html;
						?>
						</select>
					</label>
<?php if ($this->acl->check('update','tickets') > 0) { ?>
				</div>
				<div class="clear"></div>
<?php } // ACL can update ticket (admin) ?>
<?php } // ACL can update tickets ?>
<?php if ($this->acl->check('create','comments') || $this->acl->check('create','private_comments')) { ?>
				<fieldset>
					<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
<?php if ($this->acl->check('create','comments') > 0 || $this->acl->check('create','private_comments')) { ?>
					<div class="top group">
<?php } ?>
<?php if ($this->acl->check('create','comments') > 0) { ?>
						<label>
							<?php
							$hi = array();
							$o  = '<select name="messages" id="messages">'.n;
							$o .= t.'<option value="mc">'.JText::_('COMMENT_CUSTOM').'</option>'.n;
							$jconfig =& JFactory::getConfig();
							foreach ($this->lists['messages'] as $message)
							{
								$message->message = str_replace('"','&quot;',$message->message);
								$message->message = str_replace('&quote;','&quot;',$message->message);
								$message->message = str_replace('#XXX','#'.$this->row->id,$message->message);
								$message->message = str_replace('{ticket#}',$this->row->id,$message->message);
								$message->message = str_replace('{sitename}',$jconfig->getValue('config.sitename'),$message->message);
								$message->message = str_replace('{siteemail}',$jconfig->getValue('config.mailfrom'),$message->message);

								$o .= t.'<option value="m'.$message->id.'">'.stripslashes($message->title).'</option>'."\n";

								$hi[] = '<input type="hidden" name="m'.$message->id.'" id="m'.$message->id.'" value="'.htmlentities(stripslashes($message->message), ENT_QUOTES).'" />'.n;
							}
							$o .= '</select>'.n;
							$hi = implode(n,$hi);
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
					<div class="group">
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
					<div class="group">
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
				<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT_COMMENT'); ?>" /></p>
			</fieldset>
		</form>
	</div><!-- / .subject -->
</div><!-- / .section -->
<?php } // ACL can create comments ?>
<div class="clear"></div>