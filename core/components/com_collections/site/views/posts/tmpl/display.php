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

$item = $this->post->item();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
$no_html = Request::getInt('no_html', 0);

if (!$no_html) {
	$this->css()
	     ->js('post.js');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>
<?php } ?>

<section class="section">
	<div class="grid">
		<div class="col span8">

			<div class="post full <?php echo $item->type(); ?>" id="p<?php echo $this->post->get('id'); ?>" data-id="<?php echo $this->post->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&post=' . $this->post->get('id') . '&task=comment'); ?>" data-width="600" data-height="350">
				<div class="content">
					<div class="creator attribution cf">
						<?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
							<?php
							$name = $this->escape(stripslashes($item->creator()->get('name')));

							if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
								<a href="<?php echo Route::url($item->creator()->link()); ?>" title="<?php echo $name; ?>" class="img-link">
									<img src="<?php echo $item->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</a>
							<?php } else { ?>
								<span class="img-link">
									<img src="<?php echo $item->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</span>
							<?php } ?>
							<p>
								<?php echo Lang::txt('COM_COLLECTIONS_USER_CREATED_POST', (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels()) ? '<a href="' . Route::url($item->creator()->link()) . '">' : '') . $this->escape(stripslashes($item->creator()->get('name'))) . (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels()) ? '</a>' : '')); ?>
								<br />
								<span class="entry-date">
									<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('time'); ?></time></span>
									<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('date'); ?></time></span>
								</span>
							</p>
						<?php } else { ?>
							<p class="typeof <?php echo $item->get('type'); ?>">
								<?php echo $this->escape($item->type('title')); ?>
							</p>
						<?php } ?>
					</div><!-- / .attribution -->
					<?php
					$this->view('display_' . $item->type(), 'posts')
					     ->set('actual', true)
					     ->set('option', $this->option)
					     ->set('params', $this->config)
					     ->set('row', $this->post)
					     ->display();
					?>
					<?php if (count($item->tags()) > 0) { ?>
						<div class="tags-wrap">
							<p><?php echo $item->tags('render'); ?></p>
						</div><!-- / .tags-wrap -->
					<?php } ?>
					<div class="meta" data-metadata-url="<?php echo Route::url($base . '&task=metadata&post=' . $this->post->get('id')); ?>">
						<p class="stats">
							<span class="likes">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
							</span>
							<span class="comments">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
							</span>
							<span class="reposts">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
							</span>
						</p>
					</div><!-- / .meta -->
					<div class="convo attribution">
						<?php
						$name = $this->escape(stripslashes($this->post->creator()->get('name')));

						if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
							<a href="<?php echo Route::url($this->post->creator()->link()); ?>" title="<?php echo $name; ?>" class="img-link">
								<img src="<?php echo $this->post->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</a>
						<?php } else { ?>
							<span class="img-link">
								<img src="<?php echo $this->post->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</span>
						<?php } ?>
						<p>
							<?php
							$who = $name;
							if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels()))
							{
								$who = '<a href="' . Route::url($this->post->creator()->link() . '&active=collections') . '">' . $name . '</a>';
							}

							$where = '<a href="' . Route::url($base . '&task=' . $this->collection->get('alias')) . '">' . $this->escape(stripslashes($this->collection->get('title'))) . '</a>';

							echo Lang::txt('COM_COLLECTIONS_ONTO', $who, $where);
							?>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->
				</div><!-- / .content -->
			</div><!-- / .post -->

			<div class="post-comments">
				<?php if ($item->get('comments')) { ?>
					<ol class="comments">
					<?php
					foreach ($item->comments() as $comment)
					{
						$cuser = Components\Members\Models\Member::oneOrNew($comment->created_by);
						$cname = $this->escape(stripslashes($cuser->get('name')));
					?>
						<li class="comment" id="c<?php echo $comment->id; ?>">
							<p class="comment-member-photo">
								<img src="<?php echo $cuser->picture($comment->anonymous); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $cname); ?>" />
							</p>
							<div class="comment-content">
								<p class="comment-title">
									<strong>
										<?php if (in_array($cuser->get('access'), User::getAuthorisedViewLevels())) { ?>
											<a href="<?php echo Route::url($cuser->link()); ?>">
												<?php echo $cname; ?>
											</a>
										<?php } else { ?>
											<?php echo $cname; ?>
										<?php } ?>
									</strong>
									<a class="permalink" href="#c">
										<span class="entry-date">
											<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
											<span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo Date::of($comment->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
											<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
											<span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo Date::of($comment->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
										</span>
									</a>
								</p>
								<div class="comment-body">
									<p><?php echo stripslashes($comment->content); ?></p>
								</div>
							</div>
						</li>
					<?php } ?>
					</ol>
				<?php } ?>
				<?php if (!User::isGuest()) { ?>
					<form action="<?php echo Route::url($base . '&post=' . $this->post->get('id') . '&task=savecomment' . ($this->no_html ? '&no_html=' . $this->no_html  : '')); ?>" method="post" id="commentform" enctype="multipart/form-data">
						<p class="comment-member-photo">
							<img src="<?php echo User::picture(0); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes(User::get('name')))); ?>" />
						</p>

						<fieldset>
							<p class="comment-title">
								<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>">
									<?php echo $this->escape(stripslashes(User::get('name'))); ?>
								</a>
								<span class="permalink">
									<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><time datetime="<?php echo Date::toSql(); ?>"><?php echo Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
									<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo Date::toSql(); ?>"><?php echo Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
								</span>
							</p>

							<label for="comment-content">
								<span class="label-text"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_COMMENTS'); ?></span>
								<?php echo $this->editor('comment[content]', '', 35, 5, 'comment-content', array('class' => 'minimal no-footer')); ?>
							</label>

							<input type="hidden" name="comment[id]" value="0" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
							<input type="hidden" name="comment[item_type]" value="collection" />
							<input type="hidden" name="comment[state]" value="1" />

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="post" value="<?php echo $this->post->get('id'); ?>" />
							<input type="hidden" name="task" value="savecomment" />
							<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

							<?php echo Html::input('token'); ?>

							<label for="comment-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
								<?php echo Lang::txt('COM_COLLECTIONS_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('COM_COLLECTIONS_SAVE'); ?>" />
							</p>
						</fieldset>
					</form>
				<?php } ?>
			</div>

		</div>
		<div class="col span4 omega">
			<div class="post full collection" id="b<?php echo $this->collection->get('id'); ?>" data-id="<?php echo $this->collection->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&controller=posts&collection=' . $this->collection->get('id')); ?>">
				<div class="content">
					<?php
						$this->view('display_collection', 'posts')
						     ->set('option', $this->option)
						     ->set('params', $this->config)
						     ->set('row', $this->collection)
						     ->display();
					?>
					<?php if ($tags = $this->collection->item()->tags('cloud')) { ?>
							<div class="tags-wrap">
								<?php echo $tags; ?>
							</div>
						<?php } ?>
					<div class="meta">
						<p class="stats">
							<span class="likes">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $this->collection->get('positive', 0)); ?>
							</span>
							<?php /*<span class="reposts">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $this->collection->get('reposts', 0)); ?>
							</span> */ ?>
							<span class="posts">
								<?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $this->collection->count('post')); ?>
							</span>
						</p>
						<?php if (!$no_html) { ?>
						<div class="actions">
							<?php if (!User::isGuest()) { ?>
								<?php if ($this->collection->get('object_type') == 'member' && $this->collection->get('object_id') == User::get('id')) { ?>
										<a class="btn edit" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo Route::url($this->collection->link() . '/edit'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_EDIT'); ?></span>
										</a>
										<a class="btn delete" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo Route::url($this->collection->link() . '/delete'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_DELETE'); ?></span>
										</a>
								<?php } else { ?>
										<a class="btn repost" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&board=' . $this->collection->get('id') . '&task=collect'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
										</a>
									<?php if ($this->collection->isFollowing()) { ?>
										<a class="btn unfollow" data-id="<?php echo $this->collection->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($this->collection->link() . '/unfollow'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></span>
										</a>
									<?php } else { ?>
										<a class="btn follow" data-id="<?php echo $this->collection->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($this->collection->link() . '/follow'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
								<?php } ?>
							<?php } else { ?>
								<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&controller=posts&board=' . $this->collection->get('id') . '&task=collect', false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
									<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
								</a>
								<a class="btn follow tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->collection->link() . '/follow')), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
									<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
								</a>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
					<div class="convo attribution">
						<?php
						$name = $this->escape(stripslashes($this->collection->creator('name')));

						if (in_array($this->collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
							<a href="<?php echo Route::url($this->collection->creator()->link()); ?>" title="<?php echo $name; ?>" class="img-link">
								<img src="<?php echo $this->collection->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</a>
						<?php } else { ?>
							<span class="img-link">
								<img src="<?php echo $this->collection->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</span>
						<?php } ?>
						<p>
							<?php if (in_array($this->collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
								<a href="<?php echo Route::url($this->collection->creator()->link()); ?>">
									<?php echo $name; ?>
								</a>
							<?php } else { ?>
								<?php echo $name; ?>
							<?php } ?>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->collection->created(); ?>"><?php echo $this->collection->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->collection->created(); ?>"><?php echo $this->collection->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->
				</div><!-- / .content -->
			</div><!-- / .post -->
		</div>
	</div>
</section>

<?php if ($item->collections('list', array('collection_id' => $this->collection->get('id')))->total()) { ?>
	<section class="section post-collections">
		<h3><?php echo Lang::txt('COM_COLLECTIONS_ALSO_IN_THESE_COLLECTIONS'); ?></h3>
		<div id="posts">
			<?php foreach ($item->collections() as $collection) { ?>
				<div class="post collection" id="b<?php echo $collection->get('id'); ?>" data-id="<?php echo $collection->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&controller=collection&id=' . $collection->get('id')); ?>" data-width="600" data-height="350">
					<div class="content">
						<?php
						$this->view('display_collection', 'posts')
						     ->set('option', $this->option)
						     ->set('params', $this->config)
						     ->set('row', $collection)
						     ->display();
						?>
						<?php if ($tags = $collection->item()->tags('cloud')) { ?>
							<div class="tags-wrap">
								<?php echo $tags; ?>
							</div>
						<?php } ?>
						<div class="meta">
							<p class="stats">
								<span class="likes">
									<?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $collection->get('positive', 0)); ?>
								</span>
								<?php /*<span class="reposts">
									<?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $collection->count('reposts')); ?>
								</span>*/ ?>
								<span class="posts">
									<?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $collection->count('posts')); ?>
								</span>
							</p>
							<?php if (!$no_html) { ?>
							<div class="actions">
								<?php if (!User::isGuest()) { ?>
									<?php if ($collection->get('object_type') == 'member' && $collection->get('object_id') == User::get('id')) { ?>
											<a class="btn edit" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo Route::url($collection->link() . '/edit'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_EDIT'); ?></span>
											</a>
											<a class="btn delete" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo Route::url($collection->link() . '/delete'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_DELETE'); ?></span>
											</a>
									<?php } else { ?>
											<a class="btn repost" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&board=' . $collection->get('id') . '&task=collect'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
											</a>
										<?php if ($collection->isFollowing()) { ?>
											<a class="btn unfollow" data-id="<?php echo $collection->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($collection->link() . '/unfollow'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></span>
											</a>
										<?php } else { ?>
											<a class="btn follow" data-id="<?php echo $collection->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($collection->link() . '/follow'); ?>">
												<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
											</a>
										<?php } ?>
									<?php } ?>
								<?php } else { ?>
									<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&controller=posts&board=' . $collection->get('id') . '&task=collect', false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
									</a>
									<a class="btn follow tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($collection->link() . '/follow')), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
										<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							</div><!-- / .actions -->
							<?php } ?>
						</div><!-- / .meta -->
						<div class="convo attribution">
							<?php
							$name = $this->escape(stripslashes($collection->creator()->get('name')));

							if (in_array($collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
								<a href="<?php echo Route::url($collection->creator()->link() . '&active=collections'); ?>" title="<?php echo $name; ?>" class="img-link">
									<img src="<?php echo $collection->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</a>
							<?php } else { ?>
								<span class="img-link">
									<img src="<?php echo $collection->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</span>
							<?php } ?>
							<p>
								<?php if (in_array($collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
									<a href="<?php echo Route::url($collection->creator()->link() . '&active=collections'); ?>">
										<?php echo $name; ?>
									</a>
								<?php } else { ?>
									<?php echo $name; ?>
								<?php } ?>
								<br />
								<span class="entry-date">
									<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><?php echo Date::of($collection->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></span>
									<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><?php echo Date::of($collection->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
								</span>
							</p>
						</div><!-- / .attribution -->
					</div>
				</div>
			<?php } ?>
		</div>
	</section>
<?php } ?>