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

$juser = JFactory::getUser();
$no_html = JRequest::getInt('no_html', 0);
$user_messaging = $this->config->get('user_messaging', 0);

$prefix = $this->profile->get("name") . "'s";
$edit = false;
$password = false;
$messaging = false;

$tab = $this->tab;
$tab_name = "Dashboard";

//are we allowed to messagin user
switch ($user_messaging)
{
	case 0:
		$mssaging = false;
		break;
	case 1:
		$common = \Hubzero\User\Helper::getCommonGroups($juser->get("id"), $this->profile->get('uidNumber'));
		if (count($common) > 0)
		{
			$messaging = true;
		}
		break;
	case 2:
		$messaging = true;
		break;
}

//if user is this member turn on editing and password change, turn off messaging
if ($this->profile->get('uidNumber') == $juser->get("id"))
{
	if ($this->tab == "profile")
	{
		$edit = true;
		$password = true;
	}
	$messaging = false;
	$prefix = "My";
}

//no messaging if guest
if ($juser->get("guest"))
{
	$messaging = false;
}

if (!$no_html)
{
	$this->css()
	     ->js();
?>
<div class="innerwrap">
	<div id="page_container">
		<div id="page_sidebar">
			<?php
				$src = \Hubzero\User\Profile\Helper::getMemberPhoto($this->profile, 0, false);
				$link = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber'));
			?>
			<div id="page_identity">
				<?php $title = ($this->profile->get('uidNumber') == $juser->get("id")) ? JText::_('COM_MEMBERS_GO_TO_MY_DASHBOARD') : JText::sprintf('COM_MEMBERS_GO_TO_MEMBER_PROFILE', $this->profile->get('name')); ?>
				<a href="<?php echo $link; ?>" id="page_identity_link" title="<?php echo $title; ?>">
					<img src="<?php echo $src; ?>" alt="<?php echo JText::sprintf('COM_MEMBERS_PROFILE_PICTURE_FOR', $this->escape(stripslashes($this->profile->get('name')))); ?>" />
				</a>
			</div><!-- /#page_identity -->
			<?php if ($messaging): ?>
			<ul id="member_options">
				<li>
					<a class="message tooltips" title="<?php echo JText::_('COM_MEMBERS_MESSAGE'); ?> :: <?php echo JText::sprintf('COM_MEMBERS_SEND_A_MESSAGE_TO', $this->escape(stripslashes($this->profile->get('name')))); ?>" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get("id") . '&active=messages&task=new&to[]=' . $this->profile->get('uidNumber')); ?>">
						<?php echo JText::_('COM_MEMBERS_MESSAGE'); ?>
					</a>
				</li>
			</ul>
			<?php endif; ?>
			<ul id="page_menu">
				<?php foreach ($this->cats as $k => $c) : ?>
					<?php
						$key = key($c);
						if (!$key)
						{
							continue;
						}
						$name = $c[$key];
						$url = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->profile->get('uidNumber') . '&active=' . $key);
						$cls = ($this->tab == $key) ? 'active' : '';
						$tab_name = ($this->tab == $key) ? $name : $tab_name;

						$metadata = $this->sections[$k]['metadata'];
						$meta_count = (isset($metadata['count']) && $metadata['count'] != "") ? $metadata['count'] : "";
						if (isset($metadata['alert']) && $metadata['alert'] != "")
						{
							$meta_alert = $metadata['alert'];
							$cls .= ' with-alert';
						}
						else
						{
							$meta_alert = '';
						}

						if (!isset($c['icon']))
						{
							$c['icon'] = 'f009';
						}
					?>
					<li class="<?php echo $cls; ?>">
						<a class="<?php echo $key; ?>" data-icon="<?php echo '&#x' . $c['icon']; ?>;" title="<?php echo $prefix . ' ' . $name; ?>" href="<?php echo $url; ?>">
							<?php echo $name; ?>
						</a>
						<span class="meta">
							<?php if ($meta_count) : ?>
								<span class="count"><?php echo $meta_count; ?></span>
							<?php endif; ?>
						</span>
						<?php echo $meta_alert; ?>
					</li>
				<?php endforeach; ?>
			</ul><!-- /#page_menu -->

			<?php
				$thumb = '/site/stats/contributor_impact/impact_' . $this->profile->get('uidNumber') . '_th.gif';
				$full = '/site/stats/contributor_impact/impact_' . $this->profile->get('uidNumber') . '.gif';
			?>
			<?php if (file_exists(JPATH_ROOT . $thumb)) : ?>
				<a id="member-stats-graph" rel="lightbox" title="<?php echo JText::sprintf('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" data-name="<?php echo $this->profile->get("name"); ?>" data-type="Impact Graph" href="<?php echo $full; ?>">
					<img src="<?php echo $thumb; ?>" alt="<?php echo JText::sprintf('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" />
				</a>
			<?php endif; ?>

		</div><!-- /#page_sidebar -->
		<div id="page_main">
<?php if ($edit || $password) : ?>
			<ul id="page_options">
				<?php if ($edit) : ?>
					<li>
						<a class="edit tooltips" id="edit-profile" title="<?php echo JText::_('COM_MEMBERS_EDIT_PROFILE'); ?> :: Edit <?php if ($this->profile->get('uidNumber') == $juser->get("id")) { echo "my"; } else { echo $this->profile->get("name") . "'s"; } ?> profile." href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->profile->get('uidNumber') . '&task=edit'); ?>">
							<?php echo JText::_('COM_MEMBERS_EDIT_PROFILE'); ?>
						</a>
					</li>
				<?php endif; ?>
				<?php if ($password) : ?>
					<li>
						<a class="password tooltips" id="change-password" title="<?php echo JText::_('COM_MEMBERS_CHANGE_PASSWORD'); ?> :: Change your password" href="<?php echo JRoute::_('index.php?option=com_members&task=changepassword&id=' . $this->profile->get('uidNumber')); ?>">
							<?php echo JText::_('COM_MEMBERS_CHANGE_PASSWORD'); ?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
<?php endif; ?>
			<div id="page_header">
				<?php if ($this->profile->get('uidNumber') == $juser->get('id')) : ?>
					<?php
						$cls = '';
						$span_title = JText::_('COM_MEMBERS_PUBLIC_PROFILE_TITLE');
						$title = JText::_('COM_MEMBERS_PUBLIC_PROFILE_SET_PRIVATE_TITLE');
						if ($this->profile->get('public') != 1)
						{
							$cls = 'private';
							$span_title = JText::_('COM_MEMBERS_PRIVATE_PROFILE_TITLE');
							$title = JText::_('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
						}
					?>

					<?php if ($this->tab == 'profile') : ?>
						<a id="profile-privacy" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->profile->get('uidNumber')); ?>" data-uidnumber="<?php echo $this->profile->get('uidNumber'); ?>" class="<?php echo $cls; ?> tooltips" title="<?php echo $title; ?>">
							<?php echo $title; ?>
						</a>
					<?php else: ?>
						<span id="profile-privacy">
							<?php echo $span_title; ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>

				<h2>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->profile->get('uidNumber')); ?>">
						<?php echo $this->escape(stripslashes($this->profile->get('name'))); ?>
					</a>
				</h2>
				<span>►</span>
				<h3><?php echo $tab_name; ?></h3>
			</div>
			<div id="page_notifications">
				<?php
					if ($this->getError())
					{
						echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
					}
				?>
			</div>
			<div id="page_content" class="member_<?php echo $this->tab; ?>">
				<?php
					}
					if ($this->overwrite_content)
					{
						echo $this->overwrite_content;
					}
					else
					{
						foreach ($this->sections as $s)
						{
							if ($s['html'] != '')
							{
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
