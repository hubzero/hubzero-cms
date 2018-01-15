<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// get needed objects
$group  = \Hubzero\User\Group::getInstance(Request::getCmd('cn', ''));

// return url (if any)
$return = '/' . trim(str_replace(Request::base(),'', Request::current()), '/');

// include frameworks
Html::behavior('framework', true);
Html::behavior('modal');

// include group script
//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');
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
if (in_array(User::get('id'), $group->get('managers')))
{
	$isManager = true;
}
elseif (in_array(User::get('id'), $group->get('members')))
{
	$isMember = true;
}
elseif (in_array(User::get('id'), $group->get('applicants')))
{
	$isPending = true;
}
elseif (in_array(User::get('id'), $group->get('invitees')))
{
	$isInvitee = true;
}

//is membership control managed on group?
$params = new \Hubzero\Config\Registry($group->get('params'));
$membership_control = $params->get('membership_control', 1);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
<head>
	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500" type="text/css" />
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/main.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/group.css" type="text/css" />
	<jdoc:include type="head" />
</head>
<body class="contentpane" id="group-body">
<jdoc:include type="modules" name="notices" />

<div class="super-group-bar">
	<div class="content cf">
		<div class="branding">
			<a href="<?php echo Request::root(); ?>" class="poweredby">
				Powered by <span><?php echo Config::get('sitename'); ?></span>
			</a>
		</div>
		<div>
			<ul class="subnav">
				<li class="user-account loggedin" id="account">
					<?php
					if (!User::isGuest())
					{
						$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
						?>
						<a class="user-account-link loggedin icon" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
							Logged in
						</a>
						<div class="account-details">
							<div class="user-info">
								<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>" class="cf">
											<span class="user-image">
												<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo User::get('name'); ?>" />
											</span>

									<p>
										<span class="account-name"><?php echo stripslashes(User::get('name')) . ' (' . stripslashes(User::get('username')) . ')'; ?></span><br>
										<span class="account-email"><?php echo User::get('email'); ?></span>
									</p>
								</a>
							</div>
							<ul>
								<li id="account-dashboard">
									<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>"><span><?php echo Lang::txt('TPL_SYSTEM_ACCOUNT_DASHBOARD'); ?></span></a>
								</li>
								<li id="account-profile">
									<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'); ?>"><span><?php echo Lang::txt('TPL_SYSTEM_ACCOUNT_PROFILE'); ?></span></a>
								</li>
								<li id="account-logout">
									<a href="<?php echo Route::url('index.php?option=com_users&view=logout'); ?>"><span><?php echo Lang::txt('TPL_SYSTEM_LOGOUT'); ?></span></a>
								</li>
							</ul>

							<button class="close">
								<span>close</span>
							</button>
						</div>
					<?php
					}
					else
					{
					?>
						<a class="user-account-link" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return)); ?>" title="<?php echo Lang::txt('TPL_SYSTEM_LOGIN'); ?>">
							<?php echo Lang::txt('TPL_SYSTEM_LOGIN'); ?>
						</a>
					<?php
					}
					?>
				</li>
				<li class="subnav-membership">
					<?php if ($isManager) : ?>
						<a href="javascript:void(0);" class="manager toggle">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_MANAGER'); ?></span>
						</a>
					<?php elseif ($isMember) : ?>
						<a href="javascript:void(0);" class="member toggle">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_MEMBER'); ?></span>
						</a>
					<?php elseif ($isInvitee && $membership_control == 1) : ?>
						<a href="javascript:void(0);" class="invitee toggle">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_INVITEE'); ?></span>
						</a>
					<?php elseif ($isPending) : ?>
						<a href="javascript:void(0);" class="pending toggle">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_PENDING'); ?></span>
						</a>
					<?php elseif ($group->get('join_policy') == 3) : ?>
						<a href="javascript:void(0);" class="closed">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_CLOSED'); ?></span>
						</a>
					<?php elseif ($group->get('join_policy') == 2) : ?>
						<a href="javascript:void(0);" class="inviteonly">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_INVITE_ONLY'); ?></span>
						</a>
					<?php elseif ($group->get('join_policy') == 1 && $membership_control == 1) : ?>
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=join'); ?>" class="restricted">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_RESTRICTED'); ?></span>
						</a>
					<?php elseif ($group->get('join_policy') == 0 && $membership_control == 1 && !User::isGuest()) : ?>
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=join'); ?>" class="open">
							<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_JOIN'); ?></span>
						</a>
					<?php endif; ?>

					<div class="group-dash">
						<div class="links-header">
							<?php if ($isManager) : ?>
								<?php echo Lang::txt('TPL_SYSTEM_GROUPS_MANAGER_DASHBOARD'); ?>
								<span><?php echo Lang::txt('TPL_SYSTEM_GROUPS_MANAGER_DASHBOARD_DESC'); ?></span>
							<?php elseif ($isMember) : ?>
								<?php echo Lang::txt('TPL_SYSTEM_GROUPS_MEMBER_DASHBOARD'); ?>
								<span><?php echo Lang::txt('TPL_SYSTEM_GROUPS_MEMBER_DASHBOARD_DESC'); ?></span>
							<?php elseif ($isPending) : ?>
								<?php echo Lang::txt('TPL_SYSTEM_GROUPS_PENDING_DASHBOARD'); ?>
								<span><?php echo Lang::txt('TPL_SYSTEM_GROUPS_PENDING_DASHBOARD_DESC'); ?></span>
							<?php elseif ($isInvitee) : ?>
								<?php echo Lang::txt('TPL_SYSTEM_GROUPS_INVITEE_DASHBOARD'); ?>
								<span><?php echo Lang::txt('TPL_SYSTEM_GROUPS_INVITEE_DASHBOARD_DESC'); ?></span>
							<?php endif; ?>
						</div>
						<ul class="links cf">
							<?php if ($isInvitee && $membership_control == 1) : ?>
								<li>
									<a class="accept" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=accept'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_ACCEPT'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_ACCEPT_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if ($isPending && $membership_control == 1) : ?>
								<li>
									<a class="cancel-request" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=cancel'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_CANCEL_REQUEST'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_CANCEL_REQUEST_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if (($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.invite')) && $membership_control == 1) : ?>
								<li>
									<a class="membership" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&active=members'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_INVITE'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_INVITE_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if ($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.edit')) : ?>
								<li>
									<a class="settings" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=edit'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_EDIT'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_EDIT_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if ($isManager || \Hubzero\User\Profile::userHasPermissionForGroupAction($group, 'group.pages')) : ?>
								<li>
									<a class="pages" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=pages'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_PAGES'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_PAGES_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if (($isMember || ($isManager && count($group->get('managers')) > 1)) && $membership_control == 1) : ?>
								<li>
									<a class="cancel" href="<?php echo Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&task=cancel'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_CANCEL'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_CANCEL_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if ($isManager && $membership_control == 1) : ?>
								<li>
									<a class="delete danger" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&task=delete'); ?>">
										<?php echo Lang::txt('TPL_SYSTEM_GROUP_DELETE'); ?>
										<span><?php echo Lang::txt('TPL_SYSTEM_GROUP_DELETE_DESC'); ?></span>
									</a>
								</li>
							<?php endif; ?>
						</ul>

						<button class="close">
							<span>close</span>
						</button>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="hub-overlay"></div>

<jdoc:include type="message" />
<jdoc:include type="component" />
<jdoc:include type="modules" name="endpage" />
</body>
</html>
