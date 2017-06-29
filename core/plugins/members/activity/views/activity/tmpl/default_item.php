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

defined('_HZEXEC_') or die();

$status = '';
if (!$this->row->wasViewed())
{
	$status = 'new';

	$this->row->markAsViewed();
}
if ($this->row->get('starred'))
{
	$status .= ' starred';
}
$creator = User::getInstance($this->row->log->get('created_by'));

$name = Lang::txt('PLG_MEMBERS_ACTIVITY_ANONYMOUS');

$online = false;

// If the user was the current logged-in user...
if ($this->row->log->get('created_by') == User::get('id'))
{
	// Same user so go ahead and link to profile
	/*$name = '<a href="' . Route::url($creator->link()) . '">' . $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN')))) . '</a>';

	// If they posted as anonymous, indicate it
	if ($this->row->log->get('anonymous'))
	{
		$name = Lang::txt('PLG_MEMBERS_ACTIVITY_AS_ANONYMOUS', $name);
	}*/

	$name = 'You';

	$online = true;
}
// Someone else
// Is it not anonymous?
else if (!$this->row->log->get('anonymous'))
{
	// Get their full name
	$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN'))));

	// Can we see their profile?
	if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
	{
		$name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
	}

	if (isset($this->online) && !$this->row->log->get('anonymous') && in_array($this->row->log->get('created_by'), $this->online))
	{
		$online = true;
	}
}

$base = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=activity';
?>
<li
	data-time="<?php echo $this->row->get('created'); ?>"
	data-id="<?php echo $this->row->get('id'); ?>"
	data-log_id="<?php echo $this->row->get('log_id'); ?>"
	data-context="<?php echo $this->row->log->get('scope'); ?>"
	data-action="<?php echo $this->row->log->get('action'); ?>"
	id="activity<?php echo $this->row->get('id'); ?>"
	class="activity <?php echo $status . ($this->row->get('starred') ? ' starred' : ''); ?>">

	<div class="activity-actor-picture<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_MEMBERS_ACTIVITY_ONLINE'); } ?>">
		<?php if ($this->row->log->get('created_by') == User::get('id')) { ?>
			<span class="user-img-wrap">
				<span class="activity-context <?php
					$scope = explode('.', $this->row->log->get('scope'));
					$context = $this->escape($scope[0]);
					if (in_array('comment', $scope))
					{
						$context = 'comment';

						if ($this->row->log->get('parent'))
						{
							$context = 'reply';
						}
					}
					echo $context . ' ' . $this->row->log->get('action');
				?>"><?php echo $context . ' ' . $this->row->log->get('action'); ?></span>
			</span>
		<?php } else if ($creator->get('public')) { ?>
			<a class="user-img-wrap" href="<?php echo Route::url($creator->link()); ?>" title="<?php echo $name; ?>">
				<img src="<?php echo $creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ONLINE'); ?></span>
				<?php } ?>
			</a>
		<?php } else { ?>
			<span class="user-img-wrap">
				<img src="<?php echo $creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ONLINE'); ?></span>
				<?php } ?>
			</span>
		<?php } ?>
	</div><!-- / .activity-actor-picture -->

	<div class="activity-content <?php echo $this->escape($this->row->log->get('action')); ?> <?php echo $this->escape(str_replace('.', '-', $this->row->log->get('scope'))); ?>">
		<div class="activity-body">
			<div class="activity-details">
				<span class="activity-actor"><?php echo $name; ?></span>
				<span class="activity-action"><?php echo $this->escape($this->row->log->get('action')); ?></span>
				<span class="activity-channel"><?php echo $this->escape($this->row->get('scope') . '.' . $this->row->get('scope_id')); ?></span>
				<span class="activity-context"><?php
					$scope = explode('.', $this->row->log->get('scope'));
					echo $this->escape($scope[0]);
				?></span>
				<span class="activity-time"><time datetime="<?php echo Date::of($this->row->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php
					$dt = Date::of($this->row->get('created'));
					$ct = Date::of('now');

					$lapsed = $ct->toUnix() - $dt->toUnix();

					if ($lapsed < 30)
					{
						echo Lang::txt('PLG_MEMBERS_ACTIVITY_JUST_NOW');
					}
					elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
					{
						echo $dt->toLocal('M j, Y');
					}
					elseif ($lapsed > 86400)
					{
						echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
					}
					else
					{
						echo $dt->relative();
					}
				?></time></span>
			</div><!-- / .activity-details -->

			<div class="activity-event">
				<?php
				if ($this->row->log->get('scope') == 'activity.comment')
				{
					// For now, we can assume this is from a group.
					// @TODO: Find a better way to associate comments to their parent scope (group, projects)
					$group = Hubzero\User\Group::getInstance($this->row->log->get('scope_id'));

					if ($group)
					{
						?>
						<div class="activity-source icon-group">
							<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=activity'); ?>"><?php echo $group->get('description'); ?></a>
						</div>
						<?php
					}
				}
				else if (substr($this->row->log->get('scope'), 0, strlen('project')) == 'project')
				{
					// @TODO: Find a better way to associate comments to their parent scope (group, projects)
					require_once Component::path('com_projects') . '/models/project.php';

					$project = new Components\Projects\Models\Project($this->row->log->get('scope_id'));

					if ($project)
					{
						?>
						<div class="activity-source icon-project">
							<a href="<?php echo Route::url('index.php?option=com_projects&alias=' . $project->get('alias')); ?>"><?php echo $project->get('title'); ?></a>
						</div>
						<?php
					}
				}
				?>

				<?php
				$content = $this->row->log->get('description');
				$short = null;

				if (strlen(strip_tags($content)) > 150)
				{
					$short = Hubzero\Utility\String::truncate($content, 150, array('html' => true));
					?>
					<div class="activity-event-preview">
						<?php echo $short; ?>
						<p>
							<a class="more-content" href="#activity-event-content<?php echo $this->row->get('id'); ?>">
								<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_MORE'); ?>
							</a>
						</p>
					</div>
					<?php
				}
				?>
				<div class="activity-event-content<?php echo ($short ? ' hide' : ''); ?>" id="activity-event-content<?php echo $this->row->get('id'); ?>">
					<?php echo $content; ?>
				</div>
				<?php
				if (in_array($this->row->log->get('action'), array('updated', 'emailed', 'downloaded', 'uploaded', 'denied', 'voted', 'shared')))
				{
					$recipient = Hubzero\Activity\Recipient::all();

					$r = $recipient->getTableName();
					$l = Hubzero\Activity\Log::blank()->getTableName();

					$duplicates = $recipient
						->select($r . '.*')
						->including('log')
						->join($l, $l . '.id', $r . '.log_id')
						->whereEquals($r . '.scope', 'user')
						->whereEquals($r . '.scope_id', $this->member->get('id'))
						->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_UNPUBLISHED)
						->whereEquals($l . '.action', $this->row->log->get('action'))
						->whereEquals($l . '.scope', $this->row->log->get('scope'))
						->whereEquals($l . '.scope_id', $this->row->log->get('scope_id'))
						->whereEquals($l . '.created_by', $this->row->log->get('created_by'))
						->whereEquals($l . '.description', $this->row->log->get('description'))
						->where('created', '>=', Date::of($this->row->log->get('created'))->modify('-1 hour')->toSql())
						->where('created', '<', $this->row->log->get('created'))
						->order($r . '.created', 'desc')
						->rows();

					if ($duplicates->count() > 0)
					{
						?>
						<ul class="activity-duplicates">
							<?php foreach ($duplicates as $dup) { ?>
								<li>
									<span class="activity-action"><?php echo $dup->log->get('action'); ?></span>
									<span class="activity-time"><time datetime="<?php echo Date::of($dup->log->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($dup->log->get('created'))->toLocal('M j') . ' @ ' . Date::of($dup->log->get('created'))->toLocal('g:i a'); ?></time></span>
								</li>
							<?php } ?>
						</ul>
						<?php
					}
				}
				?>
			</div><!-- / .activity-event -->

			<div class="activity-options">
				<ul class="activity-options-main">
					<?php if (!$this->row->log->get('parent')) { ?>
						<li>
							<a
								data-id="activity<?php echo $this->row->get('id'); ?>"
								class="icon-starred tooltips"
								href="<?php echo Route::url($base . '&action=' . ($this->row->get('starred') ? 'un' : '') . 'star&activity=' . $this->row->get('id')); ?>"
								data-hrf-active="<?php echo Route::url($base . '&action=unstar&activity=' . $this->row->get('id')); ?>"
								data-hrf-inactive="<?php echo Route::url($base . '&action=star&activity=' . $this->row->get('id')); ?>"
								data-txt-active="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_UNSTAR'); ?>"
								data-txt-inactive="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_STAR'); ?>"
								title="<?php echo ($this->row->get('starred') ? Lang::txt('PLG_MEMBERS_ACTIVITY_UNSTAR') : Lang::txt('PLG_MEMBERS_ACTIVITY_STAR')); ?>"><!--
								--><?php echo ($this->row->get('starred') ? Lang::txt('PLG_MEMBERS_ACTIVITY_UNSTAR') : Lang::txt('PLG_MEMBERS_ACTIVITY_STAR')); ?><!--
							--></a>
						</li>
					<?php } ?>
					<li>
						<a
							data-id="activity<?php echo $this->row->get('id'); ?>"
							class="icon-delete tooltips"
							href="<?php echo Route::url($base . '&action=remove&activity=' . $this->row->get('id') . '&' . Session::getFormToken() . '=1'); ?>"
							title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_DELETE'); ?>"
							data-txt-confirm="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_CONFIRM_DELETE'); ?>"><!--
							--><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_DELETE'); ?><!--
						--></a>
					</li>
					<?php /*<li>
						<a
							data-id="activity<?php echo $this->row->get('id'); ?>"
							class="icon-options tooltips"
							href="#moreoptions<?php echo $this->row->get('id'); ?>"
							title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_OPTIONS'); ?>"><!--
							--><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_OPTIONS'); ?><!--
						--></a>
						<ul class="activity-options-more" id="moreoptions<?php echo $this->row->get('id'); ?>">
							<li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($base . '&action=unsubscribe&scope=' . $this->row->get('scope')); ?>"><?php echo Lang::txt('Hide all like this'); ?></a></li>
							<li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($base . '&action=share&activity=' . $this->row->get('id')); ?>"><?php echo Lang::txt('Share'); ?></a></li>
						</ul>
					</li>*/ ?>
				</ul>
			</div><!-- / .activity-options -->
		</div><!-- / .activity-body -->

		<div class="activity-processor">
			<div class="spinner"><div></div></div>
			<div class="msg"></div>
		</div><!-- / .activity-processor -->
	</div><!-- / .activity-content -->

</li>