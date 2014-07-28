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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// get group params
$params = new JParameter($this->group->get('params'));

//is membership control managed on group?
$membership_control = $params->get('membership_control', 1);

// build urls
$currentUrl = JURI::getInstance()->toString();
$groupUrl   = 'index.php?option=com_groups&cn='.$this->group->get('cn');

// build login and logout links
$loginReturn  = base64_encode($currentUrl);
$logoutReturn = base64_encode(JRoute::_($groupUrl));
$loginLink    = JRoute::_('index.php?option=com_users&view=login&return=' . $loginReturn);
$logoutLink   = JRoute::_('index.php?option=com_users&view=logout&return=' . $logoutReturn);

// super group login link
if ($this->group->isSuperGroup())
{
	$loginLink = JRoute::_($groupUrl.'&active=login&return='.base64_encode(JRoute::_($currentUrl)));
}
?>

<ul <?php echo $this->classOrId; ?>>
	<?php if ($this->juser->get('guest') == 1) : ?>
		<li>
			<a class="login" href="<?php echo $loginLink; ?>"><?php echo JText::_('COM_GROUPS_TOOLBAR_LOGIN'); ?></a>
		</li>
	<?php elseif (in_array($this->juser->get("id"), $this->group->get("invitees"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<li>
				<a class="invited" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=accept'); ?>">
					<?php echo JText::_('COM_GROUPS_TOOLBAR_ACCEPT'); ?>
				</a>
			</li>
		<?php endif; ?>
	<?php elseif ($this->group->get('join_policy') == 3 && !in_array($this->juser->get("id"), $this->group->get("members"))) : ?>
		<li>
			<span class="closed"><?php echo JText::_('COM_GROUPS_TOOLBAR_CLOSED'); ?></span>
		</li>
	<?php elseif ($this->group->get('join_policy') == 2 && !in_array($this->juser->get("id"), $this->group->get("members"))) : ?>
		<li>
			<span class="inviteonly"><?php echo JText::_('COM_GROUPS_TOOLBAR_INVITE_ONLY'); ?></span>
		</li>
	<?php elseif ($this->group->get('join_policy') == 0 && !in_array($this->juser->get("id"), $this->group->get("members"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<li>
				<a class="join" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=join'); ?>"><?php echo JText::_('COM_GROUPS_TOOLBAR_JOIN'); ?></a>
			</li>
		<?php endif; ?>
	<?php elseif ($this->group->get('join_policy') == 1 && !in_array($this->juser->get("id"), $this->group->get("members"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<?php if (in_array($this->juser->get("id"), $this->group->get("applicants"))) : ?>
				<li><span class="pending"><?php echo JText::_('COM_GROUPS_TOOLBAR_PENDING'); ?></span></li>
			<?php else : ?>
				<li>
					<a class="request" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=join'); ?>"><?php echo JText::_('COM_GROUPS_TOOLBAR_REQUEST'); ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	<?php else : ?>
		<?php $isManager = (in_array($this->juser->get("id"), $this->group->get("managers"))) ? true : false; ?>
		<?php $canCancel = (($isManager && count($this->group->get("managers")) > 1) || (!$isManager && in_array($this->juser->get("id"), $this->group->get("members")))) ? true : false; ?>
		<li>
			<div class="btn-group <?php echo ($isManager) ? "manager" : "member" ?>">
				<a href="javascript:void(0);" class="btn">
					<?php echo JText::_('COM_GROUPS_GROUP'); ?> <?php echo ($isManager) ? JText::_('COM_GROUPS_TOOLBAR_MANAGER') : JText::_('COM_GROUPS_TOOLBAR_MEMBER') ?>
				</a>
				<span class="btn dropdown-toggle"></span>
				<ul class="dropdown-menu">
					<?php if ($isManager) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-invite" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=invite'); ?>">
									<?php echo JText::_('COM_GROUPS_TOOLBAR_INVITE'); ?>
								</a>
							</li>
						<?php endif; ?>
						<li>
							<a class="group-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=edit'); ?>">
								<?php echo JText::_('COM_GROUPS_TOOLBAR_EDIT'); ?>
							</a>
						</li>
						<li>
							<a class="group-pages" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages'); ?>">
								<?php echo JText::_('COM_GROUPS_TOOLBAR_PAGES'); ?>
							</a>
						</li>
						<?php if ($membership_control == 1) : ?>
							<li class="divider"></li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (!$isManager && \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.invite')) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-invite" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=invite'); ?>">
									<?php echo JText::_('COM_GROUPS_TOOLBAR_INVITE'); ?>
								</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (!$isManager && \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.edit')) : ?>
						<li>
							<a class="group-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=edit'); ?>">
								<?php echo JText::_('COM_GROUPS_TOOLBAR_EDIT'); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if (!$isManager && \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.pages')) : ?>
						<li>
							<a class="group-pages" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages'); ?>">
								<?php echo JText::_('COM_GROUPS_TOOLBAR_PAGES'); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if ($canCancel) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-cancel cancel_group_membership" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=cancel'); ?>">
									<?php echo JText::_('COM_GROUPS_TOOLBAR_CANCEL'); ?>
								</a>
							</li>
							<?php if ($isManager): ?>
								<li class="divider"></li>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ($isManager) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-delete" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=delete'); ?>">
									<?php echo JText::_('COM_GROUPS_TOOLBAR_DELETE'); ?>
								</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ($this->logoutLink) : ?>
						<li class="divider"></li>
						<li>
							<a class="logout" href="<?php echo $logoutLink; ?>"><?php echo JText::_('COM_GROUPS_TOOLBAR_LOGOUT'); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</li>
	<?php endif; ?>
</ul>