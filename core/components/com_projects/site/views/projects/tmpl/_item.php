<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$isReviewer = (isset($this->filters['reviewer']) && in_array($this->filters['reviewer'], array('sponsored', 'sensitive')));

$src = Route::url($this->row->picture('master'));

$role = $this->row->access('member')
	? ($this->row->access('manager') ? Lang::txt('COM_PROJECTS_LABEL_OWNER') : Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR'))
	: '';

$role = $this->row->access('readonly') && !$this->row->isArchived()
	? Lang::txt('COM_PROJECTS_LABEL_REVIEWER')
	: $role;
?>
<div class="project-card" id="project-<?php echo $this->row->get('id'); ?>">
	<div class="project-contents">
		<?php if ((!$this->row->inSetup() && $this->row->access('view'))
				|| ($this->row->inSetup() && $this->row->access('owner'))): ?>
			<a class="project-identity" href="<?php echo Route::url($this->row->link()); ?>">
				<img src="<?php echo $src; ?>" alt="<?php echo $this->escape($this->row->get('title')); ?>" />
			</a>
		<?php else: ?>
			<span class="project-identity">
				<img src="<?php echo $src; ?>" alt="<?php echo $this->escape($this->row->get('title')); ?>" />
			</span>
		<?php endif; ?>

		<?php if ($this->row->get('featured')): ?>
			<span class="icon-star project-featured tooltips" title="<?php echo Lang::txt('COM_PROJECTS_FEATURED'); ?>">
				<span><?php echo Lang::txt('COM_PROJECTS_FEATURED'); ?></span>
			</span>
		<?php endif; ?>

		<div class="project-details">
			<span class="project-alias"><?php echo $this->escape($this->row->get('alias')); ?></span>

			<?php if ((!$this->row->inSetup() && $this->row->access('view'))
					|| ($this->row->inSetup() && $this->row->access('owner'))): ?>
				<a class="project-title" rel="<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($this->row->link()); ?>">
					<?php echo $this->escape(Hubzero\Utility\Str::truncate($this->row->get('title'), 60)); ?>
				</a>
			<?php else: ?>
				<span class="project-title">
					<?php echo $this->escape(Hubzero\Utility\Str::truncate($this->row->get('title'), 60)); ?>
				</span>
			<?php endif; ?>

			<?php if ($role): ?>
				<span class="<?php echo str_replace(array('(', ')'), '', $role); ?> project-membership-status">
					<?php echo $role; ?>
				</span>
			<?php endif; ?>

			<?php
			// Private
			//$icon = 'icon-eye-close';
			$icon = 'icon-lock';
			$privacyTxt = Lang::txt('COM_PROJECTS_PRIVATE');

			// Open project
			/*if ($this->row->get('private') < 0):
				$privacyTxt = Lang::txt('COM_PROJECTS_OPEN');
				$icon = 'icon-unlock';
			endif;

			// Public
			if ($this->row->get('private') == 0):
				$privacyTxt = Lang::txt('COM_PROJECTS_PUBLIC');
				$icon = 'icon-lock';
			endif;*/
			if ($this->row->access('member')):
				$icon = 'icon-unlock';
			endif;

			// Archived
			if ($this->row->isArchived()):
				$privacyTxt = Lang::txt('COM_PROJECTS_ARCHIVED');
				$icon = 'icon-archive';
			endif;
			?>
			<span class="<?php echo $icon; ?> project-privacy tooltips" title="<?php echo $privacyTxt; ?>">
				<?php echo $privacyTxt; ?>
			</span>
		</div>

		<div class="project-meta">
			<?php
			$setup = $this->row->inSetup() ? Lang::txt('COM_PROJECTS_COMPLETE_SETUP') : '';
			?>
			<?php if ($setup && $this->row->access('member')): ?>
				<div class="project-state s-complete"><?php echo Lang::txt('COM_PROJECTS_COMPLETE_SETUP'); ?></div>
			<?php elseif ($this->row->get('state') == 0): ?>
				<div class="project-state s-suspended"><?php echo Lang::txt('COM_PROJECTS_STATUS_INACTIVE'); ?></div>
			<?php elseif ($this->row->get('state') == 5): ?>
				<div class="project-state s-suspended"><?php echo Lang::txt('COM_PROJECTS_STATUS_PENDING'); ?></div>
			<?php endif; ?>

			<?php /*if ($this->row->get('newactivity') && $this->row->isActive() && !$setup) { ?>
				<span class="s-new"><?php echo $this->row->get('newactivity'); ?></span>
			<?php endif;*/ ?>

			<?php if ($this->row->groupOwner()): ?>
				<span class="project-owner owner-group icon-group tooltips" title="<?php echo $this->escape(Lang::txt('This project is owned by the %s group', $this->row->groupOwner('description'))); ?>">
					<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->row->groupOwner('cn')); ?>">
						<?php echo $this->escape(Hubzero\Utility\Str::truncate($this->row->groupOwner('description'), 25)); ?>
					</a>
				</span>
			<?php else: ?>
				<?php
				$owner = $this->row->owner();

				$name = Lang::txt('COM_PROJECTS_UNKNOWN');
				if ($owner->get('id')):
					$name = $owner->get('name');
				endif;
				?>
				<span class="project-owner owner-user icon-user tooltips" title="<?php echo $this->escape(Lang::txt('This project is owned by %s', $name)); ?>">
					<?php
					if ($owner->get('id') && in_array($owner->get('access'), User::getAuthorisedViewLevels())):
						$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $owner->get('id')) . '">' . $this->escape($name) . '</a>';
					endif;

					echo $name;
					?>
				</span>
			<?php endif; ?>
			<?php
			// Reviewers
			if ($isReviewer && $this->row->owner()):
				echo '<span class="block owner-email">' . $this->row->owner('email') . '</span>';

				if ($this->row->owner('phone')):
					echo '<span class="block owner-telephone"> Tel.' . $this->row->owner('phone') . '</span>';
				endif;
			endif;
			?>
		</div>

		<?php
		// Reviewers extra info
		if ($isReviewer):
			// Get project params
			$params = new Hubzero\Config\Registry($this->row->get('params'));
			?>
			<div class="project-data">
				<?php
				if ($this->filters['reviewer'] == 'sensitive'): ?>
					<div class="project-sensitive">
						<?php
						$info = '';

						if ($params->get('hipaa_data') == 'yes'):
							$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA') . '</span>';
						endif;

						if ($params->get('ferpa_data') == 'yes'):
							$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA') . '</span>';
						endif;

						if ($params->get('export_data') == 'yes'):
							$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_EXPORT_CONTROLLED') . '</span>';
						endif;

						if ($params->get('irb_data') == 'yes'):
							$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_IRB') . '</span>';
						endif;

						if ($params->get('restricted_data') == 'maybe' && $params->get('followup') == 'yes'):
							$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_FOLLOW_UP_NECESSARY') . '</span>';
						endif;

						echo $info;
						?>
						<div class="grid">
							<div class="col span6">
								<?php
								if ($this->row->isActive()):
									echo '<span class="project-status active">' . Lang::txt('COM_PROJECTS_ACTIVE') . '</span>';
								elseif ($this->row->inSetup()):
									echo '<span class="project-status setup">' . Lang::txt('COM_PROJECTS_STATUS_SETUP') . '</span> ';
								elseif ($this->row->isInactive()):
									echo '<span class="project-status inactive">' . Lang::txt('COM_PROJECTS_STATUS_INACTIVE') . '</span> ';
								elseif ($this->row->isArchived()):
									echo '<span class="project-status archived">' . Lang::txt('COM_PROJECTS_STATUS_ARCHIVED') . '</span> ';
								elseif ($this->row->isPending()):
									echo '<span class="project-status pending">' . Lang::txt('COM_PROJECTS_STATUS_PENDING') . '</span>';
								endif;
								?>
							</div>
							<div class="col span6 omega">
								<?php
								$commentCount = 0;

								if ($this->row->get('admin_notes')):
									$commentCount = \Components\Projects\Helpers\Html::getAdminNoteCount($this->row->get('admin_notes'), 'sensitive');
									echo \Components\Projects\Helpers\Html::getLastAdminNote($this->row->get('admin_notes'), 'sensitive');
								endif;

								?>
								<span class="block">
									<a href="<?php echo Route::url('index.php?option=' . $this->option .  '&task=process&id=' . $this->row->get('id') . '&reviewer=' . $this->filters['reviewer']); ?>" class="showinbox"><?php echo $commentCount . ' ' . Lang::txt('COM_PROJECTS_COMMENTS'); ?></a>
								</span>

								<?php if ($this->row->isPending()): ?>
									<span class="manage">
										<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=process&id=' . $this->row->get('id') . '&reviewer=' . $this->filters['reviewer']); ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_APPROVE'); ?></a>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div><!-- / .project-sensitive -->
				<?php endif; ?>
				<?php if ($this->filters['reviewer'] == 'sponsored'): ?>
					<div class="project-grant">
						<table>
							<caption><?php echo Lang::txt('COM_PROJECTS_SPS_INFO'); ?></caption>
							<tbody>
								<?php foreach (array('title', 'PI', 'agency', 'budget') as $key): ?>
									<tr>
										<th scope="row"><?php echo Lang::txt('COM_PROJECTS_GRANT_' . strtoupper($key)); ?></th>
										<td><?php echo $this->escape($params->get('grant_' . $key)); ?></td>
									</tr>
								<?php endforeach; ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('Status'); ?></th>
									<td>
										<?php
										if (!$params->get('grant_approval') && $params->get('grant_status', 0) == 0):
											echo '<span class="project-grant-status pending">' . Lang::txt('COM_PROJECTS_STATUS_PENDING_SPS') . '</span>';
										elseif ($params->get('grant_approval') || $params->get('grant_status') == 1):
											echo '<span class="project-grant-status active">' . Lang::txt('COM_PROJECTS_APPROVAL_CODE') . ': ' . $params->get('grant_approval', '(N/A)') . '</span>';
										elseif ($params->get('grant_status') == '2'):
											echo '<span class="project-grant-status rejected">' . Lang::txt('COM_PROJECTS_STATUS_SPS_REJECTED') . '</span>';
										endif;
										?>

										<span class="manage">
											<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=process&id=' . $this->row->get('id')) . '?reviewer=' . $this->filters['reviewer'] . '&filterby=' . $this->filters['filterby']; ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_MANAGE'); ?></a>
										</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div><!-- / .project-grant -->
				<?php endif; ?>
			</div><!-- / .project-data -->
		<?php endif; ?>

		<?php
		$results = Event::trigger('projects.onProjectsBrowse', array($this->row));

		if (!empty($results)): ?>
			<div class="project-extras">
				<?php echo implode("\n", $results); ?>
			</div>
		<?php endif; ?>
	</div><!-- / .project-contents -->
</div><!-- / .project-card -->
