<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS'); ?></h3>

<?php if (User::authorise('core.create', 'com_projects')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_ADD'); ?>
			</a>
		</li>
	</ul>
<?php } ?>

<?php if (User::get('id') == $this->user->get('id')) { ?>
	<ul class="sub-menu">
		<li class="active">
			<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=all'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_LIST') . ' (' . $this->total . ')'; ?>
			</a>
		</li>
		<li>
			<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=updates'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) { echo '<span class="s-new">' . $this->newcount . '</span>'; } ?>
			</a>
		</li>
	</ul>
<?php } ?>

<div id="s-projects">
	<div class="container">
		<nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
			<ul class="entries-menu filter-options">
				<li>
					<a<?php echo (!$this->filters['filterby'] || $this->filters['filterby'] == 'active') ? ' class="active"' : ''; ?> data-status="all" href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=all'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_FILTER_STATUS_ACTIVE'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['filterby'] == 'archived') ? ' class="active"' : ''; ?> data-status="manager" href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=all&filterby=archived'); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_FILTER_STATUS_ARCHIVED'); ?>
					</a>
				</li>
			</ul>
		</nav>

		<?php
		if (count($this->invites))
		{
			?>
			<table class="entries">
				<caption><?php echo Lang::txt('PLG_MEMBERS_PROJECTS_INVITED') . ' <span>(' . count($this->invites) . ')</span>'; ?></caption>
				<tbody>
					<?php
					foreach ($this->invites as $invite)
					{
						$row = new Components\Projects\Models\Project($invite->projectid);
						?>
						<tr class="mline">
							<td class="th_image">
								<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>">
									<img src="<?php echo Route::url($row->link('thumb')); ?>" alt="<?php echo htmlentities($this->escape($row->get('title'))); ?>" class="project-image" />
								</a>
							</td>
							<td class="th_privacy">
								<?php if (!$row->isPublic()) { echo '<span class="privacy-icon">&nbsp;</span>'; } ?>
							</td>
							<td class="th_title">
								<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>"><?php echo $this->escape($row->get('title')); ?></a>
							</td>
							<td>
								<a class="btn btn-success" href="<?php echo Route::url('index.php?option=com_projects&alias=' . $invite->alias . '&confirm=' . $invite->invited_code . '&email=' . $invite->invited_email); ?>"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS_ACCEPT'); ?></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}

		if ($this->which == 'all')
		{
			// Show owned projects first
			$this->view('list')
			     ->set('option', $this->option)
			     ->set('rows', $this->owned)
			     ->set('which', 'owned')
			     ->set('config', $this->config)
			     ->set('user', $this->user)
			     ->set('filters', $this->filters)
			     ->display();

			echo '</div><div class="container">';
		}

		// Show rows
		$this->view('list')
		     ->set('option', $this->option)
		     ->set('rows', $this->rows)
		     ->set('config', $this->config)
		     ->set('user', $this->user)
		     ->set('which', $this->filters['which'])
		     ->set('filters', $this->filters)
		     ->display();
		?>
	</div>
</div>
