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
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<label for="entry-search-text"><?php echo Lang::txt('COM_TAGS_SEARCH_TAGS'); ?></label>
					<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER'); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<nav class="entries-filters">
					<ul class="entries-menu sort-options">
						<li>
							<?php
								$filters = '&search=' . urlencode($this->filters['search']) . '&limit=' . Request::getInt('limit', 25) . '&limitstart=' . Request::getInt('limitstart', 0);

								$cls = ($this->filters['sort'] == 'total') ? 'active ' : '';
								$url = Route::url('index.php?option=' . $this->option . '&task=browse&sort=total&sortdir=' . ($cls ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc') : 'asc') . $filters);
							?>
							<a class="<?php echo $cls . ($cls ? $this->filters['sort_Dir'] : 'asc'); ?>" href="<?php echo $url; ?>" title="<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY_TITLE'); ?>">
								<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_POPULARITY'); ?>
							</a>
						</li>
						<li>
							<?php
								$cls = ($this->filters['sort'] == '' || $this->filters['sort'] == 'raw_tag') ? 'active ' : '';
								$url = Route::url('index.php?option=' . $this->option . '&task=browse&sort=raw_tag&sortdir=' . ($cls ? ($this->filters['sort_Dir'] == 'desc' ? 'asc' : 'desc') : 'asc') . $filters);
							?>
							<a class="<?php echo $cls . ($cls ? $this->filters['sort_Dir'] : 'asc'); ?>" href="<?php echo $url; ?>" title="<?php echo Lang::txt('COM_TAGS_BROWSE_SORT_ALPHA_TITLE'); ?>">
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
									<a class="tag <?php echo ($row->get('admin') ? ' admin' : ''); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&tag=' . $row->get('tag')); ?>">
										<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
									</a>
								</td>
								<td class="priority-3">
									<?php
									$subs = $row->get('substitutes') ? $this->escape($row->substitutes) : '';

									echo $subs ? \Hubzero\Utility\String::truncate($subs, 75) : '<span>' . Lang::txt('COM_TAGS_NONE') . '</span>';
									?>
								</td>
								<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
									<td>
										<?php if ($this->config->get('access-delete-tag')) { ?>
											<a class="icon-delete delete delete-tag" data-confirm="<?php echo Lang::txt('COM_TAGS_CONFIRM_DELETE'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&id[]=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sort=' . $this->filters['sort'] . '&sortdir=' . $this->filters['sort_Dir'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
												<?php echo Lang::txt('JACTION_DELETE'); ?>
											</a>
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
							<td colspan="<?php echo ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag') ? 4 : 2); ?>">
								<?php echo Lang::txt('COM_TAGS_NO_RESULTS'); ?>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
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
				<div class="clearfix"></div>
				<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
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
	</form>
</section><!-- / .main section -->
