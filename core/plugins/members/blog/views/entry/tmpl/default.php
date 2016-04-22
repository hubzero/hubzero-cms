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

$entry_year  = substr($this->row->get('publish_up'), 0, 4);
$entry_month = substr($this->row->get('publish_up'), 5, 2);

$base = $this->member->getLink() . '&active=blog';

$this->css()
     ->js();
?>

<ul id="page_options">
<?php if (User::get('id') == $this->member->get('uidNumber')) : ?>
	<li>
		<a class="icon-add add btn" href="<?php echo Route::url($base . '&task=new'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY'); ?>
		</a>
	</li>
<?php endif; ?>
	<li>
		<a class="icon-archive archive btn" href="<?php echo Route::url($base); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE'); ?>
		</a>
	</li>
</ul>

<section class="main section entry-container">
	<div class="subject">
		<?php if ($this->getError()) : ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php endif; ?>
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
		<div class="entry<?php echo $cls; ?>">
			<h2 class="entry-title">
				<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>
			</h2>

			<dl class="entry-meta">
				<dt>
					<span>
						<?php echo Lang::txt('PLG_MEMBERS_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
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
						<?php echo Lang::txt('PLG_MEMBERS_BLOG_NUM_COMMENTS', $comments); ?>
					</a>
				</dd>
			<?php } else { ?>
				<dd class="comments">
					<span>
						<?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_OFF'); ?>
					</span>
				</dd>
			<?php } ?>
			<?php if (User::get('id') == $this->row->get('created_by')) { ?>
				<dd class="state">
					<?php echo $this->row->visibility('text'); ?>
				</dd>
				<dd class="entry-options">
					<a class="edit" href="<?php echo Route::url($this->row->link('edit')); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_BLOG_EDIT'); ?>">
						<span><?php echo Lang::txt('PLG_MEMBERS_BLOG_EDIT'); ?></span>
					</a>
					<a class="delete" data-confirm="<?php echo Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->row->link('delete')); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?>">
						<span><?php echo Lang::txt('PLG_MEMBERS_BLOG_DELETE'); ?></span>
					</a>
				</dd>
			<?php } ?>
			</dl>

			<div class="entry-content">
				<?php echo $this->row->content; ?>
				<?php echo $this->row->tags('cloud'); ?>
			</div>
		</div>
	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container blog-popular-entries">
			<h4><?php echo Lang::txt('PLG_MEMBERS_BLOG_POPULAR_ENTRIES'); ?></h4>
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
				<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND'); ?></p>
			<?php } ?>
		</div><!-- / .blog-popular-entries -->
	</aside><!-- /.aside -->
</section>

<?php if ($this->row->get('allow_comments')) { ?>
	<section class="section below">
		<div class="subject">
			<h3>
				<?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_HEADER'); ?>
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

			if ($comments->count() > 0) {
				$this->view('_list', 'comments')
				     ->set('parent', 0)
				     ->set('cls', 'odd')
				     ->set('depth', 0)
				     ->set('option', $this->option)
				     ->set('comments', $comments)
				     ->set('config', $this->config)
				     ->set('base', $this->row->link())
				     ->set('member', $this->member)
				     ->display();
				?>
			<?php } else { ?>
				<p class="no-comments">
					<?php echo Lang::txt('PLG_MEMBERS_BLOG_NO_COMMENTS'); ?>
				</p>
			<?php } ?>

			<h3 id="comments">
				<?php echo Lang::txt('PLG_MEMBERS_BLOG_ADD_A_COMMENT'); ?>
			</h3>
			<form method="post" action="<?php echo Route::url($this->row->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<?php
					$user = Components\Members\Models\Member::oneOrNew(User::get('id'));
					$anon = (User::isGuest() ? 0 : 1);
					?>
					<img src="<?php echo $user->picture($anon); ?>" alt="" />
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
							$name = Lang::txt('PLG_MEMBERS_BLOG_ANONYMOUS');
							if (!$replyto->get('anonymous'))
							{
								$name = $this->escape(stripslashes($replyto->creator->get('name')));
								if (in_array($replyto->creator->get('access'), User::getAuthorisedViewLevels()))
								{
									$name = '<a href="' . Route::url($replyto->creator->link()) . '">' . $name . '</a>';
								}
							}
					?>
					<blockquote cite="c<?php echo $replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo Lang::txt('PLG_MEMBERS_BLOG_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo Lang::txt('PLG_MEMBERS_BLOG_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?></p>
					</blockquote>
					<?php
						}
					?>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_BLOG_FIELD_COMMENTS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<?php
						if (!User::isGuest()) {
							echo $this->editor('comment[content]', '', 40, 15, 'commentcontent', array('class' => 'minimal no-footer'));
						} else {
						?>
						<p class="warning">
							<?php echo Lang::txt('PLG_MEMBERS_BLOG_MUST_LOG_IN', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->row->link() . '#post-comment', false, true))) . '">' . Lang::txt('PLG_MEMBERS_BLOG_LOG_IN') . '</a>'); ?>
						</p>
						<?php } ?>
					</label>

				<?php if (!User::isGuest()) { ?>
					<label id="comment-anonymous-label">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
						<?php echo Lang::txt('PLG_MEMBERS_BLOG_POST_ANONYMOUS'); ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SUBMIT'); ?>" />
					</p>
				<?php } ?>
					<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="action" value="savecomment" />

					<?php echo Html::input('token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
						<p>
							<?php echo Lang::txt('PLG_MEMBERS_BLOG_COMMENT_HELP'); ?>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- /.subject -->
		<aside class="aside aside-below">
			<p>
				<a class="add btn" href="#post-comment">
					<?php echo Lang::txt('PLG_MEMBERS_BLOG_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</aside><!-- / .aside -->
	</section>
<?php } ?>
