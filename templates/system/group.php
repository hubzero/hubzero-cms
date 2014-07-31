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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// get needed objects
$config = JFactory::getConfig();
$juser  = JFactory::getUser();
$group  = \Hubzero\User\Group::getInstance(JRequest::getCmd('cn', ''));

// return url (if any)
$return = DS . trim(str_replace(JURI::base(),'', JURI::current()), DS);

// include frameworks
JHTML::_('behavior.framework', true);
JHTML::_('behavior.modal');

// include group script
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/group.js');

// get browser agent
$browser = new \Hubzero\Browser\Detector();
$p = strtolower(str_replace(' ', '', $browser->platform()));
$b = $browser->name();
$v = $browser->major();

// determine if we are a group member or manager
$isMember  = false;
$isManager = false;
$isPending = false;
$isInvitee = false;
if (in_array($juser->get('id'), $group->get('managers')))
{
	$isManager = true;
}
elseif (in_array($juser->get('id'), $group->get('members')))
{
	$isMember = true;
}
elseif (in_array($juser->get('id'), $group->get('applicants')))
{
	$isPending = true;
}
elseif (in_array($juser->get('id'), $group->get('invitees')))
{
	$isInvitee = true;
}

//is membership control managed on group?
$params = new JParameter($group->get('params'));
$membership_control = $params->get('membership_control', 1);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'comments', 'voting', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/group.css" type="text/css" />
		<jdoc:include type="head" />
	</head>
	<body class="contentpane" id="group-body">
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<div class="super-group-bar">
			<div class="grid">
				<div class="col span4">
					<a href="<?php echo $this->baseurl; ?>/" class="poweredby">
						<span><?php echo $config->getValue('sitename'); ?></span>
					</a>
				</div>
				<div class="col span8 omega">
					<p id="tab">
						<a href="/support/" title="<?php echo JText::_('TPL_SYSTEM_HELP_HINT'); ?>">
							<span><?php echo JText::_('TPL_SYSTEM_HELP'); ?></span>
						</a>
					</p>
				
					<div id="group" role="navigation">
						<?php if ($isManager) : ?>
							<a href="javascript:void(0);" class="manager toggle">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_MANAGER'); ?></span>
							</a>
						<?php elseif ($isMember) : ?>
							<a href="javascript:void(0);" class="member toggle">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_MEMBER'); ?></span>
							</a>
						<?php elseif ($isInvitee && $membership_control == 1) : ?>
							<a href="javascript:void(0);" class="invitee toggle">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_INVITEE'); ?></span>
							</a>
						<?php elseif ($isPending) : ?>
							<a href="javascript:void(0);" class="pending toggle">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_PENDING'); ?></span>
							</a>
						<?php elseif ($group->get('join_policy') == 3) : ?>
							<a href="javascript:void(0);" class="closed">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_CLOSED'); ?></span>
							</a>
						<?php elseif ($group->get('join_policy') == 2) : ?>
							<a href="javascript:void(0);" class="inviteonly">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_INVITE_ONLY'); ?></span>
							</a>
						<?php elseif ($group->get('join_policy') == 1 && $membership_control == 1) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=join'); ?>" class="restricted">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_RESTRICTED'); ?></span>
							</a>
						<?php elseif ($group->get('join_policy') == 0 && $membership_control == 1) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=join'); ?>" class="open">
								<span><?php echo JText::_('TPL_SYSTEM_GROUP_JOIN'); ?></span>
							</a>	
						<?php endif; ?>
					</div>

					<div id="account" role="navigation">
						<?php if (!$juser->get('guest')) : ?>
							<?php $profile = \Hubzero\User\Profile::getInstance($juser->get('id')); ?>
							<ul class="menu loggedin">
								<li>
									<div id="account-info">
										<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
											<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo $juser->get('name'); ?>" />
											<span class="account-name"><?php echo stripslashes($profile->get('name')); ?></span>
											<span class="account-email"><?php echo $profile->get('email'); ?></span>
										</a>
									</div>
									<ul>
										<li id="account-dashboard">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_DASHBOARD'); ?></span>
											</a>
										</li>
										<li id="account-profile">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_PROFILE'); ?></span>
											</a>
										</li>
										<li id="account-messages">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_MESSAGES'); ?></span>
											</a>
										</li>
										<li id="account-logout">
											<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout&return=' . base64_encode($return)); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_LOGOUT'); ?></span>
											</a>
										</li>
									</ul>
								</li>
							</ul>
						<?php else : ?>
							<ul class="menu loggedout">
								<?php if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') != '0') : ?>
									<li id="account-register">
										<a href="<?php echo JRoute::_('index.php?option=com_members&controller=register&return=' . base64_encode($return)); ?>" title="<?php echo JText::_('TPL_SYSTEM_REGISTER'); ?>">
											<?php echo JText::_('TPL_SYSTEM_REGISTER'); ?>
										</a>
									</li>
								<?php endif; ?>
								<li id="account-login">
									<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.JRequest::getCmd('cn','').'&task=login&return=' . base64_encode($return)); ?>" title="<?php echo JText::_('TPL_SYSTEM_LOGIN'); ?>">
										<?php echo JText::_('TPL_SYSTEM_LOGIN'); ?>
									</a>
								</li>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div id="group-info">
			<a class="close" href="#"><?php echo JText::_('TPL_SYSTEM_GROUP_INFO_CLOSE'); ?></a>
			<div class="links-header">
				<?php if ($isManager) : ?>
					<?php echo JText::_('TPL_SYSTEM_GROUPS_MANAGER_DASHBOARD'); ?>
					<span><?php echo JText::_('TPL_SYSTEM_GROUPS_MANAGER_DASHBOARD_DESC'); ?></span>
				<?php elseif ($isMember) : ?>
					<?php echo JText::_('TPL_SYSTEM_GROUPS_MEMBER_DASHBOARD'); ?>
					<span><?php echo JText::_('TPL_SYSTEM_GROUPS_MEMBER_DASHBOARD_DESC'); ?></span>
				<?php elseif ($isPending) : ?>
					<?php echo JText::_('TPL_SYSTEM_GROUPS_PENDING_DASHBOARD'); ?>
					<span><?php echo JText::_('TPL_SYSTEM_GROUPS_PENDING_DASHBOARD_DESC'); ?></span>
				<?php elseif ($isInvitee) : ?>
					<?php echo JText::_('TPL_SYSTEM_GROUPS_INVITEE_DASHBOARD'); ?>
					<span><?php echo JText::_('TPL_SYSTEM_GROUPS_INVITEE_DASHBOARD_DESC'); ?></span>
				<?php endif; ?>
			</div>
			<ul class="links cf">

				<?php if ($isInvitee && $membership_control == 1) : ?>
					<li>
						<a class="accept" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=accept'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_ACCEPT'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_ACCEPT_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($isPending && $membership_control == 1) : ?>
					<li>
						<a class="cancel-request" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=cancel'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_CANCEL_REQUEST'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_CANCEL_REQUEST_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.invite')) && $membership_control == 1) : ?>
					<li>
						<a class="membership" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active=members'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_INVITE'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_INVITE_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.edit')) : ?>
					<li>
						<a class="settings" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=edit'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_EDIT'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_EDIT_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.pages')) : ?>
					<li>
						<a class="pages" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=pages'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_PAGES'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_PAGES_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (($isMember || ($isManager && count($group->get('managers')) > 1)) && $membership_control == 1) : ?>
					<li>
						<a class="cancel" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=cancel'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_CANCEL'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_CANCEL_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($isManager && $membership_control == 1) : ?>
					<li>
						<a class="delete danger" href="<?php echo JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&task=delete'); ?>">
							<?php echo JText::_('TPL_SYSTEM_GROUP_DELETE'); ?>
							<span><?php echo JText::_('TPL_SYSTEM_GROUP_DELETE_DESC'); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<jdoc:include type="message" />
		<jdoc:include type="component" />
		<jdoc:include type="modules" name="endpage" />
	</body>
</html>