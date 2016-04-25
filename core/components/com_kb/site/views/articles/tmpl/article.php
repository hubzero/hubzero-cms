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
Pathway::append(
	$this->article->get('title'),
	$this->article->link()
);

Document::setTitle(Lang::txt('COM_KB') . ': ' . $this->category->get('title') . ': ' . $this->article->get('title'));
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_KB'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-main main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_KB_MAIN'); ?></a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
			<?php } ?>
			<article class="container" id="entry-<?php echo $this->article->get('id'); ?>">
				<div class="container-block">
					<h3><?php echo $this->escape(stripslashes($this->article->get('title'))); ?></h3>
					<div class="entry-content">
						<?php echo stripslashes($this->article->fulltxt()); ?>
					</div>
					<?php if ($tags = $this->article->tags('cloud')) { ?>
						<div class="entry-tags">
							<p><?php echo Lang::txt('COM_KB_TAGS'); ?></p>
							<?php echo $tags; ?>
						</div><!-- / .entry-tags -->
					<?php } ?>

					<p class="entry-voting voting">
						<?php
							$this->view('_vote')
								 ->set('option', $this->option)
								 ->set('item', $this->article)
								 ->set('type', 'entry')
								 ->set('vote', $this->vote)
								 ->set('id', 0) //$this->article->get('id');
								 ->display();
						?>
					</p>

					<p class="entry-details">
						<?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?>
						<span class="entry-date-at"><?php echo Lang::txt('COM_KB_DATETIME_AT'); ?></span>
						<span class="entry-time"><time datetime="<?php echo $this->article->modified(); ?>"><?php echo $this->article->modified('time'); ?></time></span>
						<span class="entry-date-on"><?php echo Lang::txt('COM_KB_DATETIME_ON'); ?></span>
						<span class="entry-date"><time datetime="<?php echo $this->article->modified(); ?>"><?php echo $this->article->modified('date'); ?></time></span>
					</p>
				</div><!-- / .container-block -->
			</article><!-- / .container -->
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
				<ul class="categories">
					<li>
						<a<?php if ($this->catid <= 0) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&section=all'); ?>">
							<?php echo Lang::txt('COM_KB_ALL_ARTICLES'); ?>
						</a>
					</li>
					<?php
					$filters = array('state' => Components\Kb\Models\Category::STATE_PUBLISHED, 'access' => User::getAuthorisedViewLevels());

					$categories = $this->archive->categories($filters);

					foreach ($categories as $row) { ?>
						<li>
							<a <?php if ($this->catid == $row->get('id')) { echo 'class="active" '; } ?> href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?> <span class="item-count"><?php echo $row->get('articles', 0); ?></span>
							</a>
							<?php if ($this->catid == $row->get('id') && count($row->children($filters)) > 0) { ?>
								<ul class="categories">
								<?php foreach ($row->children() as $cat) { ?>
									<li>
										<a <?php if ($this->category->get('id') == $cat->get('id')) { echo 'class="active" '; } ?> href="<?php echo Route::url($cat->link()); ?>">
											<?php echo $this->escape(stripslashes($cat->get('title'))); ?> <span class="item-count"><?php echo $cat->get('articles', 0); ?></span>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } ?>
						</li>
					<?php } ?>
				</ul>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->

<?php if ($this->article->param('allow_comments')) { ?>
<section class="below section" id="comments">
	<div class="section-inner">
		<div class="subject">
			<h3 class="comments-title">
				<?php echo Lang::txt('COM_KB_COMMENTS_ON_ENTRY'); ?>
				<?php /*if ($this->article->param('feeds_enabled') && $this->article->comments($filters)->count() > 0) { ?>
					<a class="icon-feed feed btn" href="<?php echo $this->article->link('feed'); ?>" title="<?php echo Lang::txt('COM_KB_COMMENT_FEED'); ?>">
						<?php echo Lang::txt('COM_KB_FEED'); ?>
					</a>
				<?php }*/ ?>
			</h3>

			<?php
			$comments = $this->article->comments()
				->whereIn('state', array(
					Components\Kb\Models\Comment::STATE_PUBLISHED,
					Components\Kb\Models\Comment::STATE_FLAGGED
				))
				->rows();

			if ($comments->count() > 0)
			{
				$this->view('_list')
					 ->set('parent', 0)
					 ->set('cls', 'odd')
					 ->set('depth', 0)
					 ->set('option', $this->option)
					 ->set('article', $this->article)
					 ->set('comments', $comments)
					 ->set('base', $this->article->link())
					 ->display();
			}
			else
			{
			?>
			<p class="no-comments">
				<?php echo Lang::txt('COM_KB_NO_COMMENTS'); ?>
			</p>
			<?php } ?>

			<h3 class="post-comment-title">
				<?php echo Lang::txt('COM_KB_POST_COMMENT'); ?>
			</h3>
			<form method="post" action="<?php echo Route::url($this->article->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<img src="<?php
					$user = Components\Members\Models\Member::oneOrNew(User::getRoot());
					echo $user->picture(!User::isGuest() ? 0 : 1); ?>" alt="" />
				</p>
				<fieldset>
					<?php
					$replyto = \Components\Kb\Models\Comment::oneOrNew(Request::getInt('replyto'));

					if (!User::isGuest())
					{
						if (!$replyto->isNew())
						{
							$name = Lang::txt('COM_KB_ANONYMOUS');
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
									<span class="comment-date-at"><?php echo Lang::txt('COM_KB_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $replyto->created(); ?>"><?php echo $replyto->created('time'); ?></time></span>
									<span class="comment-date-on"><?php echo Lang::txt('COM_KB_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $replyto->created(); ?>"><?php echo $replyto->created('date'); ?></time></span>
								</p>
								<p>
									<?php echo $replyto->content('raw', 300); ?>
								</p>
							</blockquote>
							<?php
						}
					}
					?>

					<?php if ($this->article->commentsOpen()) { ?>
						<label for="commentcontent">
							<?php echo Lang::txt('COM_KB_YOUR_COMMENTS'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
							<?php
							if (!User::isGuest()) {
								echo $this->editor('comment[content]', '', 40, 15, 'commentcontent', array('class' => 'minimal'));
							} else {
								$rtrn = Route::url($this->article->link() . '#post-comment', false, true);
								?>
								<p class="warning">
									<?php echo Lang::txt('COM_KB_MUST_LOG_IN', Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn), false)); ?>
								</p>
								<?php
							}
							?>
						</label>

						<?php if (!User::isGuest()) { ?>
							<label id="comment-anonymous-label" for="comment-anonymous">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
								<?php echo Lang::txt('COM_KB_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" name="submit" value="<?php echo Lang::txt('COM_KB_SUBMIT'); ?>" />
							</p>
						<?php } ?>
					<?php } else { ?>
						<p class="warning">
							<?php echo Lang::txt('COM_KB_COMMENTS_CLOSED'); ?>
						</p>
					<?php } ?>

					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->escape($this->article->get('id')); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->escape($replyto->get('id')); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape(User::get('id')); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savecomment" />

					<?php echo Html::input('token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo Lang::txt('COM_KB_COMMENT_KEEP_RELEVANT'); ?></strong>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->
		<aside class="aside">

		</aside>
	</div><!-- / .section-inner -->
</section><!-- / .below -->

<?php } // if ($this->config->get('allow_comments')) ?>
