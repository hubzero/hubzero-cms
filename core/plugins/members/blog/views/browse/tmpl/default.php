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

$base = $this->member->link() . '&active=blog';

$first = $this->archive->entries(array(
		'state'    => 1,
		'scope'    => $this->filters['scope'],
		'scope_id' => $this->filters['scope_id']
	))
	->order('publish_up', 'asc')
	->limit(1)
	->row();

$rows = $this->archive->entries($this->filters)
	->ordered()
	->paginated()
	->rows();

$this->css()
     ->js();
?>

<?php if (User::get('id') == $this->member->get('id')) : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add add btn" href="<?php echo Route::url($base . '&task=new'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY'); ?>
			</a>
		</li>
		<li>
			<a class="icon-config config btn" href="<?php echo Route::url($base . '&task=settings'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS'); ?>
			</a>
		</li>
	</ul>
<?php endif; ?>

<?php if (User::get('id') == $this->member->get('id') && !$this->filters['year'] && !$this->filters['search'] && !$rows->count()) { ?>

	<div class="introduction">
		<div class="introduction-message">
			<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_INTRO_EMPTY'); ?></p>
		</div>
		<div class="introduction-questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_BLOG_INTRO_WHAT_IS_A_BLOG'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_INTRO_WHAT_IS_A_BLOG_EXPLANATION'); ?></p>

			<p><strong><?php echo Lang::txt('PLG_MEMBERS_BLOG_INTRO_HOW_TO_START'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_INTRO_HOW_TO_START_EXPLANATION'); ?></p>
		</div>
	</div><!-- / .introduction -->

<?php } else { ?>

	<form method="get" action="<?php echo Route::url($base); ?>">
		<section class="section">
			<div class="subject">
				<?php if ($this->getError()) : ?>
					<p class="error"><?php echo $this->getError(); ?></p>
				<?php endif; ?>

				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('PLG_MEMBERS_BLOG_SEARCH_LEGEND'); ?></legend>
						<label for="entry-search-field"><?php echo Lang::txt('PLG_MEMBERS_BLOG_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape(utf8_encode(stripslashes($this->filters['search']))); ?>" placeholder="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SEARCH_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- / .container -->

				<div class="container">
					<h3>
						<?php if (isset($this->search) && $this->search) { ?>
							<?php echo Lang::txt('PLG_MEMBERS_BLOG_SEARCH_FOR', $this->escape($this->filters['search'])); ?>
						<?php } else if (!isset($this->filters['year']) || !$this->filters['year']) { ?>
							<?php echo Lang::txt('PLG_MEMBERS_BLOG_LATEST_ENTRIES'); ?>
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
						<?php
						/*if ($this->config->get('feeds_enabled', 1)) {
							$live_site = rtrim(Request::base(),'/');
							$path  = $base . '&task=feed.rss';
							$path .= ($this->filters['year'])  ? '&year=' . $this->filters['year']   : '';
							$path .= ($this->filters['month']) ? '&month=' . $this->filters['month'] : '';
							$feed = Route::url($path);
							if (substr($feed, 0, 4) != 'http')
							{
								$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
							}
							$feed = str_replace('https:://', 'http://', $feed);
						?>
						<a class="feed" href="<?php echo $feed; ?>">
							<?php echo Lang::txt('PLG_MEMBERS_BLOG_RSS_FEED'); ?>
						</a>
						<?php }*/ ?>
					</h3>

				<?php if ($rows->count() > 0) { ?>
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
								</h4>
								<dl class="entry-meta">
									<dt>
										<span>
											<?php echo Lang::txt('PLG_MEMBERS_BLOG_ENTRY_NUMBER', $row->get('id')); ?>
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
										<?php if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) { ?>
											<a href="<?php echo Route::url($row->creator->link()); ?>">
												<?php echo $this->escape(stripslashes($row->creator->get('name'))); ?>
											</a>
										<?php } else { ?>
											<?php echo $this->escape(stripslashes($row->creator->get('name'))); ?>
										<?php } ?>
									</dd>
									<?php if ($row->get('allow_comments') == 1) { ?>
										<dd class="comments">
											<a href="<?php echo Route::url($row->link('comments')); ?>">
												<?php
												$comments = $row->comments()
													->whereIn('state', array(
														Components\Blog\Models\Comment::STATE_PUBLISHED,
														Components\Blog\Models\Comment::STATE_FLAGGED
													))
													->count();
												echo Lang::txt('PLG_MEMBERS_BLOG_NUM_COMMENTS', $comments); ?>
											</a>
										</dd>
									<?php } else { ?>
										<dd class="comments">
											<span>
												<?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_OFF'); ?>
											</span>
										</dd>
									<?php } ?>
									<?php if (User::get('id') == $row->get('created_by')) { ?>
										<dd class="state <?php echo strtolower($row->visibility('text')); ?>">
											<?php echo $row->visibility('text'); ?>
										</dd>
									<?php } ?>
									<dd class="entry-options">
										<?php if (User::get('id') == $row->get('created_by')) { ?>
											<a class="edit" href="<?php echo Route::url($row->link('edit')); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_BLOG_EDIT'); ?>">
												<?php echo Lang::txt('PLG_MEMBERS_BLOG_EDIT'); ?>
											</a>
											<a class="delete" data-confirm="<?php echo Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?>">
												<?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?>
											</a>
										<?php } ?>
									</dd>
								</dl>
								<div class="entry-content">
									<?php if ($this->config->get('cleanintro', 1)) { ?>
										<p>
											<?php echo \Hubzero\Utility\String::truncate(strip_tags($row->content), $this->config->get('introlength', 300)); ?>
										</p>
									<?php } else { ?>
										<?php echo \Hubzero\Utility\String::truncate($row->content, $this->config->get('introlength', 300)); ?>
									<?php } ?>
								</div>
							</article>
						</li>
					<?php } ?>
					</ol>
					<?php
						$pageNav = $rows->pagination;

						$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
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
						echo $pageNav;
					?>
				<?php } else { ?>
					<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
				</div>
			</div><!-- / .subject -->
			<aside class="aside">
				<?php if ($first->get('id')) { ?>
					<div class="container">
						<h4><?php echo Lang::txt('PLG_MEMBERS_BLOG_ENTRIES_BY_YEAR'); ?></h4>
						<ul>
							<?php
								$start = intval(substr($first->get('publish_up'), 0, 4));
								$now = date("Y");
								$m = array(
									'JANUARY',
									'FEBRUARY',
									'MARCH',
									'APRIL',
									'MAY',
									'JUNE',
									'JULY',
									'AUGUST',
									'SEPTEMBER',
									'OCTOBER',
									'NOVEMBER',
									'DECEMBER'
								);
							?>
							<?php for ($i=$now, $n=$start; $i >= $n; $i--) : ?>
								<li>
									<a href="<?php echo Route::url($base . '&task=' . $i); ?>">
										<?php echo $i; ?>
									</a>
									<?php if (($this->filters['year'] && $i == $this->filters['year']) || (!$this->filters['year'] && $i == $now)) : ?>
										<ul>
											<?php $months = ($i == $now) ? date("m") : 12; ?>
											<?php for ($k=0, $z=$months; $k < $z; $k++) : ?>
												<li>
													<a<?php if ($this->filters['month'] && $this->filters['month'] == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo Route::url($base . '&task=' . $i . '/' . sprintf("%02d", ($k+1), 1)); ?>">
														<?php echo Lang::txt($m[$k]); ?>
													</a>
												</li>
											<?php endfor; ?>
										</ul>
									<?php endif; ?>
								</li>
							<?php endfor; ?>
						</ul>
					</div>
				<?php } ?>

				<div class="container blog-popular-entries">
					<h4><?php echo Lang::txt('PLG_MEMBERS_BLOG_POPULAR_ENTRIES'); ?></h4>
					<?php
					$popular = $this->archive->entries(array(
							'state'  => $this->filters['state'],
							'access' => $this->filters['access']
						))
						->order('hits', 'desc')
						->limit(5)
						->rows();
					if ($popular->count()) { ?>
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
						<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND'); ?></p>
					<?php } ?>
				</div><!-- / .blog-popular-entries -->
			</aside><!-- / .aside -->
		</section>
	</form>

<?php } ?>