<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

<form method="get" action="<?php echo Route::url($base . '&task=' . $this->collection->get('alias')); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('params', $this->params)
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('name', $this->name)
	     ->set('active', 'livefeed')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', $this->following)
	     ->display();
	?>

<?php if ($this->rows->total() > 0) { ?>
	<div id="posts" data-base="<?php echo rtrim(Request::base(true), '/'); ?>">
		<?php
		foreach ($this->rows as $row)
		{
			$item = $row->item();
			?>
			<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&task=post/' . $row->get('id')); ?>">
				<div class="content">
					<?php
						$this->view('default_' . $item->type(), 'post')
						     ->set('name', $this->name)
						     ->set('option', $this->option)
						     ->set('member', $this->member)
						     ->set('params', $this->params)
						     ->set('row', $row)
						     ->set('board', $this->collection)
						     ->display();
					?>
					<?php if (count($item->tags()) > 0) { ?>
						<div class="tags-wrap">
							<?php echo $item->tags('render'); ?>
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
								<?php if ($item->get('created_by') == User::get('id')) { ?>
									<a class="btn edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/edit'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
									</a>
								<?php } else { ?>
									<a class="btn vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/vote'); ?>">
										<span><?php echo ($item->get('voted')) ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE') : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
								<?php } ?>
									<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="btn repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/collect'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
								<?php if ($row->get('original') && $item->get('created_by') == User::get('id')) { ?>
									<a class="btn delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/delete'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
									<a class="btn vote like tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=post/' . $row->get('id') . '/vote', false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
									<a class="btn comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="btn repost tooltips" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($base . '&task=post/' . $row->get('id') . '/collect', false, true)), false); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
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
		$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'collections');
		echo $pageNav->render();
	}
	?>
	<div class="clear"></div>
<?php } else { ?>
	<div id="collection-introduction">
		<?php if ($this->params->get('access-create-item')) { ?>
			<?php if ($this->following <= 0) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1', Route::url('index.php?option=com_collections')); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3'); ?></li>
					</ol>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED_EXPLANATION'); ?><p>
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION'); ?></p>
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS_EXPLANATION'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p>
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_YOU'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
		<?php } else { ?>
			<div class="instructions">
				<?php if ($this->filters['collection_id'][0] == -1) { ?>
					<p>
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_MEMBER_NOT_FOLLOWING'); ?>
					</p>
				<?php } else { ?>
					<p>
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_THIS_MEMBER'); ?>
					</p>
				<?php } ?>
			</div><!-- / .instructions -->
		<?php } ?>
	</div><!-- / #collection-introduction -->
<?php } ?>
</form>
