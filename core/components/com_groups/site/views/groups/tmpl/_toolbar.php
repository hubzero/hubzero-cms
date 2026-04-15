<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
$logoutLink   = Route::url('index.php?option=com_users&view=login&task=logout&return=' . $logoutReturn);

// super group login link
if ($this->group->isSuperGroup())
{
	$loginLink = Route::url($groupUrl.'&active=login&return='.base64_encode(Route::url($currentUrl)));
}
?>

<ul <?php echo $this->classOrId; ?>>
	<?php if (User::isGuest() == 1) : ?>
		<li>
			<a class="login btn" href="<?php echo $loginLink; ?>" aria-label="<?php echo Lang::txt('COM_GROUPS_TOOLBAR_LOGIN_ARIA'); ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_LOGIN'); ?></a>
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
			<span class="closed" role="status"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_CLOSED'); ?></span>
		</li>
	<?php elseif ($this->group->get('join_policy') == 2 && !in_array(User::get("id"), $this->group->get("members"))) : ?>
		<li>
			<span class="inviteonly" role="status"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_INVITE_ONLY'); ?></span>
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
				<li><span class="pending" role="status"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_PENDING'); ?></span></li>
			<?php else : ?>
				<li>
					<a class="request btn" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=join'); ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_REQUEST'); ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	<?php else : ?>
		<?php
		$isManager = in_array(User::get("id"), $this->group->get("managers"));
		$canCancel = ($isManager && count($this->group->get("managers")) > 1)
		          || (!$isManager && in_array(User::get("id"), $this->group->get("members")));
		$cn = $this->group->get('cn');

		// Build menu items array so we can skip the <ul role="menu"> when empty
		$menuItems = array();

		if ($this->group->get('published') != 2)
		{
			if ($isManager)
			{
				if ($membership_control == 1 && $this->group->get('join_policy') != 3)
				{
					$menuItems[] = '<li role="none"><a role="menuitem" class="group-invite" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=invite') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_INVITE') . '</a></li>';
				}
				$menuItems[] = '<li role="none"><a role="menuitem" class="group-edit" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=edit') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_EDIT') . '</a></li>';
				$menuItems[] = '<li role="none"><a role="menuitem" class="group-pages" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=pages') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_PAGES') . '</a></li>';
				if ($membership_control == 1)
				{
					$menuItems[] = '<li class="divider" role="separator"></li>';
				}
			}
			else
			{
				if ($membership_control == 1 && Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.invite'))
				{
					$menuItems[] = '<li role="none"><a role="menuitem" class="group-invite" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=invite') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_INVITE') . '</a></li>';
				}
				if (Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.edit'))
				{
					$menuItems[] = '<li role="none"><a role="menuitem" class="group-edit" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=edit') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_EDIT') . '</a></li>';
				}
				if (Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.pages'))
				{
					$menuItems[] = '<li role="none"><a role="menuitem" class="group-pages" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=pages') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_PAGES') . '</a></li>';
				}
			}
		}

		if ($canCancel && $membership_control == 1)
		{
			$menuItems[] = '<li role="none"><a role="menuitem" class="group-cancel cancel_group_membership" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=cancel') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '</a></li>';
			if ($isManager)
			{
				$menuItems[] = '<li class="divider" role="separator"></li>';
			}
		}

		if ($isManager && $membership_control == 1)
		{
			$menuItems[] = '<li role="none"><a role="menuitem" class="group-delete" href="' . Route::url('index.php?option=com_groups&cn=' . $cn . '&task=delete') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_DELETE') . '</a></li>';
		}

		if ($this->logoutLink)
		{
			$menuItems[] = '<li class="divider" role="separator"></li>';
			$menuItems[] = '<li role="none"><a role="menuitem" class="logout" href="' . $logoutLink . '">' . Lang::txt('COM_GROUPS_TOOLBAR_LOGOUT') . '</a></li>';
		}
		?>
		<li>
			<div class="btn-group <?php echo ($isManager) ? "manager" : "member" ?>">
				<button type="button" class="btn dropdown-label" aria-expanded="false" aria-haspopup="true">
					<?php echo Lang::txt('COM_GROUPS_GROUP'); ?> <?php echo ($isManager) ? Lang::txt('COM_GROUPS_TOOLBAR_MANAGER') : Lang::txt('COM_GROUPS_TOOLBAR_MEMBER') ?>
				</button>
				<?php if (!empty($menuItems)) : ?>
					<button type="button" class="btn dropdown-toggle" aria-expanded="false" aria-haspopup="true" aria-label="<?php echo Lang::txt('COM_GROUPS_TOOLBAR_MORE_OPTIONS'); ?>">
						<span class="sr-only"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_MORE_OPTIONS'); ?></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<?php echo implode("\n\t\t\t\t\t", $menuItems); ?>
					</ul>
				<?php endif; ?>
			</div>
		</li>
	<?php endif; ?>
</ul>
