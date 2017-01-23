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

// No direct access
defined('_HZEXEC_') or die();

// get group params
$params = new \Hubzero\Config\Registry($this->group->get('params'));

//is membership control managed on group?
$membership_control = $params->get('membership_control', 1);

// build urls
$currentUrl = Request::current(true);
$groupUrl   = 'index.php?option=com_groups&cn='.$this->group->get('cn');

// build login and logout links
$loginReturn  = base64_encode($currentUrl);
$logoutReturn = base64_encode(Route::url($groupUrl));
$loginLink    = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn);
$logoutLink   = Route::url('index.php?option=com_users&view=logout&return=' . $logoutReturn);

// super group login link
if ($this->group->isSuperGroup())
{
	$loginLink = Route::url($groupUrl.'&active=login&return='.base64_encode(Route::url($currentUrl)));
}
?>

<ul <?php echo $this->classOrId; ?>>
	<?php if (User::isGuest() == 1) : ?>
		<li>
			<a class="login btn" href="<?php echo $loginLink; ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_LOGIN'); ?></a>
		</li>
	<?php elseif (in_array(User::get("id"), $this->group->get("invitees"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<li>
				<a class="invited btn btn-success icon" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=accept'); ?>">
					<?php echo Lang::txt('COM_GROUPS_TOOLBAR_ACCEPT'); ?>
				</a>
			</li>
			<li>
				<a class="invited btn btn-secondary" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=cancel'); ?>">
					<?php echo Lang::txt('COM_GROUPS_TOOLBAR_DECLINE'); ?>
				</a>
			</li>
		<?php endif; ?>
	<?php elseif ($this->group->get('join_policy') == 3 && !in_array(User::get("id"), $this->group->get("members"))) : ?>
		<li>
			<span class="closed"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_CLOSED'); ?></span>
		</li>
	<?php elseif ($this->group->get('join_policy') == 2 && !in_array(User::get("id"), $this->group->get("members"))) : ?>
		<li>
			<span class="inviteonly"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_INVITE_ONLY'); ?></span>
		</li>
	<?php elseif ($this->group->get('join_policy') == 0 && !in_array(User::get("id"), $this->group->get("members"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<li>
				<a class="join btn" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=join'); ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_JOIN'); ?></a>
			</li>
		<?php endif; ?>
	<?php elseif ($this->group->get('join_policy') == 1 && !in_array(User::get("id"), $this->group->get("members"))) : ?>
		<?php if ($membership_control == 1) : ?>
			<?php if (in_array(User::get("id"), $this->group->get("applicants"))) : ?>
				<li><span class="pending"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_PENDING'); ?></span></li>
			<?php else : ?>
				<li>
					<a class="request btn" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=join'); ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_REQUEST'); ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	<?php else : ?>
		<?php $isManager = (in_array(User::get("id"), $this->group->get("managers"))) ? true : false; ?>
		<?php $canCancel = (($isManager && count($this->group->get("managers")) > 1) || (!$isManager && in_array(User::get("id"), $this->group->get("members")))) ? true : false; ?>
		<li>
			<div class="btn-group <?php echo ($isManager) ? "manager" : "member" ?>">
				<a class="btn" href="javascript:void(0);">
					<?php echo Lang::txt('COM_GROUPS_GROUP'); ?> <?php echo ($isManager) ? Lang::txt('COM_GROUPS_TOOLBAR_MANAGER') : Lang::txt('COM_GROUPS_TOOLBAR_MEMBER') ?>
				</a>
				<span class="btn dropdown-toggle"></span>
				<ul class="dropdown-menu">
				<?php if ($this->group->get('published') != 2) : ?>
					<?php if ($isManager) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-invite" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=invite'); ?>">
									<?php echo Lang::txt('COM_GROUPS_TOOLBAR_INVITE'); ?>
								</a>
							</li>
						<?php endif; ?>
						<li>
							<a class="group-edit" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=edit'); ?>">
								<?php echo Lang::txt('COM_GROUPS_TOOLBAR_EDIT'); ?>
							</a>
						</li>
						<li>
							<a class="group-pages" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages'); ?>">
								<?php echo Lang::txt('COM_GROUPS_TOOLBAR_PAGES'); ?>
							</a>
						</li>
						<?php if ($membership_control == 1) : ?>
							<li class="divider"></li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (!$isManager && Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.invite')) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-invite" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=invite'); ?>">
									<?php echo Lang::txt('COM_GROUPS_TOOLBAR_INVITE'); ?>
								</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (!$isManager && Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.edit')) : ?>
						<li>
							<a class="group-edit" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=edit'); ?>">
								<?php echo Lang::txt('COM_GROUPS_TOOLBAR_EDIT'); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if (!$isManager && Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.pages')) : ?>
						<li>
							<a class="group-pages" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages'); ?>">
								<?php echo Lang::txt('COM_GROUPS_TOOLBAR_PAGES'); ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>

					<?php if ($canCancel) : ?>
						<?php if ($membership_control == 1) : ?>
							<li>
								<a class="group-cancel cancel_group_membership" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=cancel'); ?>">
									<?php echo Lang::txt('COM_GROUPS_TOOLBAR_CANCEL'); ?>
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
								<a class="group-delete" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=delete'); ?>">
									<?php echo Lang::txt('COM_GROUPS_TOOLBAR_DELETE'); ?>
								</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ($this->logoutLink) : ?>
						<li class="divider"></li>
						<li>
							<a class="logout" href="<?php echo $logoutLink; ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_LOGOUT'); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</li>
	<?php endif; ?>
</ul>
