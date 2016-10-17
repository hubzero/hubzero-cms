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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$group = Hubzero\User\Group::getInstance($this->group->gidNumber);

//get status
$status  = '';
$options = '';

//determine group status
if ($group->get('published') && !User::isGuest())
{
	$members = $group->get('members');

	if (in_array(User::get('id'), $members))
	{
		$status  = 'member';
		$options = '<a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=cancel') .'" title="' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '</a>';

		$managers = $group->get('managers');
		if (in_array(User::get('id'), $managers))
		{
			$status  = 'manager';
			$options = ' <a class="customize tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=edit') .'" title="' . Lang::txt('COM_GROUPS_TOOLBAR_EDIT') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_EDIT') . '</a>';
		}
	}
	else
	{
		$invitees   = $group->get('invitees');
		$applicants = $group->get('applicants');

		if (in_array(User::get('id'), $invitees))
		{
			$status  = 'invitee';
			$options = ' <a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=cancel') .'" title="' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '</a>';
		}
		elseif (in_array(User::get('id'), $applicants))
		{
			$status  = 'pending';
			$options = '<a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=cancel') .'" title="' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '">' . Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') . '</a>';
		}
	}
}

$published = ($group->get('published')) ? true : false;
?>
<div class="group <?php echo (!$published) ? 'notpublished' : '' ?>" id="group<?php echo $group->get('gidNumber'); ?>"
	data-id="<?php echo $group->get('gidNumber'); ?>"
	data-status="<?php echo $this->escape($status); ?>"
	data-title="<?php echo $this->escape(stripslashes($group->get('description')) . ' ' . $group->get('cn')); ?>">
	<div class="group-contents">
		<?php if ($published) : ?>
			<a class="group-identity" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn='. $group->get('cn')); ?>">
		<?php else : ?>
			<div class="group-identity">
		<?php endif; ?>
			<?php
			$path = PATH_APP . '/site/groups/' . $group->get('gidNumber') . '/uploads/' . $group->get('logo');

			if ($group->get('logo') && is_file($path)):
			?>
				<img src="<?php echo with(new Hubzero\Content\Moderator($path))->getUrl(); ?>" alt="<?php echo $this->escape(stripslashes($group->get('description'))); ?>" />
			<?php else : ?>
				<span><?php echo $this->escape(stripslashes($group->get('description'))); ?></span>
			<?php endif; ?>
		<?php if ($published) : ?>
			</a>
		<?php else : ?>
			</div>
		<?php endif; ?>

		<div class="group-details">
			<span class="group-alias"><?php echo $this->escape($group->get('cn')); ?></span>
			<?php if ($published) : ?>
				<a class="group-title" rel="<?php echo $group->get('gidNumber'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn='. $group->get('cn')); ?>">
					<?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($group->get('description')), 60)); ?>
				</a>
			<?php else : ?>
				<span class="group-title">
					<?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($group->get('description')), 60)); ?>
				</span>
			<?php endif; ?>

			<?php if ($published && $status) : ?>
				<span class="<?php echo $status; ?> group-membership-status">
					<?php
					switch ($status)
					{
						case 'manager': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MANAGER'); break;
						case 'member':  echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MEMBER');  break;
						case 'pending': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_PENDING'); break;
						case 'invitee': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_INVITED'); break;
						default: break;
					}
					?>
				</span>
			<?php endif; ?>
		</div>

		<?php if (!$published) : ?>
			<div class="group-meta">
				<span class="not-published group-status"><?php echo Lang::txt('COM_GROUPS_STATUS_NOT_PUBLISHED_GROUP'); ?></span>
			</div>
		<?php else : ?>
			<div class="group-meta">
				<?php if ($status) : ?>
					<?php if ($status == 'pending') : ?>
						<?php echo Lang::txt('Membership request requires approval.'); ?>
					<?php elseif ($status == 'invitee') : ?>
						<a class="btn btn-success accept tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=accept'); ?>" title="<?php echo Lang::txt('COM_GROUPS_ACTION_ACCEPT_TITLE'); ?>"><?php echo Lang::txt('COM_GROUPS_ACTION_ACCEPT'); ?></a>
					<?php else : ?>
						<div class="grid">
							<div class="col span6">
								<span><?php
								$activity = \Hubzero\Activity\Recipient::all()
									->including('log')
									->whereEquals('scope', 'group')
									->whereEquals('scope_id', $group->get('gidNumber'))
									->whereEquals('state', 1)
									->ordered()
									->row();
								$dt = Date::of($activity->get('created'));
								$ct = Date::of('now');

								$lapsed = $ct->toUnix() - $dt->toUnix();

								if ($lapsed < 30)
								{
									echo Lang::txt('COM_GROUPS_ACTIVITY_JUST_NOW');
								}
								elseif ($lapsed > 30 && $lapsed < 60)
								{
									echo Lang::txt('COM_GROUPS_ACTIVITY_A_MINUTE_AGO');
								}
								else
								{
									echo $dt->relative('week');
								}
								?></span>
								<?php echo Lang::txt('COM_GROUPS_ACTIVITY_LAST'); ?>
							</div>
							<div class="col span6 omega">
								<span><?php echo count($members); ?></span>
								<?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<?php if (!$group->get('join_policy') || $group->get('join_policy') == 1) : ?>
						<div class="grid">
							<div class="col span6">
								<?php if (!$group->get('join_policy')) : ?>
									<span class="open join-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?></span>
								<?php elseif ($group->get('join_policy') == 1) : ?>
									<span class="open join-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?></span>
								<?php endif; ?>
								<?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
							</div>
							<div class="col span6 omega">
						<a class="btn btn-success tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn') . '&task=join'); ?>"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_JOIN'); ?></a>
							</div>
						</div>
					<?php elseif ($group->get('join_policy') == 3) : ?>
						<span class="closed join-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?></span>
						<?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
					<?php elseif ($group->get('join_policy') == 2) : ?>
						<span class="inviteonly join-policy"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?></span>
						<?php echo Lang::txt('COM_GROUPS_INFO_JOIN_POLICY'); ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="user-actions">
				<?php echo $options; ?>
			</div>
		<?php endif; ?>
	</div>
</div><!-- / .group -->