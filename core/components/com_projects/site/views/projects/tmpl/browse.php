<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')))
{
	$this->css('reviewers')
		 ->css('jquery.fancybox.css', 'system');
}

$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

// Get count
$total = $this->model->entries('count', $this->filters);

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if (User::authorise('core.create', $this->option)) { ?>
		<div id="content-header-extra">
			<p>
				<a class="btn icon-add" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=start'); ?>">
					<?php echo Lang::txt('COM_PROJECTS_START_NEW'); ?>
				</a>
			</p>
		</div><!-- / #content-header-extra -->
	<?php } ?>
</header><!-- / #content-header -->

<form method="get" id="browseForm" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
	<section class="main section">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo Lang::txt('COM_PROJECTS_SEARCH'); ?></legend>
				<label for="entry-search-field"><?php echo Lang::txt('COM_PROJECTS_ENTER_PHRASE'); ?></label>
				<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PROJECTS_ENTER_PHRASE'); ?>" />
				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
			</fieldset>
		</div>
		<div class="container">
			<nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search'])    : '');
				$qs .= ($this->filters['start']  ? '&limitstart=' . $this->escape($this->filters['start']) : '');
				$qs .= ($this->filters['limit']  ? '&limit=' . $this->escape($this->filters['limit'])      : '');
				$qs .= '&sortdir=' . $sortbyDir;
				if ($this->filters['reviewer'])
				{
					$qs .= '&reviewer=' . $this->filters['reviewer'];
				}
				?>
				<ul class="entries-menu order-options">
					<?php if (!empty($this->filters['reviewer']) && (strtolower($this->filters['reviewer']) == 'sponsored')): ?>
					<li><a<?php echo ($this->filters['sortby'] == 'grant_status') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=grant_status' . $qs); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?>">&darr; <?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?></a></li>
					<?php endif;?>
					<li><a<?php echo ($this->filters['sortby'] == 'owner') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=owner' . $qs); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_OWNER'); ?>">&darr; <?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=title' . $qs); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_TITLE'); ?>">&darr; <?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></a></li>
				</ul>
				<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_PROJECTS_BROWSE_SHOW'); ?>">
					<li>
						<label for="filterby"><?php echo Lang::txt('COM_PROJECTS_BROWSE_SHOW'); ?></label>
						<select name="filterby" id="filterby">
							<option value="all" <?php echo ($this->filters['filterby'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_ALL'); ?></option>
							<?php /*<option value="public" <?php echo ($this->filters['filterby'] == 'public') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_PUBLIC'); ?></option>
							<option value="open" <?php echo ($this->filters['filterby'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_OPEN'); ?></option>*/ ?>
							<option value="archived" <?php echo ($this->filters['filterby'] == 'archived') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_ARCHIVED'); ?></option>
							<?php if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive'))) { ?>
								<option value="pending" <?php echo ($this->filters['filterby'] == 'pending') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_PENDING'); ?></option>
							<?php } ?>
						</select>
					</li>
				</ul>
			</nav>

			<?php
			$rows = $this->model->entries('list', $this->filters);

			if (count($rows))
			{
				// Display List of items
				$this->view('_list')
				     ->set('rows', $rows)
				     ->set('filters', $this->filters)
				     ->set('model', $this->model)
				     ->set('option', $this->option)
				     ->display();

				// Pagination
				$pageNav = $this->pagination(
					$total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pagenavhtml = $pageNav->render();
				$pagenavhtml = str_replace('projects/?', 'projects/browse/?', $pagenavhtml);
				?>
				<?php echo $pagenavhtml; ?>
				<?php if (!empty($this->filters['reviewer'])): ?>
					<input type="hidden" name="reviewer" value="<?php echo $this->filters['reviewer'];?>" />
				<?php endif; ?>
				<div class="clear"></div>
				<?php
			}
			else
			{
				if (User::isGuest())
				{
					echo '<p class="noresults">' . Lang::txt('COM_PROJECTS_NO_PROJECTS_FOUND') . ' ' . Lang::txt('COM_PROJECTS_PLEASE') . ' <a href="' . Route::url('index.php?option=' . $this->option . '&task=browse&action=login') . '">' . Lang::txt('COM_PROJECTS_LOGIN') . '</a> ' . Lang::txt('COM_PROJECTS_TO_VIEW_PRIVATE_PROJECTS') . '</p>';
				}
				else
				{
					if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')))
					{
						$txt = $this->filters['filterby'] == 'pending'
						? Lang::txt('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_PENDING')
						: Lang::txt('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_ALL');
						echo '<p class="noresults">' . $txt . '</p>';
					}
					else
					{
						echo '<p class="noresults">' . Lang::txt('COM_PROJECTS_NO_AUTHOROZED_PROJECTS_FOUND') . '</p>';
					}
				}
			}
			?>
		</div>
	</section><!-- / .main section -->
</form>
