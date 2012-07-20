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

$prefix = $this->profile->get("name")."'s";
$edit = false;
$password = false;
$messaging = false;

$tab = $this->tab;
$tab_name = "Dashboard";

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
	if($this->tab == "profile") 
	{
		$edit = true;
		$password = true;
	}
	$messaging = false;
	$prefix = "My";
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
		<div id="page_sidebar">
			<?php
				$src = Hubzero_User_Profile_Helper::getMemberPhoto($this->profile, 0, false);
				$link = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber'));
			?>
			<div id="page_identity">
				<a href="<?php echo $link; ?>" id="page_identity_link" title="Go to <?php echo $this->profile->get('name'); ?>'s Profile">
					<img src="<?php echo $src; ?>" />
				</a>
			</div><!-- /#page_identity --> 
			
			<?php if($this->profile->get("uidNumber") == $juser->get("id") && $tab == "profile") : ?>
				<?php
					$cls = "";
					$text = "Public Profile. <wbr>Set Private?";
					$title = "Public Profile :: Click here to set your profile private.";
					if($this->profile->get("public") != 1)
					{
						$cls = "private";
						$text = "Private Profile. <wbr>Set Public?";
						$title = "Private Profile :: Click here to set your profile public.";
					}
				?>
				<a href="/members/<?php echo $this->profile->get("uidNumber"); ?>/edit" data-uidnumber="<?php echo $this->profile->get("uidNumber"); ?>" id="profile-privacy" class="tooltip <?php echo $cls; ?>" title="<?php echo $title; ?>">
					<?php echo $text; ?>
				</a>
			<?php endif; ?>
			
			<ul id="page_menu">
				<?php foreach($this->cats as $k => $c) : ?>
					<?php 
						$key = key($c); 
						if(!$key) { continue; }
						$name = $c[$key];
						$url = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber') . '&active=' . $key);
						$cls = ($this->tab == $key) ? "active" : "";
						$tab_name = ($this->tab == $key) ? $name : $tab_name;
						
						$metadata = $this->sections[$k]['metadata'];
						$meta_count = (isset($metadata['count']) && $metadata['count'] != "") ? $metadata['count'] : "";
						if(isset($metadata['alert']) && $metadata['alert'] != "") 
						{
							$meta_alert = $metadata['alert'];
							$cls .= " with-alert";
						}
						else
						{
							$meta_alert = "";
						}
					?>
					<li class="<?php echo $cls; ?>">
						<a class="<?php echo $key; ?>" title="<?php echo $prefix." ".$name; ?>" href="<?php echo $url; ?>">
							<?php echo $name; ?>
						</a>
						<span class="meta">
							<?php if($meta_count) : ?>
								<span class="count"><?php echo $meta_count; ?></span>
							<?php endif; ?>
						</span>
						<?php echo $meta_alert; ?> 
					</li>
				<?php endforeach; ?>
			</ul><!-- /#page_menu -->
			
			<?php
				$thumb = "/site/stats/contributor_impact/impact_".$this->profile->get("uidNumber")."_th.gif";
				$full = "/site/stats/contributor_impact/impact_".$this->profile->get("uidNumber").".gif"
			?>
			<?php if(file_exists(JPATH_ROOT . $thumb)) : ?>
				<a id="member-stats-graph" rel="lightbox" title="<?php echo $this->profile->get("name")."'s Impact Graph"; ?>" data-name="<?php echo $this->profile->get("name"); ?>" data-type="Impact Graph" href="<?php echo $full; ?>">
					<img src="<?php echo $thumb; ?>" alt="<?php echo $this->profile->get("name")."'s Impact Graph"; ?>" />
				</a>
			<?php endif; ?>
			
		</div><!-- /#page_sidebar -->
		<div id="page_main">
			<ul id="page_options">
				<?php if($edit) : ?>
					<?php $edit_url = JRoute::_('index.php?option=com_members&id='.$this->profile->get("uidNumber").'&task=edit'); ?>
					<li><a class="edit tooltips" id="edit-profile" title="Edit Profile :: Edit <?php if($this->profile->get("uidNumber") == $juser->get("id")) { echo "my"; } else { echo $this->profile->get("name") . "'s"; } ?> profile." href="<?php echo $edit_url; ?>"><?php echo JText::_('Edit profile'); ?></a></li>
				<?php endif; ?>
				
				<?php if($password) : ?>
					<?php $pass_url = JRoute::_('index.php?option=com_members&task=changepassword&id=' . $this->profile->get("uidNumber")); ?>
						<li><a class="password tooltips" id="change-password" title="Change Password :: Change your password" href="<?php echo $pass_url; ?>"><?php echo JText::_('Change Password'); ?></a></li>
				<?php endif; ?>
				
				<?php if($messaging): ?>
					<?php $msg_url = JRoute::_('index.php?option=com_members&id=' . $juser->get("id") . '&active=messages&task=new&to[]=' . $this->profile->get('uidNumber')); ?>
					<li><a class="message tooltips" title="Message :: Send a message to <?php echo stripslashes($this->profile->get('name')); ?>" href="<?php echo $msg_url; ?>"><?php echo JText::_('Message'); ?></a></li>
				<?php endif; ?>
			</ul> 
			<div id="page_header">
				<h2 <?php if($this->profile->get("public") != 1) : ?> class="private" <?php endif; ?>>
					<a href="/members/<?php echo $this->profile->get("uidNumber"); ?>">
						<?php echo stripslashes($this->profile->get('name')); ?>
					</a>
				</h2>
				<span>►</span>
				<h3><?php echo $tab_name; ?></h3>
			</div>
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
		            if($this->overwrite_content)
					{
						echo $this->overwrite_content;
					}
					else
					{
						foreach($this->sections as $s) {
							if($s['html'] != "") {
								echo $s['html'];
							}
						}
					}
					
					if (!$no_html) { 
				?>
			</div><!-- /#page_content -->
		</div><!-- /#page_main -->
	</div> <!-- //#page_container -->
</div><!-- /.innerwrap -->
<?php } ?>
