<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

$this->css()
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo Route::url($base . '&task=following'); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'following')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', $this->rows->total())
	     ->display();
	?>

	<?php if ($this->rows->total() > 0) { ?>
		<div class="container">
			<table class="following entries">
				<tbody>
					<?php foreach ($this->rows as $row) { ?>
						<tr class="<?php echo $row->get('following_type'); ?>">
							<th>
								<?php if ($row->following()->image()) { ?>
									<img src="<?php echo $row->following()->image(); ?>" width="40" height="40" alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->following()->title()))); ?>" />
								<?php } else { ?>
									<span class="entry-id">
										<?php echo $row->get('following_id'); ?>
									</span>
								<?php } ?>
							</th>
							<td>
								<a class="entry-title" href="<?php echo Route::url($row->following()->link()); ?>">
									<?php echo $this->escape(stripslashes($row->following()->title())); ?>
								</a>
								<?php if ($row->get('following_type') == 'collection') { ?>
									<?php echo Lang::txt('by %s', $this->escape(stripslashes($row->following()->creator('name')))); ?>
								<?php } ?>
								<br />
								<span class="entry-details">
									<span class="follower count"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS', $row->count('followers')); ?></span>
									<?php if ($row->get('following_type') != 'collection') { ?>
										<span class="following count"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING', $row->count('following')); ?></span>
									<?php } ?>
								</span>
							</td>
							<td>
								<?php if ($this->params->get('access-manage-collection')) { ?>
									<a class="icon-unfollow unfollow btn" data-id="<?php echo $row->get('following_id'); ?>" data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo Route::url($row->following()->link('unfollow')); ?>">
										<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?></span>
									</a>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
			$pageNav->setAdditionalUrlParam('active', 'collections');
			$pageNav->setAdditionalUrlParam('task', 'following');
			echo $pageNav->render();
			?>
			<div class="clear"></div>
		</div><!-- / .container -->
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1', Route::url('index.php?option=com_collections')); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3'); ?></li>
					</ol>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_IS_FOLLOWING'); ?></strong></p>
					<p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p>
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_NOT_FOLLOWING_ANYONE'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
	<div class="clear"></div>
</form>