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

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';

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
<?php if ($this->group->published == 1 && ($this->canpost || $this->authorized == 'manager' || $this->authorized == 'admin')) { ?>
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

<section class="main section entry-container">
	<div class="subject">
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
						<?php echo Lang::txt('PLG_GROUPS_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
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
						<?php echo Lang::txt('PLG_GROUPS_BLOG_NUM_COMMENTS', $comments); ?>
					</a>
				</dd>
			<?php } else { ?>
				<dd class="comments">
					<span>
						<?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
					</span>
				</dd>
			<?php } ?>
			<?php if (User::get('id') == $this->row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
				<dd class="state">
					<?php echo $this->row->visibility('text'); ?>
				</dd>
				<?php if ($this->group->published == 1) { ?>
					<dd class="entry-options">
						<a class="icon-edit edit" href="<?php echo Route::url($this->row->link('edit')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT'); ?>">
							<span><?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT'); ?></span>
						</a>
						<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->row->link('delete')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>">
							<span><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?></span>
						</a>
					</dd>
				<?php } ?>
			<?php } ?>
			</dl>

			<div class="entry-content">
				<?php echo $this->row->content; ?>
				<?php echo $this->row->tags('cloud'); ?>
			</div>

			<?php
			if ($name = $this->row->creator->get('name'))
			{
				$name = $this->escape(stripslashes($name));
				?>
				<div class="entry-author">
					<h3><?php echo Lang::txt('PLG_GROUPS_BLOG_ABOUT_AUTHOR'); ?></h3>
					<p class="entry-author-photo">
						<img src="<?php echo $this->row->creator->picture(); ?>" alt="" />
					</p>
					<div class="entry-author-content">
						<h4>
							<?php if (in_array($this->row->creator->get('access'), User::getAuthorisedViewLevels())) { ?>
								<a href="<?php echo Route::url($this->row->creator->link()); ?>">
									<?php echo $name; ?>
								</a>
							<?php } else { ?>
								<?php echo $name; ?>
							<?php } ?>
						</h4>
						<p class="entry-author-bio">
							<?php if ($this->row->creator->get('bio')) { ?>
								<?php echo $this->row->creator->get('bio'); ?>
							<?php } else { ?>
								<em><?php echo Lang::txt('PLG_GROUPS_BLOG_AUTHOR_BIO_BLANK'); ?></em>
							<?php } ?>
						</p>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container blog-popular-entries">
			<h4><?php echo Lang::txt('PLG_GROUPS_BLOG_POPULAR_ENTRIES'); ?></h4>
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
	</aside><!-- /.aside -->
</section>

<?php if ($this->row->get('allow_comments')) { ?>
	<section class="section below">
		<div class="subject">
			<h3 id="comments" class="below_heading">
				<?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENTS_HEADER'); ?>
			</h3>
			<?php
			$comments = $this->row->comments()
				->including(['creator', function ($creator){
					$creator->select('*');
				}])
				->whereIn('state', array(
					Components\Blog\Models\Comment::STATE_PUBLISHED,
					Components\Blog\Models\Comment::STATE_FLAGGED
				))
				->whereEquals('parent', 0)
				->ordered()
				->rows();
			if ($comments->count() > 0) { ?>
				<?php
					$this->view('_list', 'comments')
					     ->set('group', $this->group)
					     ->set('parent', 0)
					     ->set('cls', 'odd')
					     ->set('depth', 0)
					     ->set('option', $this->option)
					     ->set('comments', $comments)
					     ->set('config', $this->config)
					     ->set('base', $this->row->link())
					     ->display();
				?>
			<?php } else { ?>
				<p class="no-comments">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_NO_COMMENTS'); ?>
				</p>
			<?php } ?>

			<?php if ($this->group->published == 1) { ?>
				<h3 class="below_heading">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_POST_COMMENT'); ?>
				</h3>

				<form method="post" action="<?php echo Route::url($this->row->link()); ?>" id="commentform">
					<p class="comment-member-photo">
						<img src="<?php echo User::picture(User::isGuest() ? 1 : 0); ?>" alt="" />
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

							if ($replyto->get('id'))
							{
								$name = Lang::txt('PLG_GROUPS_BLOG_ANONYMOUS');
								if (!$replyto->get('anonymous'))
								{
									$name = $this->escape(stripslashes($replyto->creator->get('name', $name)));
									if (in_array($replyto->creator->get('access'), User::getAuthorisedViewLevels()))
									{
										$name = '<a href="' . Route::url($replyto->creator->link()) . '">' . $name . '</a>';
									}
								}
						?>
						<blockquote cite="c<?php echo $replyto->get('id'); ?>">
							<p>
								<strong><?php echo $name; ?></strong>
								<span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_BLOG_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
								<span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_BLOG_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
							</p>
							<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?></p>
						</blockquote>
						<?php } ?>

						<?php if (!User::isGuest()) { ?>
							<label for="comment_content">
								Your <?php echo ($replyto->get('id')) ? 'reply' : 'comments'; ?>: <span class="required"><?php echo Lang::txt('PLG_GROUPS_BLOG_REQUIRED'); ?></span>
								<?php echo $this->editor('comment[content]', '', 40, 15, 'comment_content', array('class' => 'minimal no-footer')); ?>
							</label>

							<label id="comment-anonymous-label">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
								<?php echo Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" name="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_SUBMIT'); ?>" />
							</p>
						<?php } else { ?>
							<p class="warning">
								<?php echo Lang::txt('PLG_GROUPS_BLOG_MUST_LOG_IN', '<a href="'. Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->row->link() . '#post-comment', false, true))) . '">' . Lang::txt('PLG_GROUPS_BLOG_LOG_IN') . '</a>'); ?>
							</p>
						<?php } ?>

						<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="comment[state]" value="1" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="active" value="blog" />
						<input type="hidden" name="action" value="savecomment" />

						<?php echo Html::input('token'); ?>

						<div class="sidenote">
							<p>
								<strong><?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
							</p>
							<p>
								<?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENT_HELP'); ?>
							</p>
						</div>
					</fieldset>
				</form>
			<?php } ?>
		</div><!-- / .subject -->
		<aside class="aside">
			<?php if ($this->group->published == 1) { ?>
				<p>
					<a class="icon-add btn" href="#post-comment">
						<?php echo Lang::txt('PLG_GROUPS_BLOG_ADD_A_COMMENT'); ?>
					</a>
				</p>
			<?php } ?>
		</aside><!-- / .aside -->
	</section>
<?php } //end if allow comments ?>

