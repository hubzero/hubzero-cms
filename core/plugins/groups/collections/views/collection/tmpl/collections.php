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

$this->js('jquery.masonry.js', 'com_collections')
     ->js('jquery.infinitescroll.js', 'com_collections')
     ->js();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo Route::url($base); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('group', $this->group)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'collections')
	     ->set('collections', $this->rows->total())
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', ($this->params->get('access-can-follow') ? $this->following : 0))
	     ->display();
	?>

	<?php if (!User::isGuest()) { ?>
		<p class="guest-options">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<a class="icon-config config btn" href="<?php echo Route::url($base . '&scope=settings'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
				</a>
			<?php } ?>

			<?php if ($this->model->isFollowing()) { ?>
				<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&scope=unfollow'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
				</a>
			<?php } else { ?>
				<a class="icon-follow follow btn" data-text-follow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&scope=follow'); ?>">
					<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?></span>
				</a>
			<?php } ?>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div id="posts" data-base="<?php echo rtrim(Request::base(true), '/'); ?>" class="<?php echo (User::isGuest() ? 'loggedout' : 'loggedin'); ?>">
			<?php if (!User::isGuest()) { ?>
				<?php if ($this->params->get('access-create-collection') && !Request::getInt('no_html', 0)) { ?>
					<div class="post new-collection">
						<a class="add" href="<?php echo Route::url($base . '&scope=new'); ?>">
							<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'); ?></span>
						</a>
					</div>
				<?php } ?>
			<?php } ?>
			<?php foreach ($this->rows as $row) { ?>
				<div class="post collection <?php echo ($row->get('access') == 4) ? 'private' : 'public'; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
					<div class="content">
						<?php
							$this->view('default_collection', 'post')
							     ->set('row', $row)
							     ->set('collection', $row)
							     ->display();
						?>
						<?php if ($tags = $row->item()->tags('cloud')) { ?>
							<div class="tags-wrap">
								<?php echo $tags; ?>
							</div>
						<?php } ?>
						<div class="meta">
							<p class="stats">
								<span class="likes">
									<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_POST_LIKES', $row->get('positive', 0)); ?>
								</span>
								<span class="reposts">
									<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_POST_POSTS', $row->get('posts', 0)); ?>
								</span>
							</p>
							<div class="actions">
								<?php if (!User::isGuest()) { ?>
									<?php if ($row->isFollowing()) { ?>
										<a class="btn unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($base . '&scope=' . $row->get('alias') . '/unfollow'); ?>">
											<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?></span>
										</a>
									<?php } else { ?>
										<a class="btn follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($base . '&scope=' . $row->get('alias') . '/follow'); ?>">
											<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
									<?php if ($this->params->get('access-manage-collection')) { ?>
										<?php if ($this->params->get('access-edit-collection')) { ?>
											<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&scope=' . $row->get('alias') . '/edit'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT'); ?>">
												<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_EDIT'); ?></span>
											</a>
										<?php } ?>
										<?php if ($this->params->get('access-delete-collection')) { ?>
											<a class="btn delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&scope=' . $row->get('alias') . '/delete'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE'); ?>">
												<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE'); ?></span>
											</a>
										<?php } ?>
									<?php } else { ?>
											<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&scope=' . $row->get('alias') . '/collect'); ?>">
												<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
											</a>
									<?php } ?>
								<?php } else { ?>
									<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&scope=' . $row->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
									</a>
									<a class="btn follow tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&scope=' . $row->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
										<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							</div><!-- / .actions -->
						</div><!-- / .meta -->
					</div><!-- / .content -->
				</div><!-- / .post -->
			<?php } ?>
		</div><!-- / #posts -->
		<?php
		if ($this->total > $this->filters['limit'])
		{
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'collections');
			$pageNav->setAdditionalUrlParam('scope', 'all');
			echo $pageNav->render();
		}
		?>
		<div class="clear"></div>
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-collection')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP1'); ?></li>
						<li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP2'); ?></li>
						<li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP3'); ?></li>
					</ol>
					<div class="new-collection">
						<a class="add" href="<?php echo Route::url($base . '&scope=new'); ?>">
							<span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'); ?></span>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_TITLE'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_DESC'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NO_COLLECTIONS_FOUND'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
</form>