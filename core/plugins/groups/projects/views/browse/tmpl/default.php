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

<h3 class="section-header"><?php echo Lang::txt('PLG_GROUPS_PROJECTS'); ?></h3>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->group->published == 1 && User::authorise('core.create', 'com_projects')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url('index.php?option=com_projects&task=start&gid=' . $this->group->get('gidNumber')); ?>">
				<?php echo Lang::txt('PLG_GROUPS_PROJECTS_ADD'); ?>
			</a>
		</li>
	</ul>
<?php } ?>

<?php 
// Output the submenu view
$view = $this->view('submenu', 'partials')
	->set('group', $this->group)
	->set('projectcount', $this->projectcount)
	->set('newcount', $this->newcount)
	->set('tab', 'all')
	->display();
?>

<section class="main section" id="s-projects">
	<div class="container">
		<nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
			<ul class="entries-menu filter-options">
				<li>
					<a<?php echo (!$this->filters['filterby'] || $this->filters['filterby'] == 'active') ? ' class="active"' : ''; ?> data-status="all" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=projects&action=all'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_PROJECTS_FILTER_STATUS_ACTIVE'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['filterby'] == 'archived') ? ' class="active"' : ''; ?> data-status="manager" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=projects&action=all&filterby=archived'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_PROJECTS_FILTER_STATUS_ARCHIVED'); ?>
					</a>
				</li>
			</ul>
		</nav>

		<!-- Placeholder for Group Dashboard -->
		<?php 
		$dashboards = Event::trigger('groups.onGroupClassroomprojects', array($this->group));
		foreach ($dashboards as $dashboard)
		{
			echo $dashboard;
		}
		?>
		<!-- End placeholder for Group Dashboard -->

		<?php
		if ($this->which == 'all')
		{
			// Show owned projects first
			$this->view('list')
			     ->set('option', $this->option)
			     ->set('rows', $this->owned)
			     ->set('config', $this->config)
			     ->set('which', 'owned')
			     ->display();

			echo '</div><div class="container">';
		}
		// Show rows
		$this->view('list')
		     ->set('option', $this->option)
		     ->set('rows', $this->rows)
		     ->set('config', $this->config)
		     ->set('which', $this->filters['which'])
		     ->display();
		?>
		</div>
</section><!-- /.main section -->
