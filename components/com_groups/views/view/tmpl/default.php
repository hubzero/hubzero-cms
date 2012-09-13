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

//get objects
$config 	=& JFactory::getConfig();
$database 	=& JFactory::getDBO();

//is membership control managed on group?
$membership_control = $this->gparams->get('membership_control', 1);

//get no_html request var
$no_html = JRequest::getInt( 'no_html', 0 );
?>

<?php if (!$no_html) : ?>
	<div class="innerwrap">
		<div id="page_container">
			<div id="page_sidebar">
				<?php
					//default logo
					$default_logo = DS.'components'.DS.$this->option.DS.'assets'.DS.'img'.DS.'group_default_logo.png';

					//logo link - links to group overview page
					$link = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn'));

					//path to group uploaded logo
					$path = '/site/groups/'.$this->group->get('gidNumber').DS.$this->group->get('logo');

					//if logo exists and file is uploaded use that logo instead of default
					$src = ($this->group->get('logo') != '' && is_file(JPATH_ROOT.$path)) ? $path : $default_logo;
				?>
				<div id="page_identity">
					<a href="<?php echo $link; ?>" title="<?php echo $this->group->get('description'); ?> Home">
						<img src="<?php echo $src; ?>" alt="<?php echo $this->group->get('description'); ?> Logo" />
					</a>
				</div><!-- /#page_identity -->
				
				<ul id="group_options">
					<?php if(in_array($this->user->get("id"), $this->group->get("invitees"))) : ?>
						<?php if($membership_control == 1) : ?>
							<li>
								<a class="group-invited" href="/groups/<?php echo $this->group->get("cn"); ?>/accept">Accept Group Invitation</a>
							</li>
						<?php endif; ?>
					<?php elseif($this->group->get('join_policy') == 3 && !in_array($this->user->get("id"), $this->group->get("members"))) : ?>
						<li><span class="group-closed">Group Closed</span></li>
					<?php elseif($this->group->get('join_policy') == 2 && !in_array($this->user->get("id"), $this->group->get("members"))) : ?>
						<li><span class="group-inviteonly">Group is Invite Only</span></li>
					<?php elseif($this->group->get('join_policy') == 0 && !in_array($this->user->get("id"), $this->group->get("members"))) : ?>
						<?php if($membership_control == 1) : ?> 
							<li>
								<a class="group-join" href="/groups/<?php echo $this->group->get("cn"); ?>/join">Join Group</a>
							</li>
						<?php endif; ?> 
					<?php elseif($this->group->get('join_policy') == 1 && !in_array($this->user->get("id"), $this->group->get("members"))) : ?>
						<?php if($membership_control == 1) : ?>
							<?php if(in_array($this->user->get("id"), $this->group->get("applicants"))) : ?>
								<li><span class="group-pending">Request Waiting Approval</span></li>
							<?php else : ?>
								<li>
									<a class="group-request" href="/groups/<?php echo $this->group->get("cn"); ?>/join">Request Group Membership</a>
								</li>
							<?php endif; ?>
						<?php endif; ?>
					<?php else : ?>
						<?php $isManager = (in_array($this->user->get("id"), $this->group->get("managers"))) ? true : false; ?>
						<?php $canCancel = (($isManager && count($this->group->get("managers")) > 1) || (!$isManager && in_array($this->user->get("id"), $this->group->get("members")))) ? true : false; ?>
						<li class="no-float">
							<a href="javascript:void(0);" class="dropdown group-<?php echo ($isManager) ? "manager" : "member" ?>">
								Group <?php echo ($isManager) ? "Manager" : "Member" ?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu pull-right">
								<?php if($isManager) : ?>
									<?php if($membership_control == 1) : ?> 
										<li><a class="group-invite" href="/groups/<?php echo $this->group->get("cn"); ?>/invite">Invite Members</a></li>
									<?php endif; ?>
									<li><a class="group-edit" href="/groups/<?php echo $this->group->get("cn"); ?>/edit">Edit Group Settings</a></li>
									<li><a class="group-customize" href="/groups/<?php echo $this->group->get("cn"); ?>/customize">Customize Group</a></li>
									<li><a class="group-pages" href="/groups/<?php echo $this->group->get("cn"); ?>/managepages">Manage Group Pages</a></li>
									<?php if($membership_control == 1) : ?> 
										<li class="divider"></li>
									<?php endif; ?>
								<?php endif; ?>
								<?php if($canCancel) : ?>
									<?php if($membership_control == 1) : ?> 
										<li><a class="group-cancel" href="/groups/<?php echo $this->group->get("cn"); ?>/cancel">Cancel Group Membership</a></li>
										<?php if($isManager): ?>
											<li class="divider"></li>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if($isManager) : ?>
									<?php if($membership_control == 1) : ?> 
										<li><a class="group-delete" href="/groups/<?php echo $this->group->get("cn"); ?>/delete">Delete Group</a></li>
									<?php endif; ?>
								<?php endif; ?>
							</ul>
						</li>
					<?php endif; ?>
				</ul><!-- /#page_options -->
				
				<ul id="page_menu">
					<?php
						echo Hubzero_Group_Helper::displayGroupMenu($this->group, $this->sections, $this->hub_group_plugins, $this->group_plugin_access, $this->pages, $this->tab);
					?>
				</ul><!-- /#page_menu -->
				
				<div id="page_info">
					<?php 
						// Determine the join policy
						switch ($this->group->get('join_policy'))
						{
							case 3: $policy = JText::_('Closed');      break;
							case 2: $policy = JText::_('Invite Only'); break;
							case 1: $policy = JText::_('Restricted');  break;
							case 0:
							default: $policy = JText::_('Open'); break;
						}

						// Determine the privacy
						switch ($this->group->get('privacy'))
						{
							case 1: $privacy = JText::_('Hidden'); break;
							case 0:
							default: $privacy = JText::_('Visible'); break;
						}
						
						// Get the group creation date
						$gl = new XGroupLog( $database );
						$gl->getLog( $this->group->get('gidNumber'), 'first' );

						$dateFormat = '%d %b, %Y';
						$timeFormat = '%I:%M %p';
						$tz = 0;
						if (version_compare(JVERSION, '1.6', 'ge'))
						{
							$dateFormat = 'd M, Y';
							$timeFormat = 'h:m a';
							$tz = true;
						}
						$created = JHTML::_('date', $gl->timestamp, $dateFormat, $tz);
					?>
					<div class="group-info">
						<ul>
							<li class="info-discoverability">
								<span class="label">Discoverability</span>
								<span class="value"><?php echo $privacy; ?></span>
							</li>
							<li class="info-join-policy">
								<span class="label">Join Policy</span>
								<span class="value"><?php echo $policy; ?></span>
							</li>
							<li class="info-created">
								<span class="label">Created</span>
								<span class="value"><?php echo $created; ?></span>
							</li>
						</ul>
					</div>
				</div>
			</div><!-- /#page_sidebar --> 
			
			<div id="page_main">
				<?php if($this->group->get('type') == 3) : ?>
					<a href="/home" id="special-group-tab" class="" title="<?php echo $config->getValue("sitename"); ?> :: Learn more about this group page and access to more <?php echo $config->getValue("sitename"); ?> content.">
						<?php echo $config->getValue("sitename"); ?>
						<span></span>
					</a>
				<?php endif; ?>
				<div id="page_header">
					<h2><a href="/groups/<?php echo $this->group->get("cn"); ?>"><?php echo $this->group->get('description'); ?></a></h2>
					<span class="divider">►</span>
					<h3>
						<?php
							foreach($this->hub_group_plugins as $cat)
							{
								if($this->tab == $cat['name'])
								{
									echo $cat['title'];
								}
							}
						?>
					</h3>
					
					<?php
						if($this->tab == 'overview') : 
							$gt = new GroupsTags( $database );
							echo $gt->get_tag_cloud(0,0,$this->group->get('gidNumber'));
						endif;
					?>
				</div><!-- /#page_header -->
				<div id="page_notifications">
					<?php
						foreach($this->notifications as $notification) {
							echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
						}
					?>
				</div><!-- /#page_notifications -->
				
				<div id="page_content" class="group_<?php echo $this->tab; ?>">
					<?php endif; ?>
					
					<?php 
						echo Hubzero_Group_Helper::displayGroupContent($this->sections, $this->hub_group_plugins, $this->tab); 
					?>
					
					<?php if (!$no_html) : ?>
				</div><!-- /#page_content -->
			</div><!-- /#page_main -->
		</div><!-- /#page_container -->
	</div><!-- /.innerwrap -->
<?php endif; ?>
