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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Ticket' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$juser =& JFactory::getUser();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

/*if ($this->filters['_show'] != '') {
	$fstring = urlencode(trim($this->filters['_show']));
} else {
	$fstring = urlencode(trim($this->filters['_find']));
}*/
?>
<?php if ($this->row->id) { ?>
<h3><?php echo JText::_('TICKET'); echo ($this->row->id) ? ' #'.$this->row->id : ''; ?></h3>

<?php
		if ($this->row->id) {
			echo '<p id="prev-next">';
			$prv = $this->row->getTicketId('prev', $this->filters, 'admin');
			if ( $prv ) {
				echo '<a href="index.php?option='.$this->option.'&amp;task=edit&amp;id='. $prv .'">'.JText::_('PREVIOUS_TICKET').'</a>';
			} else {
				echo '<span style="color:#ccc;">'.JText::_('PREVIOUS_TICKET').'</span>';
			}
			echo ' &nbsp;&nbsp; ';
			$nxt = $this->row->getTicketId('next', $this->filters, 'admin');
			if ( $nxt ) {
				echo '<a href="index.php?option='.$this->option.'&amp;task=edit&amp;id='. $nxt .'">'.JText::_('NEXT_TICKET').'</a>';
			} else {
				echo '<span style="color:#ccc;">'.JText::_('NEXT_TICKET').'</span>';
			}
			echo '</p>';
		}
?>

<p><strong><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$this->row->created, '%d %b, %Y',0).' '.JText::_('AT').' '.JHTML::_('date', $this->row->created, '%I:%M %p',0).' '.JText::_('BY'); ?> <?php echo $this->row->name; echo ($this->row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]='.$this->row->login.'">'.$this->row->login.'</a>)' : ''; ?></strong></p>

<div class="col width-70">
	<div class="overview">
		<blockquote cite="<?php echo ($this->row->login) ? $this->row->name : $this->row->name; ?>">
			<p><?php echo preg_replace('/  /', ' &nbsp;', $this->row->report); ?></p>
		</blockquote>
		
		<table class="admintable" id="ticket-details" summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
			<caption id="toggle-details"><?php echo JText::_('TICKET_DETAILS'); ?></caption> 
			<tbody id="ticket-details-body" class="hide">
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_EMAIL'); ?>:</td>
					<td><a href="mailto:<?php echo $this->row->email; ?>"><?php echo $this->row->email; ?></a></td>
				</tr>
				<!-- <tr>
					<td class="key"><?php //echo JText::_('TICKET_DETAILS_SECTION'); ?>:</td>
					<td><?php //echo $this->row->section; ?></td>
				</tr>
				<tr>
					<td class="key"><?php //echo JText::_('TICKET_DETAILS_CATEGORY'); ?>:</td>
					<td><?php //echo $this->row->category; ?></td>
				</tr> -->
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_TAGS'); ?>:</td>
					<td><?php echo $this->lists['tagcloud']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_SEVERITY'); ?>:</td>
					<td><?php echo $this->row->severity; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_OWNER'); ?>:</td>
					<td><?php echo ($this->row->owner) ? $this->row->owner : ' '; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_OS'); ?>:</td>
					<td><?php echo $this->row->os; ?> / <?php echo $this->row->browser; ?> (<?php echo ($this->row->cookies) ? JText::_('COOKIES_ENABLED') : JText::_('COOKIES_DISABLED'); ?>)</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_IP'); ?>:</td>
					<td><?php echo $this->row->ip; ?> (<?php echo $this->row->hostname; ?>)</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_REFERRER'); ?>:</td>
					<td><?php echo ($this->row->referrer) ? $this->row->referrer : ' '; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_INSTANCES'); ?>:</td>
					<td><?php echo $this->row->instances; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('TICKET_DETAILS_UASTRING'); ?>:</td>
					<td><?php echo ($this->row->uas) ? $this->row->uas : '&nbsp;'; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="col width-30">
	<p><?php echo ($this->row->status == 2) ? '<strong class="closed">'.JText::_('TICKET_STATUS_CLOSED_TICKET').'</strong>' : '<strong class="open">'.JText::_('TICKET_STATUS_OPEN_TICKET').'</strong>'; ?></p>
</div>
<div class="clr"></div>

<?php if (count($this->comments) > 0) { ?>
<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>
<div class="col width-70">
<?php
			$o = 'even';
			$html  = "\t\t\t\t".'<ol class="comments">'."\n";
			foreach ($this->comments as $comment)
			{
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

				$html .= "\t\t\t\t\t".'<li class="';
				$html .= $access.' comment '.$o.'" id="c'.$comment->id.'">'."\n";
				$html .= "\t\t\t\t\t\t".'<dl class="comment-details">'."\n";
				$html .= "\t\t\t\t\t\t\t".'<dt class="type"><span><span>'.$access.' comment</span></span></dt>'."\n";
				$html .= "\t\t\t\t\t\t\t".'<dd class="date">'.JHTML::_('date',$comment->created, '%d %b, %Y',0).'</dd>'."\n";
				$html .= "\t\t\t\t\t\t\t".'<dd class="time">'.JHTML::_('date',$comment->created, '%I:%M %p',0).'</dd>'."\n";
				$html .= "\t\t\t\t\t\t".'</dl>'."\n";
				$html .= "\t\t\t\t\t\t".'<div class="cwrap">'."\n";
				$html .= "\t\t\t\t\t\t\t".'<p class="commenter"><strong>'. $name.' ('.$comment->created_by .')</strong></p>'."\n";
				if ($comment->comment) {
					/*$comment->comment = preg_replace('/<br\\\\s*?\\/??>/i', "\n", $comment->comment);
					$comment->comment = str_replace("<br />","\n",$comment->comment);*/
					$comment->comment = stripslashes($comment->comment);
					$comment->comment = str_replace("<br />","",$comment->comment);
					//$comment->comment = htmlentities($comment->comment);
					$comment->comment = nl2br($comment->comment);
					$comment->comment = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$comment->comment);

					$html .= "\t\t\t\t\t\t\t".'<blockquote cite="'. $comment->created_by .'">'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<p>'.preg_replace('/  /', ' &nbsp;', $comment->comment).'</p>'."\n";
					$html .= "\t\t\t\t\t\t\t".'</blockquote>'."\n";
				}
				$html .= '<div class="changelog">'.$comment->changelog.'</div>';
				$html .= "\t\t\t\t\t\t".'</div>'."\n";
				$html .= "\t\t\t\t\t".'</li>'."\n";
			}
			$html .= "\t\t\t\t".'</ol>'."\n";
			echo $html;
?>
</div><!-- / .col width-70 -->
<div class="col width-30">
	<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
</div><!-- / .col width-30 -->
<div class="clr"></div>
<?php } // end if (count($comments) > 0) ?>
<?php } // end if ($this->row->id) ?>
<form action="index.php" method="post" name="adminForm" id="commentform" enctype="multipart/form-data">
 <div class="col width-70">
	<fieldset class="adminform" id="primary">
<?php if (!$this->row->id) { ?>
		<legend><?php echo JText::_('TICKET'); ?></legend>
		
		<input type="hidden" name="summary" id="summary" value="<?php echo $this->row->summary; ?>" size="50" />
		
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="login">Login:</label></td>
					<td><input type="text" name="login" id="login" value="<?php echo $this->row->login; ?>" size="50" /></td>
				</tr>
				<tr>
					<td class="key"><label for="name">Name:</label></td>
					<td><input type="text" name="name" id="name" value="<?php echo $this->row->name; ?>" size="50" /></td>
				</tr>
				<tr>
					<td class="key"><label for="email">E-mail:</label></td>
	 				<td><input type="text" name="email" id="email" value="<?php echo $this->row->email; ?>" size="50" /></td>
				</tr>
 				<tr>
					<td class="key" style="vertical-align:top;"><label for="report">Description:</label></td>
					<td><?php echo $editor->display('report', $this->row->report, '360px', '200px', '50', '10'); ?></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="section" value="1" />
		<input type="hidden" name="uas" value="<?php echo JRequest::getVar('HTTP_USER_AGENT','','server'); ?>" />
		<input type="hidden" name="severity" value="normal" />
<?php } else { ?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('TICKET_DETAILS'); ?>:</legend>
			<table class="admintable" width="100%">
				<tbody>
					<tr>
						<td width="50%">
							<label>
								<?php echo JText::_('COMMENT_TAGS'); ?>:<br />
								<?php 
							JPluginHelper::importPlugin( 'hubzero' );
							$dispatcher =& JDispatcher::getInstance();
							$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->lists['tags'])) );

							if (count($tf) > 0) {
								echo $tf[0];
							} else { ?>
								<input type="text" name="tags" id="tags" value="<?php echo $this->lists['tags']; ?>" size="35" />
							<?php } ?>
							</label>
						</td>
						<td width="50%">
							<label>
								<?php echo JText::_('COMMENT_SEVERITY'); ?>:<br />
								<select name="severity" id="severity">
								<?php 
								foreach ($this->lists['severities'] as $anode)
								{
									$selected = ($anode == $this->row->severity)
											  ? ' selected="selected"'
											  : '';
									echo ' <option value="'.$anode.'"'.$selected.'>'.stripslashes($anode).'</option>'."\n";
								}
								?>
								</select>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="admintable" width="100%">
				<tbody>
					<tr>
						<td width="33%">
							<label>
								<?php echo JText::_('COMMENT_GROUP'); ?>:<br />
								<?php 
								$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','',$this->row->group,'','owner')) );
								if (count($gc) > 0) {
									echo $gc[0];
								} else { ?>
								<input type="text" name="group" value="<?php echo $this->row->group; ?>" id="acgroup" value="" size="30" autocomplete="off" />
								<?php } ?>
							</label>
						</td>
						<td width="33%">
							<label>
								<?php echo JText::_('COMMENT_OWNER'); ?>:<br />
								<?php echo $this->lists['owner']; ?>
							</label>
						</td>
						<td width="33%">
							<label>
								Status:<br />
								<?php 
								$html  = '<select name="resolved" id="status">'."\n";
								$html .= "\t".'<option value=""';
								if ($this->row->status == 0 || $this->row->resolved == '') {
									$html .= ' selected="selected"';
								}
								$html .= '>'.JText::_('COMMENT_OPT_OPEN').'</option>'."\n";
								$html .= "\t".'<option value="1"';
								if ($this->row->status == 1) {
									$html .= ' selected="selected"';
								}
								$html .= '>'.JText::_('COMMENT_OPT_WAITING').'</option>'."\n";
								$html .= "\t".'<optgroup label="Closed">'."\n";
								$html .= "\t\t".'<option value="noresolution"';
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
										$html .= "\t\t".'<option value="'.$anode->alias.'"'.$selected.'>'.stripslashes($anode->title).'</option>'."\n";
									}
								}
								$html .= "\t".'</optgroup>'."\n";
								$html .= '</select>'."\n";
								echo $html;
								?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
			<table class="admintable" width="100%">
				<tbody>
					<tr>
						<td width="50%">
							<label>
								<?php 
								$hi = array();
								$o  = '<select name="messages" id="messages" onchange="getMessage();">'."\n";
								$o .= "\t".'<option value="mc">'.JText::_('COMMENT_CUSTOM').'</option>'."\n";
								$jconfig =& JFactory::getConfig();
								foreach ($this->lists['messages'] as $message)
								{
									$message->message = str_replace('"','&quot;',stripslashes($message->message));
									$message->message = str_replace('&quote;','&quot;',$message->message);
									$message->message = str_replace('#XXX','#'.$this->row->id,$message->message);
									$message->message = str_replace('{ticket#}',$this->row->id,$message->message);
									$message->message = str_replace('{sitename}',$jconfig->getValue('config.sitename'),$message->message);
									$message->message = str_replace('{siteemail}',$jconfig->getValue('config.mailfrom'),$message->message);

									$o .= "\t".'<option value="m'.$message->id.'">'.$message->title.'</option>'."\n";

									$hi[] = '<input type="hidden" name="m'.$message->id.'" id="m'.$message->id.'" value="'.$message->message.'" />'."\n";
								}
								$o .= '</select>'."\n";
								$hi = implode("\n",$hi);
								echo $o.$hi;
								?>
							</label>
						</td>
						<td width="50%" style="text-align: right;">
							<label>
								<input type="checkbox" name="access" id="make-private" value="1" /> 
								<?php echo JText::_('COMMENT_PRIVATE'); ?>
							</label>
						</td>
					</tr>
	 				<tr>
						<td colspan="2">
							<textarea name="comment" id="comment" rows="13" cols="45" style="width: 100%;"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COMMENT_LEGEND_ATTACHMENTS'); ?>:</legend>
			<table class="admintable" width="100%">
				<tbody>
	 				<tr>
						<td>
							<label>
								<?php echo JText::_('COMMENT_FILE'); ?>:
								<input type="file" name="upload" id="upload" />
							</label>
						</td>
						<td>
							<label>
								<?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?>:
								<input type="text" name="description" value="" />
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COMMENT_LEGEND_EMAIL'); ?>:</legend>
			<table class="admintable" width="100%">
				<tbody>
	 				<tr>
						<!-- <td>
							<label>
								<input class="option" type="checkbox" name="email_admin" id="email_admin" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_ADMIN'); ?>
							</label>
						</td> -->
						<td>
							<label>
								<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_SUBMITTER'); ?>
							</label>
						</td>
						<td>
							<label>
								<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_OWNER'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<label>
								<?php echo JText::_('COMMENT_SEND_EMAIL_CC'); ?>: <?php 
								// Autocompleter turned off because the autocomplete method of (front-end) com_members needs to know 
								// if the user is logged in or not, which it can't do from the back-end
								/*$mc = $dispatcher->trigger( 'onGetMultiEntry', array(array('members', 'cc', 'acmembers')) );
								if (count($mc) > 0) {
									echo '<span class="hint">supports usernames, user IDs, and email addresses</span>'.$mc[0];
								} else { */?> <span class="hint"><?php echo JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
								<input type="text" name="cc" id="acmembers" value="" style="width: 100%;" />
								<?php //} ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
<?php } ?>
	</fieldset>
 </div><!-- / .col width-70 -->
 <div class="col width-30">
	<p><?php echo JText::_('COMMENT_FORM_EXPLANATION'); ?></p>
 </div><!-- / .col width-30 -->
<div class="clr"></div>
	
	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
	<input type="hidden" name="task" value="save" />
	<!-- <input type="hidden" name="find" value="<?php echo urlencode($this->filters['_find']); ?>" />
	<input type="hidden" name="show" value="<?php echo urlencode($this->filters['_show']); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" /> -->
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	//if (form.summary.value == '') {
	//	alert( 'Ticket must have a summary' );
	//} else {
		submitform( pressbutton );
	//}
}
function getMessage()
{
	var id = getSelectedOption( 'adminForm', 'messages' );
	if(id.value != 'mc') {
		var hi = document.getElementById(id.value).value;
		var co = document.getElementById('comment');
		co.value = hi;
	} else {
		var co = document.getElementById('comment');
		co.value = '';
	}
}

if ($('toggle-details')) {
	$('toggle-details').onclick = function() {
		var tbody = $('ticket-details-body');
		if (tbody.hasClass('hide')) {
			tbody.removeClass('hide');
		} else {
			tbody.addClass('hide');
		}
		return false;
	}	
}

if ($('make-private')) {
	$('make-private').onclick = function() {
		var es = $('email_submitter');
		if (this.checked == true) {
			if (es.checked == true) {
				es.checked = false;
				es.disabled = true;
			}
		} else {
			es.disabled = false;
		}
	}
}
</script>
