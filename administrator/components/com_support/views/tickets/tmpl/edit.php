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
JToolBarHelper::title( JText::_( 'Ticket' ).': <small><small>[ '. $text.' ]</small></small>', 'support.png' );
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();

$juser =& JFactory::getUser();

ximport('Hubzero_User');
$user = new Hubzero_User_Profile();
//$user = Hubzero_User::getInstance($juser->get('id'));
$user->load($juser->get('id'));
$unknown = true;
$name = '';
$usertype = JText::_('Unknown');
$notify = array();

$submitter = new Hubzero_User_Profile();
 // = Hubzero_User::getInstance($this->row->login);
if ($this->row->login) 
{
	$submitter->load($this->row->login);
	if (is_object($submitter) && $submitter->get('name')) 
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$usertype = JUser::getInstance($submitter->get('uidNumber'))->get('usertype');
		} 
		else
		{
			jimport( 'joomla.user.helper' );
			$usertype = implode(', ', JUserHelper::getUserGroups($submitter->get('uidNumber')));
		}

		$this->row->name = ($this->row->name) ? $this->row->name : stripslashes($submitter->get('name'));
		$name = '<a rel="profile" href="' . JRoute::_('index.php?option=com_members&amp;task=edit&amp;id[]=' . $submitter->get('uidNumber')) . '">' . $this->escape($this->row->name) . ' (' . $this->escape(stripslashes($submitter->get('username'))) . ')</a>';
		$unknown = false;
		
		$notify[] = $this->escape($this->row->name) . ' (' . $this->escape(stripslashes($submitter->get('username'))) . ')';
	}
} 

if (!$name)
{
	if ($this->row->name)
	{
		$name  = $this->escape($this->row->name) . ' (' . $this->escape($this->row->email) . ')';
	}
	else 
	{
		$name  = $this->escape($this->row->email);
	}
	
	$notify[] = $name;
}

$owner = new Hubzero_User_Profile();
if ($this->row->owner)
{
	$owner->load($this->row->owner); // = Hubzero_User::getInstance($this->row->owner);
	if (is_object($owner) && $owner->get('name')) 
	{
		$notify[] = $this->escape(stripslashes($owner->get('name'))) . ' (' . $this->escape(stripslashes($owner->get('username'))) . ')';
	}
}

//jimport('joomla.html.editor');
//$editor =& JEditor::getInstance();

ximport('Hubzero_User_Profile');

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$dateFormat = '%d %b, %Y';
	$timeFormat = '%I:%M %p';
	$tz = 0;
}
else 
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'H:m a';
	$tz = true;
}

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

$cc = array();
?>
<form action="index.php" method="post" name="adminForm" id="commentform" enctype="multipart/form-data">
<?php 
if ($this->row->id) {
		/*if ($this->row->id) {
			echo '<p id="prev-next">';
			$prv = $this->row->getTicketId('prev', $this->filters, 'admin');
			if ( $prv ) {
				echo '<a href="index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=edit&amp;id='. $prv .'">'.JText::_('PREVIOUS_TICKET').'</a>';
			} else {
				echo '<span style="color:#ccc;">'.JText::_('PREVIOUS_TICKET').'</span>';
			}
			echo ' &nbsp;&nbsp; ';
			$nxt = $this->row->getTicketId('next', $this->filters, 'admin');
			if ( $nxt ) {
				echo '<a href="index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=edit&amp;id='. $nxt .'">'.JText::_('NEXT_TICKET').'</a>';
			} else {
				echo '<span style="color:#ccc;">'.JText::_('NEXT_TICKET').'</span>';
			}
			echo '</p>';
		}*/
		
		/*$name = $this->escape($this->row->name);
		$xuser = new Hubzero_User_Profile();
		if ($this->row->login) {
			$xuser->load($this->row->login);
			if (is_object($xuser) && $xuser->get('name')) {
				$name = '<a href="' . JRoute::_('index.php?option=com_members&amp;task=edit&amp;id[]=' . $this->row->login) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
			}
		}*/
		
		if (count($this->comments) > 0) { 
			$last = end($this->comments);
			$lastactivity = '<time>' . JHTML::_('date', $last->created, $timeFormat, $tz) . '</time>';
		} else {
			$lastactivity = JText::_('N/A');
		}
?>
	<div class="col width-70 fltlft">
		<fieldset>
			<legend><span><?php echo JText::_('Ticket'); echo ($this->row->id) ? ' #'.$this->row->id : ''; ?></span></legend>

			<div class="ticket">
				<p class="ticket-member-photo">
					<span class="ticket-anchor"><a name="t<?php echo $this->row->id; ?>"></a></span>
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($submitter, $unknown); ?>" alt="" />
				</p>
				<div class="ticket-head">
					<strong>
						<?php echo $name; ?>
					</strong>
					<a class="permalink" href="index.php?option=com_support&amp;controller=tickets&amp;task=edit&amp;id=<?php echo $this->row->id; ?>" title="<?php echo JText::_('Permalink'); ?>">@ 
						<span class="time"><time><?php echo JHTML::_('date', $this->row->created, $timeFormat, $tz); ?></time></span> <?php echo JText::_('on'); ?> 
						<span class="date"><time><?php echo JHTML::_('date', $this->row->created, $dateFormat, $tz); ?></time></span>
					</a>
				</div>
				<blockquote class="ticket-content" cite="<?php echo ($this->row->login) ? $this->escape($this->row->login) : $this->escape($this->row->name); ?>">
					<?php echo preg_replace('/  /', ' &nbsp;', $this->row->report); ?>
				</blockquote><!-- / .ticket-content -->
				<div class="ticket-details">
					<table summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
						<tbody>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_EMAIL'); ?>:</th>
								<td><a href="mailto:<?php echo $this->row->email; ?>"><?php echo $this->escape($this->row->email); ?></a></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_USERTYPE'); ?>:</th>
								<td><?php echo $this->escape($usertype); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_OS'); ?>:</th>
								<td><?php echo $this->escape($this->row->os); ?> / <?php echo $this->escape($this->row->browser); ?> (<?php echo ($this->row->cookies) ? JText::_('COOKIES_ENABLED') : JText::_('COOKIES_DISABLED'); ?>)</td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_IP'); ?>:</th>
								<td><?php echo $this->escape($this->row->ip); ?> (<?php echo $this->escape($this->row->hostname); ?>)</td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_REFERRER'); ?>:</th>
								<td><?php echo ($this->row->referrer) ? $this->escape($this->row->referrer) : ' '; ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('TICKET_DETAILS_INSTANCES'); ?>:</th>
								<td><?php echo $this->escape($this->row->instances); ?></td>
							</tr>
<?php if ($this->row->uas) { ?>
							<tr>
								<td colspan="2"><?php echo $this->escape($this->row->uas); ?></td>
							</tr>
<?php } ?>
						</tbody>
					</table>
				</div><!-- / .ticket-details -->
			</div><!-- / .ticket -->
		</fieldset>
	</div><!-- / .col width-70 fltlft -->
	<div class="col width-30 fltrt">
		<dl class="ticket-status <?php if ($this->row->open == 0) { echo 'closed'; } else { echo 'open'; } ?>">
			<dt><?php echo JText::_('TICKET_STATUS'); ?></dt>
			<dd><?php 
				if ($this->row->open == 0) {
					echo JText::_('TICKET_STATUS_CLOSED_TICKET');
				} else {
					echo JText::_('TICKET_STATUS_OPEN_TICKET');
				} ?></dd>
		</dl>

		<table class="meta" summary="<?php echo JText::_('meta_tbl_summary'); ?>">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('ticket_details_severity'); ?></th>
					<td><?php echo JText::_('severity_' . $this->row->severity); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('ticket_details_owner'); ?></th>
					<td><?php 
					if ($this->row->owner) 
					{
						//$owner = Hubzero_User::getInstance($this->row->owner);
						if (is_object($owner))
						{
							echo '<a rel="profile" href="index.php?option=com_members&amp;task=edit&amp;id[]=' . $owner->get('uidNumber') . '">' . $this->escape(stripslashes($owner->get('name'))) . '</a>';
						}
						else 
						{
							echo $this->escape($this->row->owner);
						}
					}
					else 
					{
						echo JText::_('none');
					}
					?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('ticket_details_last_activity'); ?></th>
					<td><?php echo $lastactivity; ?></td>
				</tr>
			</tbody>
		</table>

		<?php
			$watching = new SupportTableWatching(JFactory::getDBO());
			$watching->load($this->row->id, $juser->get('id'));
		?>
		<div class="ticket-watch">
		<?php if ($watching->id) { ?>
			<div id="watching">
				<p>This ticket is saved in your watch list.</p>
				<p><a class="stop-watching btn" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $this->row->id; ?>&amp;watch=stop">Stop watching</a></p>
			</div>
		<?php } ?>
		<?php if (!$watching->id) { ?>
			<p><a class="start-watching btn" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $this->row->id; ?>&amp;watch=start">Watch ticket</a></p>
		<?php } ?>
			<p>When watching a ticket, you will be notified of any comments added or changes made. You may stop watching at any time.</p>
		</div>
	</div><!-- / .col width-30 fltlft -->
	<div class="clr"></div>

<?php if (count($this->comments) > 0) { ?>
	<div class="col width-70 fltlft">
		<fieldset>
			<legend><span><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></span></legend>
			
			<ol class="comments">
<?php
			$useri = new Hubzero_User_Profile();
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

				$name = $this->escape(JText::_('Unknown'));
				$cite = $name;
				$useri->load($comment->created_by);
				$anon = 1;
				if ($comment->created_by) 
				{
					//$juseri =& JUser::getInstance($comment->created_by);
					if (is_object($useri)) 
					{
						$name = '<a rel="profile" href="index.php?option=com_members&amp;task=edit&amp;id[]=' . $useri->get('uidNumber') . '">' . $this->escape(stripslashes($useri->get('name'))) . ' (' . $this->escape(stripslashes($useri->get('username'))) . ')</a>';
						$cite = $this->escape(stripslashes($useri->get('name')));
						$anon = 0;
					}
				}
?>
				<li class="<?php echo $access .' comment'; ?>" id="c<?php echo $comment->id; ?>">
					<p class="comment-member-photo">
						<span class="comment-anchor"><a name="c<?php echo $comment->id; ?>"></a></span>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($useri, $anon); ?>" alt="<?php echo JText::_('profile_image'); ?>" />
					</p>
					<p class="comment-head">
						<strong>
							<?php echo $name; ?>
						</strong>
						<a class="permalink" href="<?php echo 'index.php?option=com_support&controller=tickets&task=edit&amp;id=' . $this->row->id . '#c' . $comment->id; ?>" title="<?php echo JText::_('permalink'); ?>">@ 
							<span class="time"><time><?php echo JHTML::_('date',$comment->created, $dateFormat, $tz); ?></time></span> <?php echo JText::_('on'); ?>
							<span class="date"><time><?php echo JHTML::_('date',$comment->created, $timeFormat, $tz); ?></time></span>
						</a>
					</p>
<?php if ($comment->comment) { ?>
					<blockquote class="comment-content" cite="<?php echo $cite; ?>">
						<p><?php 
							$comment->comment = str_replace("<br />", '', $comment->comment);
							$comment->comment = nl2br($comment->comment);
							$comment->comment = str_replace("\t", ' &nbsp; &nbsp;', $comment->comment);

							echo preg_replace('/  /', ' &nbsp;', $comment->comment); 
						?></p>
					</blockquote><!-- / .comment-content -->
<?php } ?>
<?php 
				if (trim($comment->changelog)) 
				{
					$clog = '';
					if (strstr($comment->changelog, '<'))
					{
						$comment->changelog = str_replace('changelog', 'changes', $comment->changelog);
						$comment->changelog = str_replace('E-mailed', JText::_('Messaged'), $comment->changelog);
						$clog .= str_replace('emaillog', 'notifications', $comment->changelog);
					}
					else 
					{
						/*$json = '{
							"changes": [
								{
									"field": "status", 
									"before": "closed", 
									"after": "open"
								}
							],
							"notifications": [
								{
									"name": "Shawn Rice",
									"address": "zooley@purdue.edu",
									"role": "assignee"
								},
								{
									"name": "Michael McLennan",
									"address": "mmclennan@purdue.edu",
									"role": "submitter"
								}
							]
						}';*/
						$logs = json_decode($comment->changelog, true);
						foreach ($logs as $type => $log)
						{
							if (is_array($log) && count($log) > 0)
							{
								if ($type == 'cc')
								{
									$cc = $log;
									continue;
								}
								$clog .= '<ul class="' . $type . '">';
								foreach ($log as $items)
								{
									if ($type == 'changes')
									{
										$clog .= '<li>' . JText::sprintf('%s changed from "%s" to "%s"', $items['field'], $items['before'], $items['after']) . '</li>';
									}
									else if ($type == 'notifications')
									{
										$clog .= '<li>' . JText::_('Messaged') . ' (' . $items['role'] . ') ' . $items['name'] . ' - ' . $items['address'] . '</li>';
									}
								}
								$clog .= '</ul>';
							}
						}
					}
					if ($clog) 
					{
?>
					<div class="comment-changelog">
						<?php echo $clog; ?>
					</div><!-- / .comment-changelog -->
<?php 
					}
				} 
?>
				</li>
<?php
			}
?>
			</ol>
		</fieldset>
	</div><!-- / .col width-70 -->
	<div class="col width-30 fltrt">
		<p>
			<a class="new button" href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a>
		</p>
	</div><!-- / .col width-30 -->
	<div class="clr"></div>
<?php } // end if (count($comments) > 0) ?>
<?php } // end if ($this->row->id) ?>

 	<div class="col width-70 fltlft">
<?php if (!$this->row->id) { ?>
		<fieldset>
			<input type="hidden" name="summary" id="summary" value="<?php echo $this->row->summary; ?>" size="50" />
		
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="login">Login:</label></td>
						<td colspan="3"><input type="text" name="login" id="login" value="<?php echo $this->escape(trim($this->row->login)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="name">Name:</label></td>
						<td colspan="3"><input type="text" name="name" id="name" value="<?php echo $this->escape(trim($this->row->name)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="email">E-mail:</label></td>
		 				<td colspan="3"><input type="text" name="email" id="email" value="<?php echo $this->row->email; ?>" size="50" /></td>
					</tr>
	 				<tr>
						<td class="key" style="vertical-align:top;"><label for="report">Description:</label></td>
						<td colspan="3"><textarea name="report" id="report" cols="75" rows="15"><?php echo $this->escape(trim($this->row->report)); ?></textarea></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><label for="ticket-field-tags"><?php echo JText::_('COMMENT_TAGS'); ?></label></td>
						<td colspan="3">
							<?php 
							$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->lists['tags'])) );

							if (count($tf) > 0) {
								echo $tf[0];
							} else { ?>
								<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->lists['tags']); ?>" />
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="ticket-field-group"><?php echo JText::_('COMMENT_GROUP'); ?>:</label></td>
						<td>
							<?php 
							$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','','','','owner')) );
							if (count($gc) > 0) {
								echo $gc[0];
							} else { ?>
							<input type="text" name="group" value="" id="acgroup" value="" size="30" autocomplete="off" />
							<?php } ?>
						</td>
						<td class="key"><label><?php echo JText::_('COMMENT_OWNER'); ?></label>
						<td>
							<?php echo $this->lists['owner']; ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="ticket-field-severity"><?php echo JText::_('COMMENT_SEVERITY'); ?></label>
						<td>
							<select name="severity" id="ticket-field-severity">
								<option value="critical"><?php echo JText::_('SEVERITY_CRITICAL'); ?></option>
								<option value="major"><?php echo JText::_('SEVERITY_MAJOR'); ?></option>
								<option value="normal"><?php echo JText::_('SEVERITY_NORMAL'); ?></option>
								<option value="minor"><?php echo JText::_('SEVERITY_MINOR'); ?></option>
								<option value="trivial"><?php echo JText::_('SEVERITY_TRIVIAL'); ?></option>
							</select>
						</td>
						<td class="key"><label for="ticket-field-status"><?php echo JText::_('COMMENT_STATUS'); ?></label>
						<td>
							<select name="resolved" id="ticket-field-status">
								<option value=""><?php echo JText::_('COMMENT_OPT_OPEN'); ?></option>
								<option value="1"><?php echo JText::_('COMMENT_OPT_WAITING'); ?></option>
								<optgroup label="<?php echo JText::_('Closed'); ?>">
									<option value="noresolution"><?php echo JText::_('COMMENT_OPT_CLOSED'); ?></option>
<?php
							if (isset($this->lists['resolutions']) && $this->lists['resolutions']!='') 
							{
								foreach ($this->lists['resolutions'] as $anode) 
								{
?>
									<option value="<?php echo $this->escape($anode->alias); ?>"><?php echo $this->escape(stripslashes($anode->title)); ?></option>
<?php
								}
							}
?>
								</optgroup>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="section" value="1" />
			<input type="hidden" name="uas" value="<?php echo JRequest::getVar('HTTP_USER_AGENT', '', 'server'); ?>" />
			<input type="hidden" name="severity" value="normal" />
<?php } else { ?>
		<fieldset>
			<legend><span><a name="comments"></a><?php echo JText::_('TICKET_DETAILS'); ?></span></legend>
		
			<div class="new ticket">
				<p class="ticket-member-photo">
					<span class="ticket-anchor"><a name="new"></a></span>
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($user, 0); ?>" alt="<?php echo JText::_('profile_image'); ?>" />
				</p>
				
				<fieldset class="ticket-head">
					<strong>
						<a rel="profile" href="index.php?option=com_members&amp;task=edit&amp;id[]=<?php echo $this->escape($user->get('id')); ?>">
							<?php echo $this->escape($user->get('name')); ?> (<?php echo $this->escape($user->get('username')); ?>)
						</a>
					</strong>
					<span class="permalink">
						<?php echo JText::_('@'); ?> <span class="time"><time><?php echo JHTML::_('date', date("Y-m-d H:i:s"), $dateFormat, $tz); ?></time></span> 
						<?php echo JText::_('on'); ?> <span class="date"><time><?php echo JHTML::_('date', date("Y-m-d H:i:s"), $timeFormat, $tz); ?></time></span>
					</span>

					<label for="comment-field-access" class="private">
						<input type="checkbox" name="access" id="comment-field-access" value="1" />
						<span><?php echo JText::_('comment_field_access'); ?></span>
					</label>
				</fieldset><!-- / .ticket-head -->
				
				<div class="ticket-content">
					<fieldset>
						<label for="comment-field-template">
							<select name="messages" id="comment-field-template">
								<option value="custom"><?php echo JText::_('COMMENT_CUSTOM'); ?></option>
<?php
						$hi = array();
						$jconfig = JFactory::getConfig();
						foreach ($this->lists['messages'] as $message)
						{
							$message->message = str_replace('"','&quot;',stripslashes($message->message));
							$message->message = str_replace('&quote;','&quot;',$message->message);
							$message->message = str_replace('#XXX','#'.$this->row->id,$message->message);
							$message->message = str_replace('{ticket#}',$this->row->id,$message->message);
							$message->message = str_replace('{sitename}',$jconfig->getValue('config.sitename'),$message->message);
							$message->message = str_replace('{siteemail}',$jconfig->getValue('config.mailfrom'),$message->message);
?>
								<option value="m<?php echo $message->id; ?>"><?php echo $this->escape(stripslashes($message->title)); ?></option>
<?php
							$hi[] = '<input type="hidden" name="m' . $message->id . '" id="m' . $message->id . '" value="' . $this->escape(stripslashes($message->message)) . '" />';
						}
?>
							</select>
							<?php echo implode("\n", $hi); ?>
						</label>
					
						<label for="comment-field-content">
							<span class="label"><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?></span>
							<textarea name="comment" id="comment-field-comment" cols="75" rows="15"></textarea>
						</label>
					
						<div class="col width-50 fltlft">
							<label for="comment-field-upload">
								<?php echo JText::_('COMMENT_FILE'); ?>
								<input type="file" name="upload" id="comment-field-upload" />
							</label>
						</div>
						<div class="col width-50 fltrt">
							<label for="comment-field-description">
								<?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?>
								<input type="text" name="description" id="comment-field-description" value="" />
							</label>
						</div>
						<div class="clr"></div>
					
						<label for="comment-field-message">
							<?php echo JText::_('COMMENT_SEND_EMAIL_CC'); ?> <?php 
							/*$mc = $dispatcher->trigger('onGetMultiEntry', array(
								array(
									'members',   // The component to call
									'cc',        // Name of the input field
									'acmembers', // ID of the input field
									'',          // CSS class(es) for the input field
									implode(', ', $notify) // The value of the input field
								)
							));
							if (count($mc) > 0) {
								echo '<span class="hint">'.JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE').'</span>'.$mc[0];
							} else { ?> <span class="hint"><?php echo JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
							<input type="text" name="cc" id="acmembers" value="" size="35" />
							<?php }
							<span class="hint"><?php echo JText::_('Private comments will NOT be sent to the ticket submitter regardless of entries specified.'); ?></span>*/ ?>
							<input type="text" name="cc" id="comment-field-message" value="<?php echo implode(', ', $cc); ?>" />
						</label>
						<div class="col width-50 fltlft">
							<label for="email_submitter">
								<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_SUBMITTER'); ?>
							</label>
						</div>
						<div class="col width-50 fltrt">
							<label for="email_owner">
								<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_OWNER'); ?>
							</label>
						</div>
						<div class="clr"></div>
					</fieldset>
				</div><!-- / .ticket-content -->
				
				<fieldset class="ticket-details">
					<label for="ticket-field-tags">
						<?php echo JText::_('COMMENT_TAGS'); ?>
						<?php 
					$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->lists['tags'])) );

					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->lists['tags']); ?>" />
					<?php } ?>
						
					</label>
					
					<div class="col width-50 fltlft">
						<label for="ticket-field-group">
							<?php echo JText::_('COMMENT_GROUP'); ?>:<br />
							<?php 
							$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','',$this->row->group,'','owner')) );
							if (count($gc) > 0) {
								echo $gc[0];
							} else { ?>
							<input type="text" name="group" value="<?php echo $this->escape($this->row->group); ?>" id="acgroup" value="" size="30" autocomplete="off" />
							<?php } ?>
						</label>
					</div>
					<div class="col width-50 fltrt">
						<label>
							<?php echo JText::_('COMMENT_OWNER'); ?>
							<?php echo $this->lists['owner']; ?>
						</label>
					</div>
					<div class="clr"></div>
					
					<div class="col width-50 fltlft">
						<label for="ticket-field-severity">
							<?php echo JText::_('COMMENT_SEVERITY'); ?>
							<select name="severity" id="ticket-field-severity">
								<option value="critical"<?php if ($this->row->severity == 'critical') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEVERITY_CRITICAL'); ?></option>
								<option value="major"<?php if ($this->row->severity == 'major') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEVERITY_MAJOR'); ?></option>
								<option value="normal"<?php if ($this->row->severity == 'normal') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEVERITY_NORMAL'); ?></option>
								<option value="minor"<?php if ($this->row->severity == 'minor') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEVERITY_MINOR'); ?></option>
								<option value="trivial"<?php if ($this->row->severity == 'trivial') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEVERITY_TRIVIAL'); ?></option>
							</select>
						</label>
					</div>
					<div class="col width-50 fltrt">
						<label for="ticket-field-status">
							<?php echo JText::_('COMMENT_STATUS'); ?>
							<select name="resolved" id="ticket-field-status">
								<option value=""<?php if ($this->row->open == 1 && $this->row->status < 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COMMENT_OPT_OPEN'); ?></option>
								<option value="1"<?php if ($this->row->status == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COMMENT_OPT_WAITING'); ?></option>
								<optgroup label="<?php echo JText::_('Closed'); ?>">
									<option value="noresolution"<?php if ($this->row->open == 0 && $this->row->resolved == 'noresolution') { echo ' selected="selected"'; } ?>><?php echo JText::_('COMMENT_OPT_CLOSED'); ?></option>
<?php
							if (isset($this->lists['resolutions']) && $this->lists['resolutions']!='') 
							{
								foreach ($this->lists['resolutions'] as $anode) 
								{
?>
									<option value="<?php echo $this->escape($anode->alias); ?>"<?php if ($anode->alias == $this->row->resolved) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($anode->title)); ?></option>
<?php
								}
							}
?>
								</optgroup>
							</select>
						</label>
					</div>
					<div class="clr"></div>
				</fieldset><!-- / .ticket-details -->
			</div>
<?php } ?>
		</fieldset>
	</div><!-- / .col width-70 -->
	<div class="col width-30 fltrt">
		<p><?php echo JText::_('COMMENT_FORM_EXPLANATION'); ?></p>
	</div><!-- / .col width-30 -->
	<div class="clr"></div>
	
	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
	<input type="hidden" name="task" value="save" />
	
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

if ($('comment-field-template')) {
	$('comment-field-template').addEvent('change', function() {
		if ($(this).value != 'mc') {
			var hi = document.getElementById($(this).value).value;
			var co = document.getElementById('comment-field-comment');
			co.value = hi;
		} else {
			var co = document.getElementById('comment-field-comment');
			co.value = '';
		}
	});
}

if ($('comment-field-access')) {
	$('comment-field-access').addEvent('click', function() {
		var es = $('email_submitter');
		if ($(this).checked == true) {
			if (es.checked == true) {
				es.checked = false;
				es.disabled = true;
			}
		} else {
			es.disabled = false;
		}
	});
}
</script>
