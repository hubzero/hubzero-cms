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

$this->css()
     ->js();

$filters = array(
	'scope'      => $this->filters['scope'],
	'scope_id'   => $this->filters['scope_id'],
	'state'      => $this->filters['state'],
	'authorized' => $this->filters['authorized']
);

$first = $this->model->entries('first', $filters);

$rows = $this->model->entries('list', $this->filters);
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<?php
		$path  = 'index.php?option=' . $this->option . '&task=feed.rss';
		$path .= ($this->filters['year']) ? '&year=' . $this->filters['year'] : '';
		$path .= ($this->filters['month']) ? '&month=' . $this->filters['month'] : '';

		$feed = Route::url($path);
		if (substr($feed, 0, 4) != 'http')
		{
			$live_site = rtrim(Request::base(),'/');

			$feed = rtrim($live_site, '/') . '/' . ltrim($feed, '/');
		}
		$feed = str_replace('https:://','http://', $feed);
		?>
		<p><a class="icon-feed feed btn" href="<?php echo $feed; ?>"><?php echo Lang::txt('COM_BLOG_FEED'); ?></a></p>
	</div>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="section-inner">
		<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_BLOG_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('COM_BLOG_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('COM_BLOG_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_BLOG_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<h3>
				<?php if (isset($this->filters['search']) && $this->filters['search']) { ?>
					<?php echo Lang::txt('COM_BLOG_SEARCH_FOR', $this->escape($this->filters['search'])); ?>
				<?php } else if (!isset($this->filters['year']) || !$this->filters['year']) { ?>
					<?php echo Lang::txt('COM_BLOG_LATEST_ENTRIES'); ?>
				<?php } else {
					$archiveDate  = $this->filters['year'];
					$archiveDate .= ($this->filters['month']) ? '-' . $this->filters['month'] : '-01';
					$archiveDate .= '-01 00:00:00';
					if ($this->filters['month'])
					{
						echo Date::of($archiveDate)->format('M Y');
					}
					else
					{
						echo Date::of($archiveDate)->format('Y');
					}
				} ?>
				</h3>

				<?php if ($rows->total() > 0) { ?>
					<ol class="blog-entries entries">
					<?php
					$cls = 'even';
					foreach ($rows as $row)
					{
						$cls = ($cls == 'even') ? 'odd' : 'even';

						if ($row->ended())
						{
							$cls .= ' expired';
						}
					?>
						<li class="<?php echo $cls; ?>" id="e<?php echo $row->get('id'); ?>">
							<article>
								<h4 class="entry-title">
									<a href="<?php echo Route::url($row->link()); ?>">
										<?php echo $this->escape(stripslashes($row->get('title'))); ?>
									</a>
									<?php if (User::get('id') == $row->get('created_by') || User::authorise('core.manage', $this->option)) { ?>
										<a class="edit" href="<?php echo Route::url($row->link('edit')); ?>" title="<?php echo Lang::txt('COM_BLOG_EDIT'); ?>">
											<?php echo Lang::txt('COM_BLOG_EDIT'); ?>
										</a>
										<a class="delete" data-confirm="<?php echo Lang::txt('COM_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('COM_BLOG_DELETE'); ?>">
											<?php echo Lang::txt('COM_BLOG_DELETE'); ?>
										</a>
									<?php } ?>
								</h4>
								<dl class="entry-meta">
									<dt>
										<span>
											<?php echo Lang::txt('COM_BLOG_ENTRY_NUMBER', $row->get('id')); ?>
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
									<?php if ($this->config->get('show_authors')) { ?>
										<dd class="author">
											<?php if ($row->creator()->get('public')) { ?>
												<a href="<?php echo Route::url($row->creator()->getLink()); ?>">
													<?php echo $this->escape(stripslashes($row->get('name'))); ?>
												</a>
											<?php } else { ?>
												<?php echo $this->escape(stripslashes($row->get('name'))); ?>
											<?php } ?>
										</dd>
									<?php } ?>
									<?php if ($row->get('allow_comments') == 1) { ?>
										<dd class="comments">
											<a href="<?php echo Route::url($row->link('comments')); ?>">
												<?php echo Lang::txt('COM_BLOG_NUM_COMMENTS', $row->get('comments', 0)); ?>
											</a>
										</dd>
									<?php } else { ?>
										<dd class="comments">
											<span>
												<?php echo Lang::txt('COM_BLOG_COMMENTS_OFF'); ?>
											</span>
										</dd>
									<?php } ?>
									<?php if (User::get('id') == $row->get('created_by')) { ?>
										<dd class="state <?php echo $row->state('text'); ?>">
											<?php echo Lang::txt('COM_BLOG_STATE_' . strtoupper($row->state('text'))); ?>
										</dd>
									<?php } ?>
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
					$pageNav->setAdditionalUrlParam('year', $this->filters['year']);
					$pageNav->setAdditionalUrlParam('month', $this->filters['month']);
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					echo $pageNav->render();
					?>
				<?php } else { ?>
					<p class="warning"><?php echo Lang::txt('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->

		<aside class="aside">
			<?php if ($this->config->get('access-create-entry')) { ?>
				<p>
					<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
						<?php echo Lang::txt('COM_BLOG_NEW_ENTRY'); ?>
					</a>
				</p>
			<?php } ?>

			<div class="container blog-entries-years">
				<h4><?php echo Lang::txt('COM_BLOG_ENTRIES_BY_YEAR'); ?></h4>
				<ol>
				<?php
				if ($first->exists())
				{
					$start = intval(substr($first->get('publish_up'), 0, 4));
					$now = Date::format("Y");

					for ($i=$now, $n=$start; $i >= $n; $i--)
					{
					?>
						<li>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&year=' . $i); ?>"><?php echo $i; ?></a>
						<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) { ?>
							<ol>
							<?php
							$m = array(
								'COM_BLOG_JANUARY',
								'COM_BLOG_FEBRUARY',
								'COM_BLOG_MARCH',
								'COM_BLOG_APRIL',
								'COM_BLOG_MAY',
								'COM_BLOG_JUNE',
								'COM_BLOG_JULY',
								'COM_BLOG_AUGUST',
								'COM_BLOG_SEPTEMBER',
								'COM_BLOG_OCTOBER',
								'COM_BLOG_NOVEMBER',
								'COM_BLOG_DECEMBER'
							);
							//if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) {
							if ($i == $now)
							{
								$months = Date::format("m");
							}
							else
							{
								$months = 12;
							}

							for ($k=0, $z=$months; $k < $z; $k++)
							{
							?>
								<li>
									<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&year=' . $i . '&month=' . sprintf("%02d", ($k+1), 1)); ?>"><?php echo Lang::txt($m[$k]); ?></a>
								</li>
							<?php
							}
							?>
							</ol>
						<?php } ?>
						</li>
					<?php
					}
				} else { ?>
					<p><?php echo Lang::txt('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
				</ol>
			</div><!-- / .blog-entries-years -->

			<div class="container blog-popular-entries">
				<h4><?php echo Lang::txt('COM_BLOG_POPULAR_ENTRIES'); ?></h4>
				<?php
				$filters['limit'] = 5;
				$popular = $this->model->entries('popular', $filters);
				if ($popular->total()) { ?>
					<ol>
					<?php foreach ($popular as $row) { ?>
						<li>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						</li>
					<?php } ?>
					</ol>
				<?php } else { ?>
					<p><?php echo Lang::txt('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
			</div><!-- / .blog-popular-entries -->
		</aside><!-- / .aside -->
	</form>
</section><!-- / .main section -->
