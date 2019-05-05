<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);
$user_messaging = (Plugin::isEnabled('members', 'messages') ? $this->config->get('user_messaging', 0) : 0);

$prefix = $this->profile->get('name') . "'s";
$edit = false;
$password = false;
$messaging = false;

$tab = $this->active;
$tab_name = 'Dashboard';

//are we allowed to messagin user
switch ($user_messaging)
{
	case 0:
		$messaging = false;
		break;
	case 1:
		$common = \Hubzero\User\Helper::getCommonGroups(User::get("id"), $this->profile->get('id'));
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
if ($this->profile->get('id') == User::get("id"))
{
	if ($this->active == 'profile')
	{
		$edit = true;
		$password = true;
	}
	$messaging = false;
	$prefix = 'My';
}

// No messaging if guest or account has an invalid email (incomplete 3rd-party registration)
if (User::isGuest() || substr($this->profile->get('email'), -8) == '@invalid')
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
			<div id="page_identity">
				<?php $title = ($this->profile->get('id') == User::get('id')) ? Lang::txt('COM_MEMBERS_GO_TO_MY_DASHBOARD') : Lang::txt('COM_MEMBERS_GO_TO_MEMBER_PROFILE', $this->profile->get('name')); ?>
				<a href="<?php echo Route::url($this->profile->link()); ?>" id="page_identity_link" title="<?php echo $title; ?>">
					<img src="<?php echo $this->profile->picture(0, false); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_PROFILE_PICTURE_FOR', $this->escape(stripslashes($this->profile->get('name')))); ?>" class="profile-pic full" />
				</a>
			</div><!-- /#page_identity -->
			<?php if ($messaging): ?>
				<ul id="member_options">
					<li class="message-member">
						<a class="tooltips" title="<?php echo Lang::txt('COM_MEMBERS_MESSAGE'); ?> :: <?php echo Lang::txt('COM_MEMBERS_SEND_A_MESSAGE_TO', $this->escape(stripslashes($this->profile->get('name')))); ?>" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get("id") . '&active=messages&task=new&to[]=' . $this->profile->get('id')); ?>">
							<?php echo Lang::txt('COM_MEMBERS_MESSAGE'); ?>
						</a>
					</li>
				</ul>
			<?php endif; ?>
			<?php
			$results = Event::trigger('members.onMemberProfile', array($this->profile));
			$results = implode("\n", $results);

			if ($results)
			{
				echo '<div class="member-extensions">' . $results . '</div>';
			}
			?>
			<ul id="page_menu">
				<?php foreach ($this->cats as $k => $c) : ?>
					<?php
					$key = key($c);
					if (!$key)
					{
						continue;
					}
					if (isset($c['menu']) && !$c['menu'])
					{
						continue;
					}
					$name = $c[$key];
					$url = Route::url($this->profile->link() . '&active=' . $key);
					$cls = ($this->active == $key) ? 'active' : '';
					$tab_name = ($this->active == $key) ? $name : $tab_name;

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
							<?php echo $meta_alert; ?>
						</span>
						<?php if (isset($metadata['options']) && is_array($metadata['options'])) : ?>
							<ul class="tab-options">
								<?php
								foreach ($metadata['options'] as $option)
								{
									if (!isset($option['text']))
									{
										if (!isset($option['title']))
										{
											continue;
										}
										$option['text'] = $option['title'];
									}

									$attribs = array();
									foreach ($option as $key => $val)
									{
										if ($key == 'text')
										{
											continue;
										}

										$attribs[] = $key . '="' . $this->escape($val) . '"';
									}

									echo '<li><a ' . implode(' ', $attribs) . '>' . $this->escape($option['text']) . '</a></li>';
								}
								?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul><!-- /#page_menu -->

			<?php
			$thumb = '/site/stats/contributor_impact/impact_' . $this->profile->get('id') . '_th.gif';
			$full  = '/site/stats/contributor_impact/impact_' . $this->profile->get('id') . '.gif';
			?>
			<?php if (file_exists(PATH_APP . $thumb)) : ?>
				<a id="member-stats-graph" rel="lightbox" title="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" data-name="<?php echo $this->profile->get('name'); ?>" data-type="Impact Graph" href="<?php echo with(new \Hubzero\Content\Moderator(PATH_APP . $full, 'public'))->getUrl(); ?>">
					<img src="<?php echo with(new \Hubzero\Content\Moderator(PATH_APP . $thumb, 'public'))->getUrl(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" />
				</a>
			<?php endif; ?>
		</div><!-- /#page_sidebar -->
		<div id="page_main">
			<?php if ($edit || $password) : ?>
				<ul id="page_options">
					<?php if ($edit) : ?>
						<li>
							<a class="edit tooltips" id="edit-profile" title="<?php echo Lang::txt('COM_MEMBERS_EDIT_PROFILE'); ?> :: Edit <?php echo ($this->profile->get('id') == User::get("id")) ? "my" : $this->profile->get("name") . "'s"; ?> profile." href="<?php echo Route::url($this->profile->link() . '&task=edit'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_EDIT_PROFILE'); ?>
							</a>
						</li>
					<?php endif; ?>
					<?php if ($password) : ?>
						<li>
							<a class="password tooltips" id="change-password" title="<?php echo Lang::txt('COM_MEMBERS_CHANGE_PASSWORD'); ?> :: <?php echo Lang::txt('Change your password'); ?>" href="<?php echo Route::url($this->profile->link('changepassword')); ?>">
								<?php echo Lang::txt('COM_MEMBERS_CHANGE_PASSWORD'); ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			<div id="page_header">
				<?php if ($this->profile->get('id') == User::get('id')) : ?>
					<?php
					$cls = '';
					$span_title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_TITLE');
					$title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_SET_PRIVATE_TITLE');

					if ($this->profile->get('access') == 2)
					{
						$cls = 'protected';
						$span_title = Lang::txt('COM_MEMBERS_PROTECTED_PROFILE_TITLE');
						$title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
					}

					if ($this->profile->get('access') > 2)
					{
						$cls = 'private';
						$span_title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_TITLE');
						$title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
					}
					?>

					<?php if ($this->active == 'profile') : ?>
						<a id="profile-privacy" href="<?php echo Route::url($this->profile->link() . '?' . Session::getFormToken() . '=1'); ?>"
							data-id="<?php echo $this->profile->get('id'); ?>"
							data-private="<?php echo Lang::txt('Click here to set your profile private.'); ?>"
							data-public="<?php echo Lang::txt('Click here to set your profile public.'); ?>"
							class="<?php echo $cls; ?> tooltips"
							title="<?php echo $title; ?>">
							<?php echo $title; ?>
						</a>
					<?php else: ?>
						<span id="profile-privacy"<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>
							data-id="<?php echo $this->profile->get('id'); ?>"
							data-private="<?php echo Lang::txt('Click here to set your profile private.'); ?>"
							data-public="<?php echo Lang::txt('Click here to set your profile public.'); ?>">
							<?php echo $span_title; ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>

				<h2>
					<a href="<?php echo Route::url($this->profile->link()); ?>">
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

				$results = array();
				$notifications = Notify::messages('com_members.profile');
				foreach ($notifications as $notification)
				{
					$results[] = $notification['message'];
				}
				$results = implode("<br />\n", $results);

				if ($results)
				{
					echo '<p class="info">' . $results . '</p>';
				}
				?>
			</div>
			<div id="page_content" class="member_<?php echo $this->active; ?>">
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
<?php
}
