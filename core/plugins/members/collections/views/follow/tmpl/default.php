<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

$this->css();
?>

<form method="get" action="<?php echo Route::url($base . '&task=' . $this->collection->get('alias')); ?>" id="collections">

	<p class="overview">
		<span class="title count">
			"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
		</span>
		<span class="posts count">
			<?php echo Lang::txt('<strong>%s</strong> posts', $this->rows->total()); ?>
		</span>
<?php if (!User::isGuest()) { ?>
	<?php if ($this->rows && $this->params->get('access-create-item')) { ?>
		<a class="add btn tooltips" title="<?php echo Lang::txt('New post :: Add a new post to this collection'); ?>" href="<?php echo Route::url($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
			<?php echo Lang::txt('New post'); ?>
		</a>
	<?php } else { ?>
		<a class="follow btn tooltips" title="<?php echo Lang::txt('Repost :: Watch this collection'); ?>" href="<?php echo Route::url($base . '&task=' . $this->collection->get('alias') . '/follow'); ?>">
			<?php echo Lang::txt('Follow'); //Repost collection ?>
		</a>
	<?php } ?>
<?php } ?>
		<span class="clear"></span>
	</p>

	<div id="posts">
<?php
if ($this->rows->total() > 0)
{
	foreach ($this->rows as $row)
	{
		$item = $row->item();

		if ($item->get('state') == 2)
		{
			$item->set('type', 'deleted');
		}
		$type = $item->get('type');
		if (!in_array($type, array('collection', 'deleted', 'image', 'file', 'text', 'link')))
		{
			$type = 'link';
		}
?>
		<div class="post <?php echo $type; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&task=post/' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
			<?php
				$this->view('default_' . $type, 'post')
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
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo Lang::txt('%s likes', $item->get('positive', 0)); ?>
						</span>
						<span class="comments">
							<?php echo Lang::txt('%s comments', $item->get('comments', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo Lang::txt('%s reposts', $item->get('reposts', 0)); ?>
						</span>
					</p>
			<?php if (!User::isGuest()) { ?>
					<div class="actions">
				<?php if ($item->get('created_by') == User::get('id')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/edit'); ?>">
							<span><?php echo Lang::txt('Edit'); ?></span>
						</a>
				<?php } else { ?>
						<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo Lang::txt('Like'); ?>" data-text-unlike="<?php echo Lang::txt('Unlike'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/vote'); ?>">
							<span><?php echo ($item->get('voted')) ? Lang::txt('Unlike') : Lang::txt('Like'); ?></span>
						</a>
				<?php } ?>
						<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/comment'); ?>">
							<span><?php echo Lang::txt('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/collect'); ?>">
							<span><?php echo Lang::txt('Collect'); ?></span>
						</a>
				<?php if ($row->get('original') && ($item->get('created_by') == User::get('id') || $this->params->get('access-delete-item'))) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/delete'); ?>">
							<span><?php echo Lang::txt('Delete'); ?></span>
						</a>
				<?php } else if ($row->get('created_by') == User::get('id') || $this->params->get('access-edit-item')) { ?>
						<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo Route::url($base . '&task=post/' . $row->get('id') . '/remove'); ?>">
							<span><?php echo Lang::txt('Remove'); ?></span>
						</a>
				<?php } ?>
					</div><!-- / .actions -->
			<?php } ?>
				</div><!-- / .meta -->

			<?php if ($row->original() || $item->get('created_by') != $this->member->get('id')) { ?>
				<div class="convo attribution clearfix">
					<a href="<?php echo Route::url('index.php?option=com_members&id=' . $item->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo $item->creator()->picture(0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . $item->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
						</a>
						posted
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo Date::of('date', $item->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></span>
							<span class="entry-date-on">on</span> <span class="time"><?php echo Date::of($item->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php } ?>
			<?php if (!$row->original()) {//if ($item->get('created_by') != $this->member->get('id')) { ?>
				<div class="convo attribution reposted clearfix">
					<a href="<?php echo Route::url('index.php?option=com_members&id=' . $row->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo $this->member->picture(0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a>
						onto
						<a href="<?php echo Route::url($base . ($this->collection->get('is_default') ? '' : '/' . $this->collection->get('alias'))); ?>">
							<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo JDate::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
							<span class="entry-date-on">on</span> <span class="time"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php } ?>
			</div><!-- / .content -->
		</div><!-- / .post -->
<?php
	}
}
else
{
?>
		<div id="collection-introduction">
	<?php if ($this->params->get('access-create-item')) { ?>
			<div class="instructions">
				<ol>
					<li><?php echo Lang::txt('Find images, files, links or text you want to share.'); ?></li>
					<li><?php echo Lang::txt('Click on "New post" button.'); ?></li>
					<li><?php echo Lang::txt('Add anything extra you want (tags are nice).'); ?></li>
					<li><?php echo Lang::txt('Done!'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<!-- <div class="questions">
				<p><strong>What is the "Collect" button for?</strong></p>
				<p>This is how you can add other content on the site to a collection. You can collect wiki pages, resources, and more. You can even collect other collections!<p>
			</div><!- / .post-type -->
	<?php } else { ?>
			<div class="instructions">
				<p><?php echo Lang::txt('No posts available for this collection.'); ?></p>
			</div><!-- / .instructions -->
	<?php } ?>
		</div><!-- / #collection-introduction -->
<?php
}
?>
	</div><!-- / #posts -->
</form>