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

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt('COM_KB'),
		'index.php?option=' . $this->option
	);
}
Pathway::append(
	$this->category->get('title'),
	$this->category->link()
);

Document::setTitle(Lang::txt('COM_KB') . ': ' . $this->category->get('title'));
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_KB'); ?></h2>

	<div id="content-header-extra">
		<?php
		$this->view('_search')
		     ->set('option', $this->option)
		     ->set('filters', $this->filters)
		     ->display();
		?>
		<p>
			<a class="icon-main main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_KB_MAIN'); ?></a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner hz-layout-with-aside">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<div class="subject">
			<?php
			// Sort links keep the search; the search box keeps the sort
			$search = $this->filters['search'];
			$carry  = $search ? '&search=' . urlencode($search) : '';
			?>
			<div class="container">
				<?php if ($search) { ?>
					<p class="search-results-for">
						<?php echo Lang::txt('COM_KB_SEARCH_RESULTS_FOR', $this->escape($search)); ?>
						<a href="<?php echo Route::url($this->category->link() . ($this->filters['sort'] ? '&sort=' . $this->filters['sort'] : '')); ?>"><?php echo Lang::txt('COM_KB_SEARCH_CLEAR'); ?></a>
					</p>
				<?php } ?>
				<nav class="entries-filters" aria-label="<?php echo Lang::txt('COM_KB_SORT_LABEL'); ?>">
					<ul class="entries-menu">
						<li>
							<a<?php echo ($this->filters['sort'] == 'popularity') ? ' class="active" aria-current="page"' : ''; ?> href="<?php echo Route::url($this->category->link() . '&sort=popularity' . $carry); ?>" title="<?php echo Lang::txt('COM_KB_SORT_BY_POPULAR'); ?>">
								<?php echo Lang::txt('COM_KB_SORT_POPULAR'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sort'] == 'recent') ? ' class="active" aria-current="page"' : ''; ?> href="<?php echo Route::url($this->category->link() . '&sort=recent' . $carry); ?>" title="<?php echo Lang::txt('COM_KB_SORT_BY_RECENT'); ?>">
								<?php echo Lang::txt('COM_KB_SORT_RECENT'); ?>
							</a>
						</li>
					</ul>
				</nav>

				<?php
				$filters = array('state' => 1, 'access' => User::getAuthorisedViewLevels());

				$categories = $this->archive->categories($filters);

				if (!$this->category->get('id'))
				{
					$articles = $this->archive->articles();
				}
				else
				{
					$articles = $this->category->articles();
				}

				$articles->whereEquals('state', 1)
						->whereIn('access', User::getAuthorisedViewLevels());

				if (isset($this->filters['search']) && $this->filters['search'])
				{
					$articles->whereLike('title', $this->filters['search'], 1)
						->orWhereLike('fulltxt', $this->filters['search'], 1)
						->resetDepth();
				}
				if ($this->filters['sort'] == 'popularity')
				{
					$articles->order('helpful', 'desc');
				}
				else
				{
					$articles->order('modified', 'desc')
							->order('created', 'desc');
				}

				$articles = $articles->paginated();

				if ($articles->count() > 0) { ?>
				<table class="articles entries">
					<caption class="sr-only"><?php echo Lang::txt('COM_KB_ARTICLES'); ?></caption>
					<thead class="sr-only">
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_KB_COL_ID'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KB_COL_ARTICLE'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KB_COL_VOTES'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($articles as $row)
					{
						if (!$this->category->get('id'))
						{
							foreach ($categories as $cat)
							{
								if ($cat->get('id') == $row->get('category'))
								{
									$row->set('ctitle', $cat->get('title'));
									$row->set('calias', $cat->get('path'));
									break;
								}
							}
						}
						else
						{
							$row->set('calias', $this->category->get('path'));
							$row->set('ctitle', $this->category->get('title'));
						}
						?>
						<tr>
							<td>
								<span class="entry-identifier icon-file"><?php echo $row->get('id'); ?></span>
							</td>
							<td>
								<a class="entry-title" href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(stripslashes($row->get('title',''))); ?></a><br />
								<?php if ($search && ($snippet = $row->snippet($search))) { ?>
									<span class="entry-snippet"><?php echo $snippet; ?></span>
								<?php } ?>
								<span class="entry-details">
									<?php if ($this->catid <= 0) { echo Lang::txt('COM_KB_IN_CATEGORY', $this->escape(stripslashes($row->get('ctitle','')  ?? ''))); } ?>
									<?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>
									<span class="entry-time-at"><?php echo Lang::txt('COM_KB_DATETIME_AT'); ?></span>
									<span class="entry-time"><?php echo $row->modified('time'); ?></span>
									<span class="entry-date-on"><?php echo Lang::txt('COM_KB_DATETIME_ON'); ?></span>
									<span class="entry-date"><?php echo $row->modified('date'); ?></span>
								</span>
							</td>
							<td class="voting">
								<?php
								$view = $this->view('_vote')
										 ->set('option', $this->option)
										 ->set('item', $row)
										 ->set('type', 'entry')
										 ->set('vote', '')
										 ->set('id', '');
								if (!User::isGuest())
								{
									if ($row->get('user_id') == User::get('id'))
									{
										$view->set('vote', $row->get('vote'));
										$view->set('id', $row->get('id'));
									}
								}
								$view->display();
								?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php } ?>
				<?php
				echo $articles
						->pagination
						->setAdditionalUrlParam('search', $this->filters['search'])
						->setAdditionalUrlParam('sort', $this->filters['sort']);
				?>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<?php
			$this->view('_categories')
			     ->set('option', $this->option)
			     ->set('archive', $this->archive)
			     ->set('catid', $this->catid)
			     ->set('category', $this->category)
			     ->set('filters', $this->filters)
			     ->display();
			?>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
