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

// No direct access
defined('_HZEXEC_') or die();

$filters = array(
	'members'  => Lang::txt('PLG_GROUPS_MEMBERS'),
	'managers' => Lang::txt('PLG_GROUPS_MEMBERS_MANAGERS'),
	'pending'  => Lang::txt('PLG_GROUPS_MEMBERS_PENDING'),
	'invitees' => Lang::txt('PLG_GROUPS_MEMBERS_INVITEES')
);

if ($this->filter == '')
{
	$this->filter = 'members';
}

$role_id   = '';
$role_name = '';

if ($this->role_filter)
{
	foreach ($this->member_roles as $role)
	{
		if ($role['id'] == $this->role_filter)
		{
			$role_id   = $role['id'];
			$role_name = $role['name'];
			break;
		}
	}
}
$option = 'com_groups';
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_GROUPS_MEMBERS'); ?>
</h3>

<?php if ($this->membership_control == 1) { ?>
	<?php //if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
		<ul id="page_options">
			<li>
				<?php if ($this->authorized == 'manager'
					|| $this->authorized == 'admin'
					|| Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction($this->group, 'group.invite')) : ?>
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $option . '&cn=' . $this->group->get('cn') . '&task=invite'); ?>">
					<?php echo Lang::txt('PLG_GROUPS_MEMBERS_INVITE_MEMBERS'); ?>
				</a>
				<?php endif; ?>
				<?php if ($this->membership_control == 1 && $this->authorized == 'manager') : ?>
					<a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=addrole'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ADD_ROLE'); ?>
					</a>
				<?php endif; ?>
			</li>
		</ul>
	<?php //} ?>
<?php } ?>

<section class="section">
	<div class="subject">
		<form action="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&filter='.$this->filter); ?>" method="post">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_SEARCH_LABEL'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->q); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<nav class="entries-filters">
					<?php if (($this->authorized == 'manager' || $this->authorized == 'admin') && count($this->groupusers) > 0) { ?>
						<ul class="entries-menu message-options">
							<li>
								<span class="message-all message-member">
									<?php if ($this->messages_acl != 'nobody') { ?>
									<?php
										if ($role_id) {
											$append = '&users[]=role&role_id='.$role_id;
											$title = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_ROLE', $role_name);
										} else {
											switch ($this->filter)
											{
												case 'pending':
													$append = '&users[]=applicants';
													$title = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_APPLICANTS');
													break;
												case 'invitees':
													$append = '&users[]=invitees';
													$title = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_INVITEES');
													break;
												case 'managers':
													$append = '&users[]=managers';
													$title = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_MANAGERS');
													break;
												case 'members':
												default:
													$append = '&users[]=all';
													$title = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_MEMBERS');
													break;
											}
										}
									?>
									<a class="tooltips" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=messages&action=new'.$append); ?>" title="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE'); ?> :: <?php echo $title; ?>">
										<?php echo Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL'); ?>
									</a>
									<?php } ?>
								</span><!-- / .message-all -->
							</li>
						</ul>
					<?php } ?>

					<ul class="entries-menu filter-options">
						<?php foreach ($filters as $filter => $name) { ?>
							<?php $active = ($this->filter == $filter) ? ' active': ''; ?>
							<?php
								if (($filter == 'pending' || $filter == 'invitees') && $this->membership_control == 0) {
									continue;
								}
							?>
							<?php if ($filter != 'pending' && $filter != 'invitees' || ($this->authorized == 'admin' || $this->authorized == 'manager')) { ?>
									<li>
										<a class="<?php echo $filter . $active; ?>" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&filter='.$filter); ?>"><?php echo $name; ?>
											<?php
												if ($filter == 'pending') {
													echo '('.count($this->group->get('applicants')).')';
												} elseif ($filter == 'invitees') {
													//get invite emails
													echo '('.(count($this->group->get('invitees')) + count($this->current_inviteemails)).')';
												} else {
													echo '('.count($this->group->get($filter)).')';
												}
											?>
										</a>
									</li>

							<?php } ?>
						<?php } ?>
					</ul>
				</nav>

				<table class="groups entries">
					<tbody>
						<?php
						if ($this->groupusers)
						{
							// get all sessions
							$sessions = Hubzero\Session\Helper::getAllSessions(array(
								'guest'    => 0,
								'distinct' => 1
							));

							// Loop through the results
							$html = '';
							if ($this->limit == 0)
							{
								$this->limit = 500;
							}

							$db = App::get('db');

							for ($i=0, $n=$this->limit; $i < $n; $i++)
							{
								$cls = '';
								$inviteemail = false;

								if (($i+$this->start) >= count($this->groupusers))
								{
									break;
								}
								$guser = $this->groupusers[($i+$this->start)];

								$u = User::getInstance($guser);

								if (\Components\Members\Helpers\Utility::validemail($guser))
								{
									$inviteemail = true;
									$pic = rtrim(Request::base(true), '/') . '/core/components/com_groups/site/assets/img/emailthumb.png';
								}
								else if (!is_object($u))
								{
									continue;
								}
								else
								{
									$pic = $u->picture(0);
								}

								// Timestamp for when the user was invited
								$invited = null;

								switch ($this->filter)
								{
									case 'invitees':
										$status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_INVITEE');

										// @TODO: Find a better way to do this!
										//        Ideally, this should be a timestamp on the invitations table
										if ($inviteemail)
										{
											$query = "SELECT `timestamp`
												FROM `#__xgroups_log`
												WHERE `action`=" . $db->quote('membership_invites_sent') . "
												AND `comments` LIKE " . $db->quote('%"' . $u->get($guser) . '"%') . "
												AND `gidNumber`=" . $db->quote($this->group->get('gidNumber')) . "
												ORDER BY `timestamp` DESC
												LIMIT 1";
										}
										else
										{
											$query = "SELECT `timestamp`
												FROM `#__xgroups_log`
												WHERE `action`=" . $db->quote('membership_invites_sent') . "
												AND `comments` LIKE " . $db->quote('%"' . $u->get('id') . '"%') . "
												AND `gidNumber`=" . $db->quote($this->group->get('gidNumber')) . "
												ORDER BY `timestamp` DESC
												LIMIT 1";
										}
										$db->setQuery($query);
										$invited = $db->loadResult();
									break;
									case 'pending':
										$status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_PENDING');
									break;
									case 'managers':
										$status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
										$cls .= ' manager';
									break;
									case 'members':
									default:
										$status = 'Member';
										if (in_array($guser,$this->managers))
										{
											$status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
											$cls .= 'manager';
										}
									break;
								}

								if (is_object($u) && User::get('id') == $u->get('id'))
								{
									$cls .= ' me';
								}

								// Member online?
								$online = 0;
								if ($sessions)
								{
									// see if any session matches our userid
									foreach ($sessions as $session)
									{
										if ($session->userid == $u->get('id'))
										{
											$online = 1;
										}
									}
								}

								$url = $this->group->isSuperGroup() ? 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=members&scope=' . $u->get('id') : $u->link();
						?>
						<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
							<td class="photo">
								<span class="photo-wrap<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_GROUPS_MEMBERS_ONLINE'); } ?>">
									<img width="50" height="50" src="<?php echo $pic; ?>" alt="" />
									<?php if ($online) { ?>
										<span class="online"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ONLINE'); ?></span>
									<?php } ?>
								</span>
							</td>
							<td>
								<?php if ($inviteemail) { ?>
									<span class="name">
										<a href="mailto:<?php echo $guser; ?>">
											<?php echo $guser; ?>
										</a>
									</span>
									<span class="status"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_INVITE_SENT_TO_EMAIL'); ?></span><br />
									<?php if ($invited) { ?>
										<span class="invited"><time datetime="<?php echo $invited; ?>"><?php echo Lang::txt('Invoted on %s', Date::of($invited)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))); ?></time></span><br />
									<?php } ?>
								<?php } else { ?>
									<span class="name">
											<?php
												//handles the comma
												$displayName = '';
												$surname = $u->get('surname');
												$givenName = $u->get('givenName');
												$displayName = !empty($surname) ? $surname : '';
												$displayName .= !empty($givenName) ? !empty($displayName) ? ', ' . $givenName : $givenName :  '';
											?>
										<?php if (in_array($u->get('access'), User::getAuthorisedviewLevels()) && ($u->get('activation') > 0)) { ?>
											<a href="<?php echo Route::url($url); ?>"><?php echo $displayName;?></a>
										<?php } else { ?>
											<?php echo $displayName; ?>
										<?php } ?>
									</span>
									<span class="status"><?php echo $status; ?></span><br />
									<?php if ($invited) { ?>
										<span class="invited"><?php echo Lang::txt('Invited on %s', '<time datetime="' . $invited . '">' . Date::of($invited)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '</time>'); ?></span><br />
									<?php } ?>
									<?php if ($u->get('organization')) { ?>
										<span class="organization"><?php echo $this->escape(stripslashes($u->get('organization'))); ?></span><br />
									<?php } ?>
								<?php } ?>
								<?php
								$html = '';
								if ($this->filter == 'members' || $this->filter == 'managers') {
									$html .= '<span class="roles">';
									$all_roles = '';

									$db = \App::get('db');
									$db->setQuery(
										"SELECT r.id, r.name, r.permissions
										FROM `#__xgroups_roles` as r
										LEFT JOIN `#__xgroups_member_roles` as m ON m.roleid=r.id
										WHERE m.uidNumber=" . $db->quote($u->get('id')) . " AND r.gidNumber=" . $db->quote($this->group->gidNumber)
									);
									$roles = $db->loadAssocList();

									if ($roles) {
										$html .= '<strong>' . Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES') . ':</strong> ';
										foreach ($roles as $role) {
											$all_roles .= ', <span><a href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&filter='.$this->filter.'&role_filter='.$role['id']).'">'.$role['name'].'</a>';

											if ($this->authorized == 'manager') {
												if ($this->membership_control == 1) {
													$all_roles .= '<span class="delete-role"><a href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=deleterole&uid='.$u->get('id').'&role='.$role['id']).'">x</a></span></span>';
												}
											} else {
												$all_roles .= '</span>';
											}
										}

										$html .= '<span class="roles-list" id="roles-list-'.$u->get('id').'">'.substr($all_roles,2).'</span>';

										if ($this->authorized == 'manager') {
											if ($this->membership_control == 1) {
												$html .= ', <a class="assign-role" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=assignrole&uid='.$u->get('id')).'">' . Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') . '</a>';
											}
										}

									}

									if ($this->membership_control == 1) {
										if (($this->authorized == 'manager' || $this->authorized == 'admin') && !$roles) {
											$html .= '<strong>' . Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES') . ':</strong> ';
											$html .= '<span class="roles-list" id="roles-list-'.$u->get('id').'"></span>';
											$html .= ' <a class="assign-role" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=assignrole&uid='.$u->get('id')).'">' . Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') . '</a>';
										}
									}
									$html .= '</span>';
								}

								if ($this->filter == 'pending') {
									$database = App::get('db');
									$row = new Components\Groups\Tables\Reason($database);
									$row->loadReason($u->get('id'), $this->group->gidNumber);

									if ($row)
									{
										$html .= '<span class="reason" data-title="' . Lang::txt('PLG_GROUPS_MEMBERS_REASON_FOR_REQUEST') . '">';
										$html .= '<span class="reason-reason">'.stripslashes($row->reason).'</span>';
										$html .= '<span class="reason-date">'.Date::of($row->date)->toLocal('F d, Y @ g:ia').'</span>';
										$html .= '</span>';
									}
								}

								$html .= '</td>'."\n";
								if ($this->authorized == 'manager' || $this->authorized == 'admin') {
									switch ($this->filter)
									{
										case 'invitees':
											if ($this->membership_control == 1) {
												if (!$inviteemail) {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="cancel tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=cancel&users[]='.$guser.'&filter='.$this->filter).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_CANCEL_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_CANCEL').'</a></td>'."\n";
												} else {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="cancel tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=cancel&users[]='.urlencode(urlencode($guser)).'&filter='.$this->filter).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_CANCEL_MEMBER',$this->escape($guser)).'">'.Lang::txt('PLG_GROUPS_MEMBERS_CANCEL').'</a></td>'."\n";
												}
											}
											$html .= "\t\t\t\t".'<td class="approve-member"> </td>'."\n";
										break;
										case 'pending':
											if ($this->membership_control == 1) {
												$html .= "\t\t\t\t".'<td class="decline-member"><a class="decline tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=deny&users[]='.$guser.'&filter='.$this->filter).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_DECLINE_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_DENY').'</a></td>'."\n";
												$html .= "\t\t\t\t".'<td class="approve-member"><a class="approve tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=approve&users[]='.$guser.'&filter='.$this->filter).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_APPROVE_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_APPROVE').'</a></td>'."\n";
											}
										break;
										case 'managers':
										case 'members':
										default:
											if ($this->membership_control == 1) {
												if (!in_array($guser,$this->managers) || (in_array($guser,$this->managers) && count($this->managers) > 1)) {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="remove tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=remove&users[]='.$guser.'&filter='.$this->filter).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_REMOVE_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_REMOVE').'</a></td>'."\n";
												} else {
													$html .= "\t\t\t\t".'<td class="remove-member"> </td>'."\n";
												}

												if (in_array($guser,$this->managers)) {
													//force admins to use backend to demote manager if only 1
													//if ($this->authorized == 'admin' || count($this->managers) > 1) {
													if (count($this->managers) > 1) {
														$html .= "\t\t\t\t".'<td class="demote-member"><a class="demote tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=demote&users[]='.$guser.'&filter='.$this->filter.'&limit='.$this->limit.'&limitstart='.$this->start).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_DEMOTE_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_DEMOTE').'</a></td>'."\n";
													} else {
														$html .= "\t\t\t\t".'<td class="demote-member"> </td>'."\n";
													}
												} else {
													$html .= "\t\t\t\t".'<td class="promote-member"><a class="promote tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=promote&users[]='.$guser.'&filter='.$this->filter.'&limit='.$this->limit.'&limitstart='.$this->start).'" title="'.Lang::txt('PLG_GROUPS_MEMBERS_PROMOTE_MEMBER',$this->escape($u->get('name'))).'">'.Lang::txt('PLG_GROUPS_MEMBERS_PROMOTE').'</a></td>'."\n";
												}
											}
										break;
									}
								} else {
									$html .= "\t\t\t\t".'<td class="remove-member"> </td>'."\n";
									$html .= "\t\t\t\t".'<td class="demote-member"> </td>'."\n";
								}
								$html .= "\t\t\t\t" . '<td class="message-member">';
								if (is_object($u) && User::get('id') == $u->get('uidNumber') || $this->filter == 'invitees' || $this->filter == 'pending') {
								} else {
									$membersParams = Component::params('com_members');
									$userMessaging = $membersParams->get('user_messaging', 1);
									if (!$inviteemail && $this->messages_acl != 'nobody')
									{
										if ($u->get('activation') > 0)
										{
											if (in_array(User::get('id'), $this->group->get('managers')) && ($u->get('activation') > 0))
											{
												$html .= '<a class="tooltips" href="'.Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=messages&action=new&users[]='.$guser).'" title="Message :: Send a message to '.$this->escape($u->get('name')).'">'.Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') . '</a>';
											}
											else if (($userMessaging == 2 || ($userMessaging == 1 && in_array(User::get('id'), $this->group->get('members')))) && ($u->get('activation') > 0))
											{
												$html .= '<a class="tooltips" href="'.Route::url('index.php?option=com_members&id='.User::get('id').'&active=messages&task=new&to[]='.$guser).'" title="Message :: Send a message to '.$this->escape($u->get('name')).'">'.Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') . '</a>';
											}
										}
										else
										{
											$html .= '<span class="unconfirmed">' . Lang::txt('PLG_GROUPS_MEMBERS_EMAIL_NOT_ACTIVATED') . '</span>';
										}
									}
									$html .= '</td>' . "\n";
								}
								echo $html;
							?>
						</tr>
						<?php
							}
						} else {
						?>
						<tr>
							<td><?php echo Lang::txt('PLG_GROUPS_MEMBERS_NO_RESULTS'); ?></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			<?php
				// Initiate paging
				$pageNav = $this->pagination(
					count($this->groupusers),
					$this->start,
					$this->limit
				);
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'members');
				$pageNav->setAdditionalUrlParam('filter', $this->filter);
				$pageNav->setAdditionalUrlParam('q', $this->q);

				echo $pageNav->render();
			?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
			<div class="clear"></div>

			<input type="hidden" name="cn" value="<?php echo $this->group->cn; ?>" />
			<input type="hidden" name="active" value="members" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="filter" value="<?php echo $this->filter; ?>" />
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<h4><?php echo Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES'); ?></h4>
			<?php if (count($this->member_roles) > 0) { ?>
				<ul class="roles">
					<?php foreach ($this->member_roles as $role) { ?>
						<?php $cls = ($role['id'] == $this->role_filter) ? 'active' : ''; ?>
						<li>
							<?php if ($this->authorized == 'manager' && $this->membership_control == 1) : ?>
								<a class="remove-role" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=removerole&role='.$role['id']); ?>" title="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_REMOVE'); ?>">
									<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_REMOVE'); ?>
								</a>
								<a class="edit-role" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&action=editrole&role='.$role['id']); ?>" title="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_EDIT'); ?>">
									<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_EDIT'); ?>
								</a>
							<?php endif; ?>
							<a class="role <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$option.'&cn='.$this->group->cn.'&active=members&role_filter='.$role['id']); ?>">
								<?php echo $this->escape($role['name']); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p class="starter"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_NO_ROLES_FOUND'); ?></p>
			<?php }?>
		</div><!-- / .container -->
	</aside>
</section><!--/ #group_members -->
