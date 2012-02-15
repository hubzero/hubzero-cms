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

ximport('Hubzero_User_Helper');
ximport("Hubzero_User_Profile_Helper");

$juser =& JFactory::getUser();
$no_html = JRequest::getInt( 'no_html', 0 );
$user_messaging = $this->config->get('user_messaging', 0);

$edit = false;
$password = false;
$messaging = false;


//are we allowed to messagin user
switch( $user_messaging )
{
	case 0:
		$mssaging = false;
		break;
	case 1:
		$common = Hubzero_User_Helper::getCommonGroups( $juser->get("id"), $this->profile->get("uidNumber") );
		if(count($common) > 0) {
			$messaging = true;
		}
		break;
	case 2:
		$messaging = true;
		break;
}

//if user is this member turn on editing and password change, turn off messaging
if($this->profile->get("uidNumber") == $juser->get("id")) {
	$edit = true;
	$password = true;
	$messaging = false;
}

//no messaging if guest
if($juser->get("guest"))
{
	$messaging = false;
}

if (!$no_html) {
?>
<div class="innerwrap">
	<div id="page_container">
		<div id="page_container_inner">

			<div id="page_sidebar">
				<div id="page_sidebar_inner">
					<?php
						$default_picture = '/components/com_members/images/profile.gif';
						$picture = array_pop(explode("/", $this->profile->get('picture')));
						$id = Hubzero_User_Profile_Helper::niceidformat( $this->profile->get("uidNumber") );
						
						$picture_path = $this->config->get("webpath") . DS . $id . DS . $picture; 
						$src = ($picture != $default_picture && is_file(JPATH_ROOT . $picture_path)) ? $picture_path : $default_picture;
						
						$link = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber'));
					?>
					<a id="page_identity" href="<?php echo $link; ?>">
						<img src="<?php echo $src; ?>" />
					</a>

					<ul id="page_menu">
						<?php foreach($this->cats as $k => $c) : ?>
							<?php 
								$key = key($c); 
								if(!$key) { continue; }
								$name = $c[$key];
								$url = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber') . '&active=' . $key);
								$meta = ($this->sections[$k]['metadata'] != "") ? $this->sections[$k]['metadata'] : "";
								$cls = ($this->tab == $key) ? "active" : "";
							?>
							<li><a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo $name; ?><?php echo $meta; ?></a></li>
						<?php endforeach; ?>
					</ul><!-- //end page menu -->
				</div><!-- //end page sidebar inner -->
			</div><!-- //end page sidebar -->

			<div id="page_main">
				<div id="page_header">
					<h2>
						<?php echo stripslashes($this->profile->get('name')); ?>
					</h2>
					
					<?php if($edit || $password || $messaging) : ?>
						<ul id="page_options">
							<?php if($edit) : ?>
								<?php $edit_url = JRoute::_('index.php?option=com_members&task=edit&id=' . $this->profile->get("uidNumber")); ?>
								<li><a class="edit tooltips" title="Edit Profile :: Edit <?php if($this->profile->get("uidNumber") == $juser->get("id")) { echo "my"; } else { echo $this->profile->get("name") . "'s"; } ?> profile." href="<?php echo $edit_url; ?>"><?php echo JText::_('Edit profile'); ?></a></li>
							<?php endif?>
							
							<?php if($password) : ?>
								<?php $pass_url = JRoute::_('index.php?option=com_members&task=changepassword&id=' . $this->profile->get("uidNumber")); ?>
								<li><a class="password tooltips" title="Change Password :: Change your password" href="<?php echo $pass_url; ?>"><?php echo JText::_('Change Password'); ?></a></li>
							<?php endif; ?>
							
							<?php if($messaging): ?>
								<?php $msg_url = JRoute::_('index.php?option=com_members&id=' . $juser->get("id") . '&active=messages&task=new&to[]=' . $this->profile->get('uidNumber')); ?>
								<li><a class="message tooltips" title="Message :: Send a message to <?php echo stripslashes($this->profile->get('name')); ?>" href="<?php echo $msg_url; ?>"><?php echo JText::_('Message'); ?></a></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</div><!-- // end page header -->
				<div id="page_notifications">
					<?php
						if($this->getError()) {
							echo "<p class=\"error\">" . $this->getError() . "</p>";
						}
					?>
				</div>
				<div id="page_content" class="member_<?php echo $this->tab; ?>">
					<?php
			 			} 
			
						foreach($this->sections as $s) {
							if($s['html'] != "") {
								echo $s['html'];
							}
						}
						
						if (!$no_html) { 
					?>
				</div>
			</div> <!-- //close page main -->

		</div> <!-- //close page container inner -->
	</div> <!-- //close page container -->
</div>
<?php } ?>
