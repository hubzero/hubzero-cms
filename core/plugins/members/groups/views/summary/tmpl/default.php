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

$base = $this->member->link() . '&active=groups';

$this->css()
	->js();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_GROUPS'); ?>
</h3>

<?php if (User::authorise('core.create', 'com_groups')) { ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" href="<?php echo Route::url('index.php?option=com_groups&task=new'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_GROUPS_CREATE'); ?>
			</a>
		</li>
	</ul>
<?php } ?>

<?php if ($this->total) { ?>
	<div class="container">
		<nav class="entries-filters">
			<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('PLG_MEMBERS_GROUPS_BROWSE_FILTER_STATE'); ?>">
				<li>
					<a class="sort-title<?php echo ($this->state == 'active') ? ' active' : ''; ?>" href="<?php echo Route::url($base . '&filter=' . $this->filter); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATE_ACTIVE'); ?>
					</a>
				</li>
				<li>
					<a class="sort-alias<?php echo ($this->state == 'archived') ? ' active' : ''; ?>" href="<?php echo Route::url($base . '&filter=' . $this->filter . '&state=archived'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATE_ARCHIVED'); ?>
					</a>
				</li>
			</ul>

			<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('PLG_MEMBERS_GROUPS_BROWSE_FILTER_MEMBERSHIP'); ?>">
				<li>
					<a<?php echo ($this->filter == '') ? ' class="active"' : ''; ?> data-status="all" href="<?php echo Route::url($base); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_ALL', $this->total); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filter == 'managers') ? ' class="active"' : ''; ?> data-status="manager" href="<?php echo Route::url($base . '&state=' . $this->state . '&filter=managers'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MANAGER'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filter == 'members') ? ' class="active"' : ''; ?> data-status="member" href="<?php echo Route::url($base . '&state=' . $this->state . '&filter=members'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MEMBER'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filter == 'applicants') ? ' class="active"' : ''; ?> data-status="applicant" href="<?php echo Route::url($base . '&state=' . $this->state . '&filter=applicants'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_APPLICANT'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filter == 'invitees') ? ' class="active"' : ''; ?> data-status="invitee" href="<?php echo Route::url($base . '&state=' . $this->state . '&filter=invitees'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_INVITEES'); ?>
					</a>
				</li>
			</ul>
		</nav>

		<div class="groups-container">
			<?php
			$db = App::get('db');

			foreach ($this->groups as $group)
			{
				$status    = '';
				$options   = '';
				$published = false;
				$approved  = false;

				if ($group->manager)
				{
					$status = 'manager';

					//$options  = '<a class="manage tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&active=members') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_MANAGE_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_MANAGE').'</a>';
					$options .= ' <a class="customize tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=edit') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_EDIT_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_EDIT').'</a>';
					//$options .= ' <a class="delete tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=delete') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DELETE_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DELETE').'</a>';
				}
				else if ($group->registered && $group->regconfirmed)
				{
					$status = 'member';

					$options = '<a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
				}
				else if ($group->registered && !$group->regconfirmed)
				{
					$status = 'pending';

					$options = '<a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
				}
				else if (!$group->registered && $group->regconfirmed)
				{
					$status = 'invitee';

					//$options  = '<a class="accept tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=accept') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT').'</a>';
					$options .= ' <a class="cancel tooltips" href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DECLINE_TITLE') . '">'.Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DECLINE').'</a>';
				}

				// do we have a new unpublished group
				$approved  = ($group->approved) ? true : false;

				// are we published
				$published = ($group->published) ? true : false;
				?>
				<div class="group <?php echo (!$published) ? 'notpublished' : ($group->published == 2 ? 'archived' : 'published'); ?>" id="group<?php echo $group->gidNumber; ?>"
					data-id="<?php echo $group->gidNumber; ?>"
					data-status="<?php echo $this->escape($status); ?>"
					data-title="<?php echo $this->escape(stripslashes($group->description) . ' ' . $group->cn); ?>">
					<div class="group-contents">
						<?php if ($published) : ?>
							<a class="group-identity" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn='. $group->cn); ?>">
						<?php else : ?>
							<div class="group-identity">
						<?php endif; ?>
							<?php
							$path = PATH_APP . '/site/groups/' . $group->gidNumber . '/uploads/' . $group->logo;

							if ($group->logo && is_file($path)):
							?>
								<img src="<?php echo with(new Hubzero\Content\Moderator($path))->getUrl(); ?>" alt="<?php echo $this->escape(stripslashes($group->description)); ?>" />
							<?php else : ?>
								<span><?php echo $this->escape(stripslashes($group->description)); ?></span>
							<?php endif; ?>
						<?php if ($published) : ?>
							</a>
						<?php else : ?>
							</div>
						<?php endif; ?>

						<div class="group-details">
							<span class="group-alias"><?php echo $this->escape($group->cn); ?></span>
							<?php if ($published) : ?>
								<a class="group-title" rel="<?php echo $group->gidNumber; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn='. $group->cn); ?>">
									<?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($group->description), 60)); ?>
								</a>
							<?php else : ?>
								<span class="group-title">
									<?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($group->description), 60)); ?>
								</span>
							<?php endif; ?>

							<?php if ($published) : ?>
								<span class="<?php echo $status; ?> group-membership-status">
									<?php
									switch ($status)
									{
										case 'manager': echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MANAGER'); break;
										case 'member':  echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MEMBER');  break;
										case 'pending': echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_PENDING'); break;
										case 'invitee': echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_INVITED'); break;
										default: break;
									}
									?>
								</span>
							<?php endif; ?>
						</div>

						<?php if (!$published) : ?>
							<div class="group-meta">
								<span class="not-published group-status"><?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_NOT_PUBLISHED_GROUP'); ?></span>
							</div>
						<?php elseif (!$approved) : ?>
							<div class="group-meta">
								<span class="pending-approval group-status"><?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATUS_NEW_GROUP'); ?></span>
							</div>
						<?php else : ?>
							<div class="group-meta">
								<?php if ($group->registered && !$group->regconfirmed) : ?>
									<?php echo Lang::txt('Membership request requires approval.'); ?>
								<?php elseif (!$group->registered && $group->regconfirmed) : ?>
									<a class="btn btn-success accept tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=accept'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT_TITLE'); ?>"><?php echo Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT'); ?></a>
								<?php else : ?>
									<div class="grid">
										<div class="col span6">
											<?php if ($group->published == 2) : ?>
												<span><?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATE_ARCHIVED_HINT'); ?></span>
												<?php echo Lang::txt('PLG_MEMBERS_GROUPS_STATE_ARCHIVED'); ?>
											<?php else : ?>
												<span><?php
												$activity = \Hubzero\Activity\Recipient::all()
													->including('log')
													->whereEquals('scope', 'group')
													->whereEquals('scope_id', $group->gidNumber)
													->whereEquals('state', 1)
													->ordered()
													->row();
												$dt = Date::of($activity->get('created'));
												$ct = Date::of('now');

												$lapsed = $ct->toUnix() - $dt->toUnix();

												if ($lapsed < 30)
												{
													echo Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_JUST_NOW');
												}
												elseif ($lapsed > 30 && $lapsed < 60)
												{
													echo Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_A_MINUTE_AGO');
												}
												else
												{
													echo $dt->relative('week');
												}
												?></span>
												<?php echo Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_LAST'); ?>
											<?php endif; ?>
										</div>
										<div class="col span6 omega">
											<span><?php
											// @TODO: Move this to a model
											$db->setQuery("SELECT COUNT(*) FROM `#__xgroups_members` WHERE `gidNumber`=" . $group->gidNumber);
											echo $db->loadResult();
											?></span>
											<?php echo Lang::txt('PLG_MEMBERS_GROUPS_MEMBERS'); ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<?php if ($group->published != 2) : ?>
								<div class="user-actions">
									<?php echo $options; ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div><!-- / .group -->
				<?php
			}
			?>
			<div class="results-none" <?php if (count($this->groups)) { echo 'style="display:none;"'; } ?>>
				<p><?php echo Lang::txt('PLG_MEMBERS_GROUPS_NONE_FOUND'); ?></p>
			</div>
		</div><!-- / .groups -->
	</div><!-- / .container -->
<?php } else { ?>
	<div class="introduction">
		<div class="instructions">
			<p><?php echo Lang::txt('PLG_MEMBERS_GROUPS_YOURS_EXPLANATION'); ?></p>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_GROUPS_WHAT_ARE_GROUPS'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_GROUPS_EXPLANATION'); ?></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_GROUPS_GO_TO_GROUPS', Route::url('index.php?option=com_groups')); ?></p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
