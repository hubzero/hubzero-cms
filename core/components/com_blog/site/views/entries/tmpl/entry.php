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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt('COM_BLOG'),
		'index.php?option=' . $this->option
	);
}
Pathway::append(
	$this->row->published('Y'),
	'index.php?option=' . $this->option . '&year=' . $this->row->published('Y')
);
Pathway::append(
	$this->row->published('m'),
	'index.php?option=' . $this->option . '&year=' . $this->row->published('Y') . '&month=' . sprintf("%02d", $this->row->published('m'))
);
Pathway::append(
	stripslashes($this->row->get('title')),
	$this->row->link()
);

Document::setTitle(Lang::txt('COM_BLOG') . ': ' . stripslashes($this->row->get('title')));

$this->css()
     ->js();

$first = $this->archive->entries(array(
		'state'      => $this->filters['state'],
		'authorized' => $this->filters['authorized']
	))
	->order('publish_up', 'asc')
	->limit(1)
	->row();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_BLOG'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-archive archive btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=archive'); ?>">
				<?php echo Lang::txt('COM_BLOG_ARCHIVE'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<?php if ($this->row) { ?>
			<?php
			$cls = '';

			if (!$this->row->isAvailable())
			{
				$cls = ' pending';
			}
			if ($this->row->ended())
			{
				$cls = ' expired';
			}
			if ($this->row->get('state') == 0)
			{
				$cls = ' private';
			}
			?>
			<div class="entry<?php echo $cls; ?>" id="e<?php echo $this->row->get('id'); ?>">

				<h2 class="entry-title">
					<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>
				</h2>

				<dl class="entry-meta">
					<dt>
						<span>
							<?php echo Lang::txt('COM_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
						</span>
					</dt>
					<dd class="date">
						<time datetime="<?php echo $this->row->published(); ?>">
							<?php echo $this->row->published('date'); ?>
						</time>
					</dd>
					<dd class="time">
						<time datetime="<?php echo $this->row->published(); ?>">
							<?php echo $this->row->published('time'); ?>
						</time>
					</dd>
				<?php if ($this->row->get('allow_comments')) {
					$comments = $this->row->comments()
						->whereIn('state', array(
							Components\Blog\Models\Comment::STATE_PUBLISHED,
							Components\Blog\Models\Comment::STATE_FLAGGED
						))
						->count();
					?>
					<dd class="comments">
						<a href="<?php echo Route::url($this->row->link('comments')); ?>">
							<?php echo Lang::txt('COM_BLOG_NUM_COMMENTS', $comments); ?>
						</a>
					</dd>
				<?php } else { ?>
					<dd class="comments">
						<span>
							<?php echo Lang::txt('COM_BLOG_COMMENTS_OFF'); ?>
						</span>
					</dd>
				<?php } ?>
				<?php if (User::get('id') == $this->row->get('created_by') || User::authorise('core.manage', $this->option)) { ?>
					<dd class="state <?php echo strtolower($this->row->visibility('text')); ?>">
						<?php echo $this->row->visibility('text'); ?>
					</dd>
					<dd class="entry-options">
						<a class="edit" href="<?php echo Route::url($this->row->link('edit')); ?>" title="<?php echo Lang::txt('COM_BLOG_EDIT'); ?>">
							<span><?php echo Lang::txt('COM_BLOG_EDIT'); ?></span>
						</a>
						<a class="delete" data-confirm="<?php echo Lang::txt('COM_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->row->link('delete')); ?>" title="<?php echo Lang::txt('COM_BLOG_DELETE'); ?>">
							<span><?php echo Lang::txt('COM_BLOG_DELETE'); ?></span>
						</a>
					</dd>
				<?php } ?>
				</dl>

				<div class="entry-content">
					<?php echo $this->row->content(); ?>
					<?php echo $this->row->tags('cloud'); ?>
				</div>

				<?php
				if ($this->config->get('show_authors'))
				{
					if ($name = $this->row->creator()->get('name'))
					{
						$name = $this->escape(stripslashes($name));
						?>
						<div class="entry-author">
							<h3><?php echo Lang::txt('COM_BLOG_AUTHOR_ABOUT'); ?></h3>
							<p class="entry-author-photo">
								<img src="<?php echo $this->row->creator()->getPicture(); ?>" alt="" />
							</p>
							<div class="entry-author-content">
								<h4>
									<?php if ($this->row->creator()->get('public')) { ?>
										<a href="<?php echo Route::url($this->row->creator()->getLink()); ?>">
											<?php echo $name; ?>
										</a>
									<?php } else { ?>
										<?php echo $name; ?>
									<?php } ?>
								</h4>
								<div class="entry-author-bio">
								<?php if ($this->row->creator()->get('bio')) { ?>
									<?php echo $this->row->creator()->getBio('parsed', 300); ?>
								<?php } else { ?>
									<em><?php echo Lang::txt('COM_BLOG_AUTHOR_NO_BIO'); ?></em>
								<?php } ?>
								</div>
								<div class="clearfix"></div>
							</div><!-- / .entry-author-content -->
						</div><!-- / .entry-author -->
						<?php
					}
				}
				?>
			</div><!-- / .entry -->
		<?php } ?>
		</div><!-- / .subject -->

		<aside class="aside hide6">
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
				if ($first->get('id'))
				{
					$entry_year  = substr($this->row->get('publish_up'), 0, 4);
					$entry_month = substr($this->row->get('publish_up'), 5, 2);

					$start = intval(substr($first->get('publish_up'), 0, 4));
					$now = Date::of('now')->format("Y");
					//$mon = date("m");
					for ($i=$now, $n=$start; $i >= $n; $i--)
					{
						?>
						<li>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&year=' . $i); ?>">
								<?php echo $i; ?>
							</a>
							<?php if ($i == $entry_year) { ?>
								<ol>
									<?php
									$months = array(
										'01' => Lang::txt('COM_BLOG_JANUARY'),
										'02' => Lang::txt('COM_BLOG_FEBRUARY'),
										'03' => Lang::txt('COM_BLOG_MARCH'),
										'04' => Lang::txt('COM_BLOG_APRIL'),
										'05' => Lang::txt('COM_BLOG_MAY'),
										'06' => Lang::txt('COM_BLOG_JUNE'),
										'07' => Lang::txt('COM_BLOG_JULY'),
										'08' => Lang::txt('COM_BLOG_AUGUST'),
										'09' => Lang::txt('COM_BLOG_SEPTEMBER'),
										'10' => Lang::txt('COM_BLOG_OCTOBER'),
										'11' => Lang::txt('COM_BLOG_NOVEMBER'),
										'12' => Lang::txt('COM_BLOG_DECEMBER')
									);
									foreach ($months as $key => $month)
									{
										if (intval($key) <= $entry_month)
										{
										?>
										<li>
											<a <?php if ($entry_month == $key) { echo 'class="active" '; } ?>href="<?php echo Route::url('index.php?option=' . $this->option . '&year=' . $i . '&month=' . $key); ?>">
												<?php echo $month; ?>
											</a>
										</li>
										<?php
										}
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
				$popular = $this->archive->entries()
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
					<p><?php echo Lang::txt('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
				<?php } ?>
			</div><!-- / .blog-popular-entries -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->

<?php if ($this->row->get('allow_comments')) { ?>
<section class="below section">
	<div class="section-inner">
		<div class="subject">
			<h3 id="comments">
				<?php echo Lang::txt('COM_BLOG_COMMENTS_HEADER'); ?>
			</h3>

		<?php
		$comments = $this->row->comments()
			->whereIn('state', array(
				Components\Blog\Models\Comment::STATE_PUBLISHED,
				Components\Blog\Models\Comment::STATE_FLAGGED
			))
			->whereEquals('parent', 0)
			->ordered()
			->rows();

		if ($comments->count() > 0) { ?>
			<?php
				$this->view('_list')
					 ->set('parent', 0)
					 ->set('option', $this->option)
					 ->set('comments', $comments)
					 ->set('config', $this->config)
					 ->set('depth', 0)
					 ->set('cls', 'odd')
					 ->set('base', $this->row->link())
					 ->display();
			?>
		<?php } else { ?>
			<p class="no-comments">
				<?php echo Lang::txt('COM_BLOG_NO_COMMENTS'); ?>
			</p>
		<?php } ?>

			<h3>
				<?php echo Lang::txt('COM_BLOG_POST_COMMENT'); ?>
			</h3>

			<form method="post" action="<?php echo Route::url($this->row->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<?php
					$jxuser = new \Hubzero\User\Profile;
					if (!User::isGuest()) {
						$jxuser = \Hubzero\User\Profile::getInstance(User::get('id'));
						$anonymous = 0;
					} else {
						$anonymous = 1;
					}
					?>
					<img src="<?php echo $jxuser->getPicture($anonymous); ?>" alt="" />
				</p>
				<fieldset>
				<?php
				$replyto = $this->row->comments()
					->whereEquals('id', Request::getInt('reply', 0))
					->whereIn('state', array(
						Components\Blog\Models\Comment::STATE_PUBLISHED,
						Components\Blog\Models\Comment::STATE_FLAGGED
					))
					->row();

				if (!User::isGuest())
				{
					if ($replyto->get('id'))
					{
						$name = Lang::txt('COM_BLOG_ANONYMOUS');
						if (!$replyto->get('anonymous'))
						{
							$name = $this->escape(stripslashes($replyto->creator()->get('name', $name)));
							if ($replyto->creator()->get('public'))
							{
								$name = '<a href="' . Route::url($replyto->creator()->getLink()) . '">' . $name . '</a>';
							}
						}
					?>
					<blockquote cite="c<?php echo $replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo Lang::txt('COM_BLOG_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo Lang::txt('COM_BLOG_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p>
							<?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?>
						</p>
					</blockquote>
					<?php
					}
				}
				?>
					<?php if (!User::isGuest()) { ?>
					<label for="commentcontent">
						Your <?php echo ($replyto->get('id')) ? 'reply' : 'comments'; ?>:
						<?php
							echo $this->editor('comment[content]', '', 40, 15, 'commentcontent', array('class' => 'minimal no-footer'));
						?>
					</label>
					<?php } else { ?>
					<input type="hidden" name="comment[content]" id="commentcontent" value="" />

					<p class="warning">
						<?php echo Lang::txt('COM_BLOG_MUST_LOG_IN', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->row->link() . '#post-comment', false, true))) . '">' . Lang::txt('COM_BLOG_LOG_IN') . '</a>'); ?>
					</p>
					<?php } ?>

				<?php if (!User::isGuest()) { ?>
					<label id="comment-anonymous-label">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
						<?php echo Lang::txt('COM_BLOG_POST_ANONYMOUS'); ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo Lang::txt('COM_BLOG_SUBMIT'); ?>" />
					</p>
				<?php } ?>
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savecomment" />

					<?php echo Html::input('token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo Lang::txt('COM_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container blog-entries-years">
				<h4><?php echo Lang::txt('COM_BLOG_COMMENTS_FEED'); ?></h4>
				<p>
					<?php echo Lang::txt('COM_BLOG_COMMENTS_FEED_EXPLANATION'); ?>
				</p>
				<p>
					<?php
						$feed = Route::url($this->row->link() . '/comments.rss');
						if (substr($feed, 0, 4) != 'http')
						{
							$live_site = rtrim(Request::base(), '/');

							$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
						}
						$feed = str_replace('https:://', 'http://', $feed);
					?>
					<a class="icon-feed feed btn" href="<?php echo $feed; ?>"><?php echo Lang::txt('COM_BLOG_FEED'); ?></a>
				</p>
			</div>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .below section -->
<?php } ?>
