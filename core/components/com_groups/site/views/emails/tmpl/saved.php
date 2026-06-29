<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// build urls
$base      = rtrim(str_replace('administrator', '', Request::base()), '/');
$groupLink = $base . '/groups/' . $this->group->get('cn');

// Pre-change values (keyed) for highlighting what changed. Empty for a new
// group, in which case no change markers are shown.
$before = (isset($this->before) && is_array($this->before)) ? $this->before : array();

// Return a red "(was: <old>)" marker when a field's display value changed.
// $oldDisplay is the already-formatted previous value; pass null when there
// is no snapshot for the field (e.g. new group) to suppress the marker.
$wasMarker = function($key, $newDisplay, $oldDisplay) use ($before)
{
	if (!array_key_exists($key, $before))
	{
		return '';
	}
	if ((string) $newDisplay === (string) $oldDisplay)
	{
		return '';
	}
	$old = ($oldDisplay === '' || $oldDisplay === null)
		? '&lt;Empty&gt;'
		: htmlspecialchars((string) $oldDisplay);
	return ' <span style="color: #cc0000; font-weight: bold;">(' . Lang::txt('COM_GROUPS_EMAIL_WAS', $old) . ')</span>';
};

// Shared value->label formatters so the old and new sides render identically
$joinPolicyLabel = function($v)
{
	switch ($v)
	{
		case 3:  return Lang::txt('Closed');
		case 2:  return Lang::txt('Invite Only');
		case 1:  return Lang::txt('Restricted');
		default: return Lang::txt('Open');
	}
};
$discoverabilityLabel = function($v)
{
	return ($v == 1) ? Lang::txt('Hidden') : Lang::txt('Visible');
};
$pageCommentsLabel = function($v)
{
	if ($v == 2)
	{
		return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK');
	}
	if ($v == 1)
	{
		return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES');
	}
	return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO');
};
$pageAuthorLabel = function($v)
{
	return ($v == 1)
		? Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES')
		: Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO');
};
$onOffLabel = function($v)
{
	return $v ? Lang::txt('On') : Lang::txt('Off');
};
?>
	<!-- Start Header -->
	<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
					<?php echo Config::get('sitename'); ?>
				</td>
				<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
					<span class="home">
						<a href="<?php echo Request::base(); ?>"><?php echo Request::base(); ?></a>
					</span>
					<br />
					<span class="description"><?php echo Config::get('MetaDesc'); ?></span>
				</td>
				<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
					<?php echo Lang::txt('COM_GROUPS'); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<table id="ticket-info" width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid #e1e1e1; background: #f1f1f1; font-size: 0.9em; line-height: 1.6em; background-image: -webkit-gradient(linear, 0 0, 100% 100%,
										color-stop(.25, rgba(255, 255, 255, .075)), color-stop(.25, transparent),
										color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .075)),
										color-stop(.75, rgba(255, 255, 255, .075)), color-stop(.75, transparent),
										to(transparent));
	background-image: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -ms-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: -o-linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
	background-image: linear-gradient(-45deg, rgba(255, 255, 255, .075) 25%, transparent 25%,
									transparent 50%, rgba(255, 255, 255, .075) 50%, rgba(255, 255, 255, .075) 75%,
									transparent 75%, transparent);
									-webkit-background-size: 30px 30px;
									-moz-background-size: 30px 30px;
									background-size: 30px 30px;">
		<thead>
			<tr>
				<th colspan="2" style="font-weight: bold; border-bottom: 1px solid #e1e1e1; padding: 8px; text-align: left; font-style: italic;" align="left">
					<?php echo Lang::txt('Group Saved'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id="ticket-number" style="padding: 8px; font-size: 2.5em; font-weight: bold; text-align: center; padding: 8px 30px;" align="center">
					<img src="<?php echo $base . DS . ltrim($this->group->getLogo(), DS); ?>" width="100px" />
				</td>
				<td width="100%" style="padding: 8px;">
					<table style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Group:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php echo $this->group->get('description'); ?>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Alias:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php echo $this->group->get('cn'); ?>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Updated:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">@ <?php echo Date::of('now')->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?> on <?php echo Date::of('now')->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Updated By:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<?php
										echo $this->user->get('name') . ' ('.$this->user->get('email').')';
									?>
								</td>
							</tr>
							<tr>
								<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right"><?php echo Lang::txt('Link:'); ?></th>
								<td style="text-align: left; padding: 0 0.5em;" align="left">
									<a href="<?php echo $groupLink; ?>">
										<?php echo $groupLink; ?>
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table width="650" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
		<tbody>
			<tr>
				<td width="100%" style="padding: 8px;">
					<table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Name:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
										$new = $this->group->get('description');
										echo $new;
										echo $wasMarker('description', $new, isset($before['description']) ? $before['description'] : null);
									?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Interests (Tags):'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
										$gt = new \Components\Groups\Models\Tags($this->group->get('gidNumber'));
										$tags = $gt->render('string');
									?>
									<?php if ($tags) : ?>
										<?php echo $tags; ?>
									<?php else : ?>
										&#60;Empty&#62;
									<?php endif; ?>
									<?php echo $wasMarker('tags', $tags, isset($before['tags']) ? $before['tags'] : null); ?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Public Description:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left;padding: 1em; line-height:18px;" align="left">
									<?php if ($this->group->get('public_desc')) : ?>
										<?php echo $this->group->get('public_desc'); ?>
									<?php else : ?>
										&#60;Empty&#62;
									<?php endif; ?>
									<?php echo $wasMarker('public_desc', $this->group->get('public_desc'), isset($before['public_desc']) ? $before['public_desc'] : null); ?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Private Description:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php if ($this->group->get('private_desc')) : ?>
										<?php echo $this->group->get('private_desc'); ?>
									<?php else : ?>
										&#60;Empty&#62;
									<?php endif; ?>
									<?php echo $wasMarker('private_desc', $this->group->get('private_desc'), isset($before['private_desc']) ? $before['private_desc'] : null); ?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Logo:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php if ($this->group->get('logo')) : ?>
										<img src="<?php echo $base . DS . ltrim($this->group->getLogo(), DS); ?>" width="50px" />
									<?php else : ?>
										&#60;Not Set&#62;
									<?php endif; ?>
									<?php echo $wasMarker('logo', $this->group->get('logo'), isset($before['logo']) ? $before['logo'] : null); ?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Membership Settings/Join Policy:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
									// Determine the join policy
									$policy = $joinPolicyLabel($this->group->get('join_policy'));
									echo $policy;
									echo $wasMarker('join_policy', $policy, isset($before['join_policy']) ? $joinPolicyLabel($before['join_policy']) : null);

									if ($this->group->get('join_policy') == 1)
									{
										echo '<br /><em>' . $this->group->get('restrict_msg') . '</em>';
										echo $wasMarker('restrict_msg', $this->group->get('restrict_msg'), isset($before['restrict_msg']) ? $before['restrict_msg'] : null);
									}
									?>
								</td>
							</tr>


							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Discoverability:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
									// Determine the discoverability
									$discoverability = $discoverabilityLabel($this->group->get('discoverability'));
									echo $discoverability;
									echo $wasMarker('discoverability', $discoverability, isset($before['discoverability']) ? $discoverabilityLabel($before['discoverability']) : null);
									?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Access Permissions:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding:1em; line-height:18px;" align="left">
									<?php
									//access levels
									$levels = array(
										//'anyone' => 'Enabled/On',
										'anyone' => 'Any HUB Visitor',
										'registered' => 'Only Registered User of the HUB',
										'members' => 'Only Group Members',
										'nobody' => 'Disabled/Off'
									);

									// Get plugins
									$group_plugins = Event::trigger('groups.onGroupAreas', array());
									array_unshift($group_plugins, array(
										'name'             => 'overview',
										'title'            => 'Overview',
										'default_access'   => 'anyone',
										'display_menu_tab' => true
									));

									$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group);
									$oldAccess = (isset($before['access']) && is_array($before['access'])) ? $before['access'] : null;

									foreach ($group_plugins as $plugin)
									{
										if ($plugin['display_menu_tab'] == 1)
										{
											$title  = $plugin['title'];
											$perm = $access[$plugin['name']];
											echo $title . ' => ' . $levels[$perm];
											if ($oldAccess !== null && isset($oldAccess[$plugin['name']]) && $oldAccess[$plugin['name']] != $perm)
											{
												$oldPerm  = $oldAccess[$plugin['name']];
												$oldLabel = isset($levels[$oldPerm]) ? $levels[$oldPerm] : $oldPerm;
												echo ' <span style="color: #cc0000; font-weight: bold;">(' . Lang::txt('COM_GROUPS_EMAIL_WAS', htmlspecialchars($oldLabel)) . ')</span>';
											}
											echo '<br />';
										}
									}
									?>
								</td>
							</tr>
							<?php $params = Component::params('com_groups'); ?>

							<?php if ($params->get('email_forum_comments')) :?>
								<tr>
									<td align="left">&#32;</td>
								</tr>
								<tr>
									<td style="text-align: left;font-weight: bold;" align="left">
										<?php echo Lang::txt('Discussion Group Emails Autosubscribe:'); ?>
									</td>
								</tr>
								<tr>
									<td style="text-align:left; padding:1em; line-height:18px;" align="left">
										<?php
										$autosub = $onOffLabel($this->group->get('discussion_email_autosubscribe'));
										echo $autosub;
										echo $wasMarker('discussion_email_autosubscribe', $autosub, isset($before['discussion_email_autosubscribe']) ? $onOffLabel($before['discussion_email_autosubscribe']) : null);
										?>
									</td>
								</tr>
							<?php endif; ?>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Page Comments:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
										$gparams = new \Hubzero\Config\Registry($this->group->get('params'));
										$pageComments = $pageCommentsLabel($gparams->get('page_comments'));
										echo $pageComments;
										echo $wasMarker('page_comments', $pageComments, isset($before['page_comments']) ? $pageCommentsLabel($before['page_comments']) : null);
									?>
								</td>
							</tr>

							<tr>
								<td align="left">&#32;</td>
							</tr>
							<tr>
								<td style="text-align: left;font-weight: bold;" align="left">
									<?php echo Lang::txt('Page Author Details:'); ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; padding: 1em; line-height:18px;" align="left">
									<?php
										$gparams = new \Hubzero\Config\Registry($this->group->get('params'));
										$pageAuthor = $pageAuthorLabel($gparams->get('page_author'));
										echo $pageAuthor;
										echo $wasMarker('page_author', $pageAuthor, isset($before['page_author']) ? $pageAuthorLabel($before['page_author']) : null);
									?>
								</td>
							</tr>

						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- Start Spacer -->
	<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td height="30"></td>
			</tr>
		</tbody>
	</table>
	<!-- End Spacer -->

	<!-- Start Footer -->
	<table class="tbl-footer" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="left" valign="bottom">
					<span><?php echo Config::get('sitename'); ?> sent this email because you are a group manager for this group. Visit our <a href="<?php echo rtrim($base, '/'); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($base, '/'); ?>/support">Support Center</a> if you have any questions.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- End Header -->