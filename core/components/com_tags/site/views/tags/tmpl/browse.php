<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-tag btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
	// The search and pagination are separate GET forms rather than one form
	// around the page: the tags carry POST delete forms, and forms can't nest.
	?>
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">

			<form class="container data-entry" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<label for="entry-search-text"><?php echo Lang::txt('COM_TAGS_SEARCH_TAGS'); ?></label>
					<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER'); ?>" />
				</fieldset>
				<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
			</form><!-- / .container -->

			<div class="container">
				<nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
					<ul class="entries-menu sort-options">
						<li>
							<?php
								$filters = '&search=' . urlencode($this->filters['search']) . '&limit=' . Request::getInt('limit', 25) . '&limitstart=' . Request::getInt('limitstart', 0);

								$cls = ($this->filters['sort'] == 'total') ? 'active ' : '';
								$url = Route::url('index.php?option=' . $this->option . '&task=browse&sort=total&sortdir=' . ($cls ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc') : 'asc') . $filters);
							?>
							<a class="<?php echo $cls . ($cls ? ($this->filters['sort_Dir'] =='desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'); ?>" href="<?php echo $url; ?>" title="<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY_TITLE'); ?>">
								<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY'); ?>
							</a>
						</li>
						<li>
							<?php
								$cls = ($this->filters['sort'] == '' || $this->filters['sort'] == 'raw_tag') ? 'active ' : '';
								$url = Route::url('index.php?option=' . $this->option . '&task=browse&sort=raw_tag&sortdir=' . ($cls ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc') : 'asc') . $filters);
							?>
							<a class="<?php echo $cls . ($cls ? ($this->filters['sort_Dir'] =='desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'); ?>" href="<?php echo $url; ?>" title="<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA_TITLE'); ?>">
								<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA'); ?>
							</a>
						</li>
					</ul>
				</nav>

				<table class="entries" id="taglist">
					<!-- <caption>
						<?php
						if (!$this->filters['limit'])
						{
							$this->filters['limit'] = $this->total;
						}
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '') {
							echo Lang::txt('COM_TAGS_BROWSE_SEARCH_FOR_IN', $this->escape($this->filters['search']), Lang::txt('COM_TAGS'));
						} else {
							echo Lang::txt('COM_TAGS');
						}
						?>
						<span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
					</caption> -->
					<thead>
						<tr>
							<th scope="col">
								<?php echo Lang::txt('COM_TAGS_TAG'); ?>
							</th>
							<th class="priority-3" scope="col">
								<?php echo Lang::txt('COM_TAGS_COL_ALIAS'); ?>
							</th>
							<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
								<th scope="col" colspan="2">
									<?php echo Lang::txt('COM_TAGS_COL_ACTION'); ?>
								</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php
					if ($this->rows->count())
					{
						$cls = 'even';
						foreach ($this->rows as $row)
						{
							$cls = ($cls == 'even' ? 'odd' : 'even');
							?>
							<tr class="<?php echo $cls; ?>">
								<td>
									<a class="tag <?php echo $row->get('admin') ? ' admin' : ''; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&tag=' . $row->get('tag')); ?>">
										<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
									</a>
								</td>
								<td class="priority-3">
									<?php
									$subs = $row->get('substitutes') ? $this->escape($row->substitutes) : '';

									echo $subs ? \Hubzero\Utility\Str::truncate($subs, 75) : '<span>' . Lang::txt('COM_TAGS_NONE') . '</span>';
									?>
								</td>
								<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
									<td>
										<?php if ($this->config->get('access-delete-tag')) { ?>
											<button type="submit" form="hz-post-action" formaction="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&id[]=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sort=' . $this->filters['sort'] . '&sortdir=' . $this->filters['sort_Dir'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>" class="icon-delete delete delete-tag" data-confirm="<?php echo Lang::txt('COM_TAGS_CONFIRM_DELETE'); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></button>
										<?php } ?>
									</td>
									<td>
										<?php if ($this->config->get('access-edit-tag')) { ?>
											<a class="icon-edit edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sort=' . $this->filters['sort'] . '&sortdir=' . $this->filters['sort_Dir'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>" title="<?php echo Lang::txt('COM_TAGS_EDIT_TAG', $this->escape(stripslashes($row->get('raw_tag')))); ?>">
												<?php echo Lang::txt('JACTION_EDIT'); ?>
											</a>
										<?php } ?>
									</td>
								<?php } ?>
							</tr>
							<?php
						}
					}
					else
					{
						?>
						<tr class="odd">
							<td colspan="<?php echo $this->config->get('access-edit-tag') || $this->config->get('access-delete-tag') ? 4 : 2; ?>">
								<?php echo Lang::txt('COM_TAGS_NO_RESULTS'); ?>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
					<input type="hidden" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
					<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
					<?php
						// Initiate paging
						$pageNav = $this->pagination(
							$this->total,
							$this->filters['start'],
							$this->filters['limit']
						);
						$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
						$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
						$pageNav->setAdditionalUrlParam('sortdir', $this->filters['sort_Dir']);
						echo $pageNav->render();
					?>
				</form>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .main subject -->
		<aside class="aside">
			<div class="container">
				<p>
					<?php echo Lang::txt('COM_TAGS_BROWSE_EXPLANATION'); ?>
				</p>
				<p class="help">
					<strong><?php echo Lang::txt('COM_TAGS_WHATS_AN_ALIAS'); ?></strong>
					<br /><?php echo Lang::txt('COM_TAGS_ALIAS_EXPLANATION'); ?>
				</p>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</div>
</section><!-- / .main section -->
