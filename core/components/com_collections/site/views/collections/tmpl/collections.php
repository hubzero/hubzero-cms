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

$base  = 'index.php?option=' . $this->option;
$mode  = Request::getWord('mode', 'grid');

$this->css()
     ->js('jquery.masonry')
     ->js('jquery.infinitescroll')
     ->js();
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

<form method="get" action="<?php echo Route::url($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<?php
	$this->view('_submenu')
	     ->set('option', $this->option)
	     ->set('active', 'collections')
	     ->set('collections', $this->total)
	     ->set('posts', $this->posts)
	     ->display();
	?>

	<section class="section filters">
		<field class="input-group">
			<span class="input-cell">
				<label for="filter-search">
					<span><?php echo Lang::txt('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
			</span>
			<span class="input-cell">
				<input type="submit" class="btn" value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
			</span>
		</fieldset>
	</section>

	<section class="main section">
		<?php if ($this->rows->total() > 0) { ?>
			<div id="posts" data-base="<?php echo Request::base(true); ?>" class="view-as <?php echo $mode . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin'); ?>">
				<?php if (!User::isGuest() && !Request::getInt('no_html', 0)) { ?>
					<div class="post new-post">
						<a class="icon-add add" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=collections&task=new'); ?>">
							<?php echo Lang::txt('COM_COLLECTIONS_NEW_COLLECTION'); ?>
						</a>
					</div>
				<?php } ?>
				<?php foreach ($this->rows as $row) { ?>
					<div class="post collection" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&controller=collection&id=' . $row->get('id')); ?>">
						<div class="content">
							<?php
								$this->view('display_collection', 'posts')
								     ->set('option', $this->option)
								     ->set('params', $this->config)
								     ->set('row', $row)
								     ->display();
							?>
							<div class="meta">
								<p class="stats">
									<span class="likes">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $row->count('likes')); ?>
									</span>
									<?php /*<span class="reposts">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $row->count('reposts')); ?>
									</span>*/ ?>
									<span class="posts">
										<?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $row->count('posts')); ?>
									</span>
								</p>
								<div class="actions">
									<?php if (!User::isGuest()) { ?>
										<?php if ($row->get('object_type') == 'member' && $row->get('object_id') == User::get('id')) { ?>
												<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($row->link() . '/edit'); ?>">
													<span><?php echo Lang::txt('COM_COLLECTIONS_EDIT'); ?></span>
												</a>
												<a class="btn delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($row->link() . '/delete'); ?>">
													<span><?php echo Lang::txt('COM_COLLECTIONS_DELETE'); ?></span>
												</a>
										<?php } else { ?>
												<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&controller=posts&board=' . $row->get('id') . '&task=collect'); ?>">
													<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
												</a>
											<?php if ($row->isFollowing()) { ?>
												<a class="btn unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($row->link() . '/unfollow'); ?>">
													<span><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></span>
												</a>
											<?php } else { ?>
												<a class="btn follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($row->link() . '/follow'); ?>">
													<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
												</a>
											<?php } ?>
										<?php } ?>
									<?php } else { ?>
										<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($row->link(), false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
										</a>
										<a class="btn follow tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($row->link(), false, true)), false); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
											<span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
								</div><!-- / .actions -->
							</div><!-- / .meta -->
							<div class="convo attribution">
								<?php
								$name = $this->escape(stripslashes($row->creator('name')));

								if ($row->creator('public')) { ?>
									<a href="<?php echo Route::url($row->creator()->getLink() . '&active=collections'); ?>" title="<?php echo $name; ?>" class="img-link">
										<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
									</a>
								<?php } else { ?>
									<span class="img-link">
										<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
									</span>
								<?php } ?>
								<p>
									<?php if ($row->creator('public')) { ?>
										<a href="<?php echo Route::url($row->creator()->getLink() . '&active=collections'); ?>">
											<?php echo $name; ?>
										</a>
									<?php } else { ?>
										<?php echo $name; ?>
									<?php } ?>
									<br />
									<span class="entry-date">
										<span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
										<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
										<span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
										<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
									</span>
								</p>
							</div><!-- / .attribution -->
						</div><!-- / .content -->
					</div><!-- / .post -->
				<?php } ?>
			</div>
			<?php
			if ($this->total > $this->filters['limit'])
			{
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
			}
			?>
			<div class="clear"></div>
		<?php } else { ?>
			<div id="collection-introduction">
				<?php if ($this->config->get('access-create-post')) { ?>
					<div class="instructions">
						<ol>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP1'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP2'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP3'); ?></li>
							<li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP4'); ?></li>
						</ol>
					</ul>
				<?php } else { ?>
					<div class="instructions">
						<p><?php echo Lang::txt('COM_COLLECTIONS_NO_COLLECTIONS_FOUND'); ?></p>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</section>
</form>