<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<?php if (!User::isGuest() && !$this->params->get('access-create-item')) { ?>
<ul id="page_options">
	<li>
		<?php if ($this->model->isFollowing()) { ?>
		<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo Lang::txt('Follow All'); ?>" data-text-unfollow="<?php echo Lang::txt('Unfollow All'); ?>" href="<?php echo Route::url($base . '&scope=unfollow'); ?>">
			<span><?php echo Lang::txt('Unfollow All'); ?></span>
		</a>
		<?php } else { ?>
		<a class="icon-follow follow btn" data-text-follow="<?php echo Lang::txt('Follow All'); ?>" data-text-unfollow="<?php echo Lang::txt('Unfollow All'); ?>" href="<?php echo Route::url($base . '&scope=follow'); ?>">
			<span><?php echo Lang::txt('Follow All'); ?></span>
		</a>
		<?php } ?>
	</li>
</ul>
<?php } ?>

<form method="get" action="<?php echo Route::url($base . '&scope=following'); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('group', $this->group)
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
							<img src="<?php echo $row->following()->image(); ?>" width="40" height="40" alt="Profile picture of <?php echo $this->escape(stripslashes($row->following()->title())); ?>" />
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
								<span class="follower count"><?php echo Lang::txt('<strong>%s</strong> followers', $row->count('followers')); ?></span>
							<?php if ($row->get('following_type') != 'collection') { ?>
								<span class="following count"><?php echo Lang::txt('<strong>%s</strong> following', $row->count('following')); ?></span>
							<?php } ?>
							</span>
						</td>
						<td>
							<?php if ($this->params->get('access-manage-collection')) { ?>
							<a class="unfollow btn" data-id="<?php echo $row->get('following_id'); ?>" data-text-follow="<?php echo Lang::txt('Follow'); ?>" data-text-unfollow="<?php echo Lang::txt('Unfollow'); ?>" href="<?php echo Route::url($row->following()->link('unfollow')); ?>">
								<span><?php echo Lang::txt('Unfollow'); ?></span>
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
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'collections');
			$pageNav->setAdditionalUrlParam('scope', 'following');
			echo $pageNav->render();
			?>
			<div class="clear"></div>
		</div><!-- / .container -->
	<?php } else { ?>
			<div id="collection-introduction">
				<div class="instructions">
			<?php if ($this->params->get('access-manage-collection')) { ?>
					<ol>
						<li><?php echo Lang::txt('Find a member or collection you like.'); ?></li>
						<li><?php echo Lang::txt('Click on the "follow" button.'); ?></li>
						<li><?php echo Lang::txt('Come back to collections and see all the posts!'); ?></li>
					</ol>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('What is following?'); ?></strong></p>
					<p><?php echo Lang::txt('"Following" someone means you\'ll see that person\'s posts on this page in real time. If he/she creates a new collection, you\'ll automatically follow the new collection as well.'); ?><p>
					<p><?php echo Lang::txt('You can follow individual collections if you\'re only interested in seeing posts being added to specific collections.'); ?><p>
					<p><?php echo Lang::txt('You can unfollow other people or collections at any time.'); ?></p>
				</div>
			<?php } else { ?>
					<p>
						<?php echo Lang::txt('This group is not following anyone or any collections.'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
			</div><!-- / #collection-introduction -->
	<?php } ?>
	<div class="clear"></div>
</form>