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

$base = $this->member->link() . '&active=' . $this->name;

$this->css()
     ->js('jquery.masonry', 'com_collections')
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo Route::url($base . '&task=all'); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'collections')
	     ->set('collections', $this->rows->total())
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', $this->following)
	     ->display();
	?>

	<?php /*<fieldset class="filters">
		<div class="input-group">
			<span class="input-cell">
				<label for="filter-search">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
			</span>
			<span class="input-cell">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GO'); ?>" />
			</span>
			<?php if (!User::isGuest() && !$this->params->get('access-create-collection')) { ?>
				<span class="input-cell">
					<?php if ($this->model->isFollowing()) { ?>
						<a class="unfollow btn" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&task=unfollow'); ?>">
							<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
						</a>
					<?php } else { ?>
						<a class="follow btn" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&task=follow'); ?>">
							<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?></span>
						</a>
					<?php } ?>
				</span>
			<?php } ?>
		</div>
	</fieldset> */ ?>
	<?php if (!User::isGuest() && !$this->params->get('access-create-collection')) { ?>
		<p class="guest-options">
			<?php if ($this->model->isFollowing()) { ?>
				<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&task=unfollow'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
				</a>
			<?php } else { ?>
				<a class="icon-follow follow btn" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo Route::url($base . '&task=follow'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?></span>
				</a>
			<?php } ?>
		</p>
	<?php } ?>

<?php if ($this->rows->total() > 0) { ?>
	<div id="posts" data-base="<?php echo rtrim(Request::base(true), '/'); ?>" class="<?php echo (User::isGuest() ? 'loggedout' : 'loggedin'); ?>">
	<?php if (!User::isGuest()) { ?>
		<?php if ($this->params->get('access-create-collection') && !Request::getInt('no_html', 0)) { ?>
			<div class="post new-collection">
				<a class="icon-add add" href="<?php echo Route::url($base . '&task=new'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_COLLECTION'); ?></span>
				</a>
			</div>
		<?php } ?>
	<?php } ?>
	<?php foreach ($this->rows as $row) { ?>
		<div class="post collection <?php echo ($row->get('access') == 4) ? 'private' : 'public'; echo ($row->get('is_default')) ? ' default' : ''; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
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
							<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $row->get('positive', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)); ?>
						</span>
					</p>
					<div class="actions">
						<?php if (!User::isGuest()) { ?>
							<?php if ($row->get('object_type') == 'member' && $row->get('object_id') == User::get('id')) { ?>
								<?php if ($this->params->get('access-edit-collection')) { ?>
									<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=' . $row->get('alias') . '/edit'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
									</a>
								<?php } ?>
								<?php if ($this->params->get('access-delete-collection')) { //!$row->get('is_default') && ?>
									<a class="btn delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=' . $row->get('alias') . '/delete'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
									<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=' . $row->get('alias') . '/collect'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
								<?php if ($row->isFollowing()) { ?>
									<a class="btn unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($base . '&task=' . $row->get('alias') . '/unfollow'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?></span>
									</a>
								<?php } else { ?>
									<a class="btn follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($base . '&task=' . $row->get('alias') . '/follow'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							<?php } ?>
						<?php } else { ?>
							<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=' . $row->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
								<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
							</a>
							<a class="btn follow tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=' . $row->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
								<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?></span>
							</a>
						<?php } ?>
					</div><!-- / .actions -->
				</div><!-- / .meta -->
				<?php if ($row->get('object_type') == 'member' && $row->get('object_id') != User::get('id')) { ?>
				<div class="convo attribution clearfix">
					<?php
					$name = $this->escape(stripslashes($row->creator('name')));
					if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
						<a href="<?php echo Route::url($row->creator()->link()); ?>" title="<?php echo $name; ?>" class="img-link">
							<img src="<?php echo $row->creator()->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
						</a>
					<?php } else { ?>
						<span class="img-link">
							<img src="<?php echo $row->creator()->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
						</span>
					<?php } ?>
					<p>
						<?php if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
							<a href="<?php echo Route::url($row->creator()->link()); ?>">
								<?php echo $name; ?>
							</a>
						<?php } else { ?>
							<?php echo $name; ?>
						<?php } ?>
						<br />
						<span class="entry-date">
							<span class="entry-date-at"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_AT'); ?></span>
							<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ON'); ?></span>
							<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
				<?php } ?>
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
		$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'collections');
		$pageNav->setAdditionalUrlParam('task', 'all');
		echo $pageNav->render();
	}
	?>
	<div class="clear"></div>
<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-collection')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP1'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP3'); ?></li>
					</ol>
					<div class="new-collection">
						<a class="icon-add add" href="<?php echo Route::url($base . '&task=new'); ?>">
							<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_COLLECTION'); ?></span>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION_EXPLANATION'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NONE'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
<?php } ?>
</form>