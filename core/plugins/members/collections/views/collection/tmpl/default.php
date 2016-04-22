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

$base = $this->member->getLink() . '&active=' . $this->name;

if (!$this->collection->get('layout'))
{
	$this->collection->set('layout', 'grid');
}
$viewas = Request::getWord('viewas', $this->collection->get('layout'));
if (!in_array($viewas, array('grid', 'list')))
{
	$viewas = 'grid';
}

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

<form method="get" action="<?php echo Route::url($base . '&task=' . $this->collection->get('alias')); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('params', $this->params)
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('name', $this->name)
	     ->set('active', ($this->collection->exists() ? '' : 'posts'))
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', $this->following)
	     ->display();
	?>

	<?php if ($this->collection->exists()) { ?>
		<p class="overview">
			<span class="title count">
				"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
			</span>
			<span class="posts count">
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $this->total); ?>
			</span>
			<?php if (!User::isGuest()) { ?>
				<?php if (!$this->params->get('access-create-item')) { ?>
					<?php if ($this->collection->isFollowing()) { ?>
						<a class="icon-unfollow unfollow btn tooltips" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_TITLE'); ?>" href="<?php echo Route::url($this->collection->link() . '/unfollow'); ?>">
							<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?></span>
						</a>
					<?php } else { ?>
						<a class="icon-follow follow btn tooltips" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_TITLE'); ?>" href="<?php echo Route::url($this->collection->link() . '/follow'); ?>">
							<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?></span>
						</a>
					<?php } ?>
					<a class="icon-repost repost btn tooltips" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT_TITLE'); ?>" href="<?php echo Route::url($this->collection->link() . '/collect'); ?>">
						<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
			<span class="options sort-options">
				<a href="<?php echo Route::url($this->collection->link() . '&sort=created&viewas=' . $viewas); ?>" class="icon-created<?php if ($this->filters['sort'] == 'created') { echo ' selected'; } ?>" data-view="sort-created" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT'); ?>"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT'); ?></a>
				<a href="<?php echo Route::url($this->collection->link() . '&sort=ordering&viewas=' . $viewas); ?>" class="icon-ordering<?php if ($this->filters['sort'] == 'ordering') { echo ' selected'; } ?>" data-view="sort-ordering" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT'); ?>"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT'); ?></a>
			</span>
			<span class="options view-options">
				<a href="<?php echo Route::url($this->collection->link() . '&sort=' . $this->filters['sort'] . '&viewas=grid'); ?>" class="icon-grid<?php if ($viewas == 'grid') { echo ' selected'; } ?>" data-view="view-grid" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW'); ?>"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW'); ?></a>
				<a href="<?php echo Route::url($this->collection->link() . '&sort=' . $this->filters['sort'] . '&viewas=list'); ?>" class="icon-list<?php if ($viewas == 'list') { echo ' selected'; } ?>" data-view="view-list" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW'); ?>"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW'); ?></a>
			</span>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div id="posts" data-base="<?php echo rtrim(Request::base(true), '/'); ?>" data-update="<?php echo Route::url('index.php?option=com_collections&controller=posts&task=reorder&' . Session::getFormToken() . '=1'); ?>" class="view-<?php echo $viewas . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin'); ?>">
			<?php if ($this->params->get('access-create-collection') && !Request::getInt('no_html', 0)) { ?>
				<div class="post new-post" id="post_0">
					<a class="icon-add add" href="<?php echo Route::url($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
					</a>
				</div>
			<?php } ?>
		<?php
		foreach ($this->rows as $row)
		{
			$item = $row->item();
			?>
			<div class="post <?php echo $item->type(); ?>" id="post_<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&task=post/' . $row->get('id')); ?>">
				<div class="content">
					<?php if (!User::isGuest() && $this->params->get('access-create-item') && $this->filters['sort'] == 'ordering') { ?>
						<div class="sort-handle tooltips" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GRAB_TO_REORDER'); ?>"></div>
					<?php } ?>
					<?php
						$this->view('default_' . $item->type(), 'post')
						     ->set('name', $this->name)
						     ->set('option', $this->option)
						     ->set('member', $this->member)
						     ->set('params', $this->params)
						     ->set('row', $row)
						     ->display();
					?>
					<?php if ($tags = $item->tags('cloud')) { ?>
						<div class="tags-wrap">
							<?php echo $tags; ?>
						</div>
					<?php } ?>
					<div class="meta" data-metadata-url="<?php echo Route::url('index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id')); ?>">
						<p class="stats">
							<span class="likes">
								<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
							</span>
							<span class="comments">
								<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
							</span>
							<span class="reposts">
								<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
							</span>
						</p>
						<div class="actions">
							<?php if (!User::isGuest()) { ?>
								<?php //if ($item->get('created_by') == User::get('id')) { ?>
								<?php if ($row->get('created_by') == User::get('id')) { ?>
									<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/edit'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
									</a>
								<?php } else { ?>
									<a class="btn vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo Lang::txt('Unlike'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/vote'); ?>">
										<span><?php echo ($item->get('voted')) ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE') : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
								<?php } ?>
									<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/collect'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
								<?php if ($row->get('original') && ($item->get('created_by') == User::get('id') || $this->params->get('access-delete-item'))) { ?>
									<a class="btn delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/delete'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
									</a>
								<?php } else if ($row->get('created_by') == User::get('id') || $this->params->get('access-edit-item')) { ?>
									<a class="btn unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/remove'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_REMOVE'); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
									<a class="btn vote like tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=' . $this->collection->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
									<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=' . $this->collection->get('alias'), false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
							<?php } ?>
						</div><!-- / .actions -->
					</div><!-- / .meta -->
					<div class="convo attribution reposted">
						<?php
						$name = $this->escape(stripslashes($row->creator()->get('name')));
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
							<?php
							$who = $name;
							if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels()))
							{
								$who = '<a href="' . Route::url($row->creator()->link()) . '">' . $name . '</a>';
							}

							$where = '<a href="' . Route::url($row->link()) . '">' . $this->escape(stripslashes($row->get('title'))) . '</a>';

							echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where);
							?>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->

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
			$pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
			$pageNav->setAdditionalUrlParam('active', 'collections');
			$pageNav->setAdditionalUrlParam('task', $this->task);
			$pageNav->setAdditionalUrlParam('viewas', $viewas);
			$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
			echo $pageNav->render();
		}
		?>
		<div class="clear"></div>
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-item')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP1'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP3'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP4'); ?></li>
					</ol>
					<div class="new-post">
						<a class="icon-add add" href="<?php echo Route::url($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST_EXPLANATION'); ?><p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EMPTY_COLLECTION'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
</form>