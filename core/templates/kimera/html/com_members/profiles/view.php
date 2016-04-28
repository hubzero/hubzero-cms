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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);
$user_messaging = $this->config->get('user_messaging', 0);

$prefix = $this->profile->get("name") . "'s";
$edit = false;
$password = false;
$messaging = false;

$tab = $this->active;
$tab_name = 'Dashboard';

//are we allowed to messagin user
switch ($user_messaging)
{
	case 0:
		$mssaging = false;
		break;
	case 1:
		$common = \Hubzero\User\Helper::getCommonGroups(User::get('id'), $this->profile->get('id') );
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
	if ($this->active == "profile")
	{
		$edit = true;
		$password = true;
	}
	$messaging = false;
	$prefix = "My";
}

//no messaging if guest
if (User::isGuest())
{
	$messaging = false;
}

if (!$no_html)
{
	$this->css()
	     ->js();
?>
<header id="content-header" class="content-header">
	<h2>
		<?php echo $this->escape(stripslashes($this->profile->get('name'))); ?>
	</h2>
	<?php if ($this->profile->get('id') == User::get('id')) :
		$cls = '';
		$span_title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_TITLE');
		$title = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_SET_PRIVATE_TITLE');
		if ($this->profile->get('public') != 1)
		{
			$cls = 'private';
			$span_title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_TITLE');
			$title = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
		}

		if ($this->active == 'profile') : ?>
			<a id="profile-privacy" href="<?php echo Route::url($this->profile->link() . '&' . Session::getFormToken() . '=1'); ?>" data-uidnumber="<?php echo $this->profile->get('id'); ?>" class="<?php echo $cls; ?> tooltips" title="<?php echo $title; ?>">
				<?php echo $title; ?>
			</a>
		<?php else: ?>
			<span id="profile-privacy"<?php echo ($cls ? ' class="' . $cls . '"' : ''); ?>>
				<?php echo $span_title; ?>
			</span>
		<?php endif; ?>
	<?php endif; ?>
</header>

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
			<ul id="page_menu">
				<?php foreach ($this->cats as $k => $c) : ?>
					<?php
						$key = key($c);
						if (!$key)
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
						<a class="<?php echo $key; ?>" data-icon="<?php echo '&#x' . $c['icon']; ?>" title="<?php echo $prefix.' '.$name; ?>" href="<?php echo $url; ?>">
							<?php echo $name; ?>
						</a>
						<span class="meta">
							<?php if ($meta_count) : ?>
								<span class="count"><?php echo $meta_count; ?></span>
							<?php endif; ?>
						</span>
						<?php echo $meta_alert; ?>
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
									if ($key == 'text') continue;

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
				$thumb = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/stats/contributor_impact/impact_' . $this->profile->get('id') . '_th.gif';
				$full = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/stats/contributor_impact/impact_' . $this->profile->get('id') . '.gif';
			?>
			<?php if (file_exists(PATH_ROOT . $thumb)) : ?>
				<a id="member-stats-graph" rel="lightbox" title="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" data-name="<?php echo $this->profile->get('name'); ?>" data-type="Impact Graph" href="<?php echo $full; ?>">
					<img src="<?php echo $thumb; ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $this->profile->get('name')); ?>" />
				</a>
			<?php endif; ?>

		</div><!-- /#page_sidebar -->
		<div id="page_main">
		<?php if ($edit || $password) : ?>
			<ul id="page_options">
				<?php if ($edit) : ?>
					<li>
						<a class="edit tooltips" id="edit-profile" title="<?php echo Lang::txt('COM_MEMBERS_EDIT_PROFILE'); ?> :: Edit <?php if ($this->profile->get('id') == User::get('id')) { echo 'my'; } else { echo $this->profile->get('name') . "'s"; } ?> profile." href="<?php echo Route::url($this->profile->link() . '&task=edit'); ?>">
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
			<?php /*<div id="page_header">
				<h3><?php echo $tab_name; ?></h3>
			</div>*/ ?>
			<div id="page_notifications">
				<?php
					if ($this->getError())
					{
						echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
					}
				?>
			</div>
			<div id="page_content" class="member_<?php echo $this->active; ?>">
				<?php
					}
					/*if ($this->overwrite_content)
					{
						echo $this->overwrite_content;
					}
					else
					{*/
						foreach ($this->sections as $s)
						{
							if ($s['html'] != '')
							{
								echo $s['html'];
							}
						}
					//}
					if (!$no_html) {
				?>
			</div><!-- /#page_content -->
		</div><!-- /#page_main -->
	</div> <!-- //#page_container -->
</div><!-- /.innerwrap -->
<?php } ?>
