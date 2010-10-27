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
	<h2><?php echo $this->title; ?>: #<?php echo $this->row->id; ?></h2>
<?php
/*$targetuser = null;
if ($this->row->login) {
	$targetuser =& JUser::getInstance($this->row->login);
}
if (is_object($targetuser) && $targetuser->id) {
	?>
	<h3><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$this->row->created, '%d %b, %Y').' '.JText::_('AT').' '.JHTML::_('date', $this->row->created, '%I:%M %p').' '.JText::_('BY'); ?> <a href="<?php echo JRoute::_('index.php?option=com_members&id='.$targetuser->id); ?>"><?php echo ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name; ?></a></h3>
	<?php
} else {
	?>
	<h3><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$this->row->created, '%d %b, %Y').' '.JText::_('AT').' '.JHTML::_('date', $this->row->created, '%I:%M %p').' '.JText::_('BY'); ?> <a href="mailto:<?php echo $this->row->email; ?>"><?php echo ($this->row->login) ? $this->row->name.' ('.$this->row->login.')' : $this->row->name; ?></a></h3>
	<?php
}*/
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

<div class="main section">
	<div class="aside">
		<div class="ticket-status">
			<p class="<?php echo ($this->row->status == 2) ? 'closed' : 'open'; ?>"><strong><?php echo ($this->row->status == 2) ? JText::_('TICKET_STATUS_CLOSED_TICKET') : JText::_('TICKET_STATUS_OPEN_TICKET'); ?></strong></p>
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
			<a name="ticket"></a>
			<p class="ticket-member-photo">
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
				<p><?php echo $this->row->report; ?></p>
<?php if ($this->authorized) { ?>
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

<div class="below section">
	<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>
<?php if (count($this->comments) > 0) { ?>			
	<div class="aside">
		<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
	</div><!-- / .aside -->

	<div class="subject">
		<ol class="comments">
<?php
			ximport('Hubzero_User_Profile');
			$o = 'even';
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
				<a name="c<?php echo $comment->id; ?>"></a>
				<p class="comment-member-photo">
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
	</div><!-- / .subject -->
	<div class="clear"></div>
<?php } ?>
</div><!-- / .below section -->

<?php if ((!$juser->get('guest') && ($juser->get('username') == $this->row->login || $this->authorized))) { ?>
<div class="below section">
	<h3>
		<a name="commentform"></a>
		<?php echo JText::_('COMMENT_FORM'); ?>
	</h3>
	
	<div class="aside">
<?php if ($this->authorized) { ?>
		<p><?php echo JText::_('COMMENT_FORM_EXPLANATION'); ?></p>
<?php } else { ?>
		<p>Please remember to describe problems in detail, including any steps you may have taken before encountering an error.</p>
<?php } ?>
	</div><!-- / .aside -->
	
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
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
<?php if (!$this->authorized) { ?>
				<input type="hidden" name="access" value="0" />
<?php } ?>
				
				<fieldset>
					<legend>Ticket Details</legend>
<?php if ($this->authorized) { ?>
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

<?php } ?>
<?php if (!$juser->get('guest') && $this->authorized && ($juser->get('username') != $this->row->login || $this->authorized == 'admin')) { ?>
				<div class="grouping">
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
				</div>
				<div class="clear"></div>
<?php } ?>
				<div class="grouping">
					<label>
						<?php echo JText::_('COMMENT_SEVERITY'); ?>:
						<?php echo SupportHtml::selectArray('ticket[severity]',$this->lists['severities'],$this->row->severity); ?>
					</label>
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
				</div>
				<div class="clear"></div>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
<?php if ($this->authorized) { ?>
					<div class="top grouping">
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

						<label>
							<input class="option" type="checkbox" name="access" id="make-private" value="1" />
							<?php echo JText::_('COMMENT_PRIVATE'); ?>
						</label>
					</div>
					<div class="clear"></div>
<?php } ?>
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

<?php if ($this->authorized) { ?>
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
<?php } ?>
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('SUBMIT_COMMENT'); ?>" />
				</p>
			</fieldset>
		</form>
	</div><!-- / .subject -->
</div><!-- / .section -->
<?php } ?>
<div class="clear"></div>