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

$first = $this->model->entries('first');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';

$this->css()
     ->js();
?>

<?php if ($this->canpost || ($this->authorized == 'manager' || $this->authorized == 'admin')) { ?>
	<ul id="page_options">
		<?php if ($this->canpost) { ?>
			<li>
				<a class="icon-add add btn" href="<?php echo Route::url($base . '&action=new'); ?>">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_NEW_ENTRY'); ?>
				</a>
			</li>
		<?php } ?>
		<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
			<li>
				<a class="icon-config config btn" href="<?php echo Route::url($base . '&action=settings'); ?>">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_SETTINGS'); ?>
				</a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>

<form method="get" action="<?php echo Route::url($base . '&action=browse'); ?>" id="blogentries">
	<section class="section">
		<div class="subject">
			<?php if ($this->getError()) : ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php endif; ?>

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('PLG_GROUPS_BLOG_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_BLOG_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape(utf8_encode(stripslashes($this->search))); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_BLOG_SEARCH_PLACEHOLDER'); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<h3>
					<?php if (isset($this->search) && $this->search) { ?>
						<?php echo Lang::txt('PLG_GROUPS_BLOG_SEARCH_FOR', $this->escape($this->search)); ?>
					<?php } else if (!isset($this->year) || !$this->year) { ?>
						<?php echo Lang::txt('PLG_GROUPS_BLOG_LATEST_ENTRIES'); ?>
					<?php } elseif (isset($this->year) && isset($this->month) && $this->month == 0) { ?>
						<?php echo Lang::txt('PLG_GROUPS_BLOG_YEAR_ENTRIES_FOR', $this->year); ?>
					<?php } else {
						$archiveDate  = $this->year;
						$archiveDate .= ($this->month) ? '-' . $this->month : '-01';
						$archiveDate .= '-01 00:00:00';
						if ($this->month)
						{
							echo Date::of($archiveDate)->format('M Y');
						}
						else
						{
							echo Date::of($archiveDate)->format('Y');
						}
					} ?>
					<?php
						if ($this->config->get('feeds_enabled', 1)) :
							$path  = $base . '&scope=feed.rss';
							$path .= ($this->year)  ? '&year=' . $this->year   : '';
							$path .= ($this->month) ? '&month=' . $this->month : '';
							$feed = Route::url($path);
							$live_site = 'https://' . $_SERVER['HTTP_HOST'];
							if (substr($feed, 0, 4) != 'http')
							{
								$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
							}
							$feed = str_replace('https://', 'http://', $feed);
					?>
					<a class="feed" href="<?php echo $feed; ?>">
						<?php echo Lang::txt('PLG_GROUPS_BLOG_RSS_FEED'); ?>
					</a>
					<?php endif; ?>
				</h3>
			<?php
			$rows = $this->model->entries('list', $this->filters, false, false);
			if ($rows->total() > 0) { ?>
				<ol class="blog-entries entries">
			<?php
				$cls = 'even';
				foreach ($rows as $row)
				{
					$cls = ($cls == 'even') ? 'odd' : 'even';

					$clse = '';
					if (!$row->isAvailable())
					{
						if ($row->get('created_by') != User::get('id'))
						{
							continue;
						}
						$clse = ' pending';
					}
					if ($row->ended())
					{
						$clse = ' expired';
					}
					if ($row->get('state') == 0)
					{
						$clse = ' private';
					}

					?>
					<li class="<?php echo $cls . $clse; ?>" id="e<?php echo $row->get('id'); ?>">
						<article>
							<h4 class="entry-title">
								<a href="<?php echo Route::url($row->link()); ?>">
									<?php echo $this->escape(stripslashes($row->get('title'))); ?>
								</a>
							</h4>
							<dl class="entry-meta">
								<dt>
									<span>
										<?php echo Lang::txt('PLG_GROUPS_BLOG_ENTRY_NUMBER', $row->get('id')); ?>
									</span>
								</dt>
								<dd class="date">
									<time datetime="<?php echo $row->published(); ?>">
										<?php echo $row->published('date'); ?>
									</time>
								</dd>
								<dd class="time">
									<time datetime="<?php echo $row->published(); ?>">
										<?php echo $row->published('time'); ?>
									</time>
								</dd>
								<dd class="author">
									<?php if ($row->creator('public')) { ?>
										<a href="<?php echo Route::url($row->creator()->getLink()); ?>">
											<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
										</a>
									<?php } else { ?>
										<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
									<?php } ?>
								</dd>
								<?php if ($row->get('allow_comments') == 1) { ?>
									<dd class="comments">
										<a href="<?php echo Route::url($row->link('comments')); ?>">
											<?php echo Lang::txt('PLG_GROUPS_BLOG_NUM_COMMENTS',  $row->get('comments', 0)); ?>
										</a>
									</dd>
								<?php } else { ?>
									<dd class="comments">
										<span>
											<?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
										</span>
									</dd>
								<?php } ?>
								<?php if (User::get('id') == $row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
									<dd class="state <?php echo $row->state('text'); ?>">
										<?php echo Lang::txt('PLG_GROUPS_BLOG_STATE_' . strtoupper($row->state('text'))); ?>
									</dd>
								<?php } ?>
								<dd class="entry-options">
									<?php if (User::get('id') == $row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
										<a class="icon-edit edit" href="<?php echo Route::url($row->link('edit')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT'); ?>">
											<?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT'); ?>
										</a>
										<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>">
											<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>
										</a>
									<?php } ?>
								</dd>
							</dl>
							<div class="entry-content">
								<?php if ($this->config->get('cleanintro', 1)) { ?>
									<p>
										<?php echo $row->content('clean', $this->config->get('introlength', 300)); ?>
									</p>
								<?php } else { ?>
									<?php echo $row->content('parsed', $this->config->get('introlength', 300)); ?>
								<?php } ?>
							</div>
						</article>
					</li>
		<?php } ?>
				</ol>
				<?php
					$pageNav = $this->pagination(
						$this->model->entries('count', $this->filters),
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
					$pageNav->setAdditionalUrlParam('active', 'blog');
					if ($this->filters['year'])
					{
						$pageNav->setAdditionalUrlParam('year', $this->filters['year']);
					}
					if ($this->filters['month'])
					{
						$pageNav->setAdditionalUrlParam('month', $this->filters['month']);
					}
					if ($this->filters['search'])
					{
						$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					}
					echo $pageNav->render();
				?>
	<?php } else { ?>
				<p class="warning"><?php echo Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
	<?php } ?>
			</div>
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container blog-entries-years">
				<h4><?php echo Lang::txt('PLG_GROUPS_BLOG_ENTRIES_BY_YEAR'); ?></h4>
				<ol>
					<?php if ($first->exists()) { ?>
						<?php
							$start = intval(substr($first->get('publish_up'), 0, 4));
							$now = date("Y");
						?>
						<?php for ($i=$now, $n=$start; $i >= $n; $i--) { ?>
							<li>
								<a href="<?php echo Route::url($base . '&scope=' . $i); ?>">
									<?php echo $i; ?>
								</a>
								<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) { ?>
									<ol>
										<?php
											$m = array(
												'PLG_GROUPS_BLOG_JANUARY',
												'PLG_GROUPS_BLOG_FEBRUARY',
												'PLG_GROUPS_BLOG_MARCH',
												'PLG_GROUPS_BLOG_APRIL',
												'PLG_GROUPS_BLOG_MAY',
												'PLG_GROUPS_BLOG_JUNE',
												'PLG_GROUPS_BLOG_JULY',
												'PLG_GROUPS_BLOG_AUGUST',
												'PLG_GROUPS_BLOG_SEPTEMBER',
												'PLG_GROUPS_BLOG_OCTOBER',
												'PLG_GROUPS_BLOG_NOVEMBER',
												'PLG_GROUPS_BLOG_DECEMBER'
											);
											if ($i == $now) {
												$months = date("m");
											} else {
												$months = 12;
											}
										?>
										<?php for ($k=0, $z=$months; $k < $z; $k++) { ?>
											<li>
												<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo Route::url($base . '&scope='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>">
													<?php echo Lang::txt($m[$k]); ?>
												</a>
											</li>
										<?php } ?>
									</ol>
								<?php } ?>
							</li>
						<?php } ?>
					<?php } ?>
				</ol>
			</div>

			<?php
			$limit = $this->filters['limit'];
			$this->filters['limit'] = 5;
			?>
			<div class="container blog-popular-entries">
				<h4><?php echo Lang::txt('PLG_GROUPS_BLOG_POPULAR_ENTRIES'); ?></h4>
				<?php
				$popular = $this->model->entries('popular', $this->filters);
				if ($popular->total() > 0) { ?>
					<ol>
					<?php foreach ($popular as $row) { ?>
						<?php
							if (!$row->isAvailable() && $row->get('created_by') != User::get('id'))
							{
								continue;
							}
						?>
						<li>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						</li>
					<?php } ?>
					</ol>
				<?php } else { ?>
					<p><?php echo Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
			</div><!-- / .blog-popular-entries -->

			<div class="container blog-recent-entries">
				<h4><?php echo Lang::txt('PLG_GROUPS_BLOG_RECENT_ENTRIES'); ?></h4>
				<?php
				$recent = $this->model->entries('recent', $this->filters);
				if ($recent->total() > 0) { ?>
					<ol>
					<?php foreach ($recent as $row) { ?>
						<?php
							if (!$row->isAvailable() && $row->get('created_by') != User::get('id'))
							{
								continue;
							}
						?>
						<li>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						</li>
					<?php } ?>
					</ol>
				<?php } else { ?>
					<p><?php echo Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
			</div><!-- / .blog-recent-entries -->
			<?php
			$this->filters['limit'] = $limit;
			?>
		</aside><!-- / .aside -->
	</section>
</form><!-- /.main -->

