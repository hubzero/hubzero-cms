<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_KARMA_REVIEW'); ?></h2>
	<p class="review-intro"><?php echo Lang::txt('COM_KARMA_REVIEW_INTRO'); ?></p>
</header>

<section class="main section">

<?php if ($this->off) { ?>
	<p class="info"><?php echo Lang::txt('COM_KARMA_REVIEW_OFF'); ?></p>

<?php } elseif ($this->refusal) { ?>
	<p class="warning"><?php echo $this->escape($this->refusal); ?></p>

<?php } elseif (!count($this->batch)) { ?>
	<p class="info"><?php echo Lang::txt('COM_KARMA_REVIEW_NOTHING_WAITING'); ?></p>

<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=review'); ?>" method="post" id="review-form">

		<ol class="review-batch">
<?php foreach ($this->batch as $entry) {
	$log = $entry->log;
	$id  = (int) $log->get('id');
?>
			<li class="review-entry">
				<p class="review-what">
<?php if ($entry->reason) { ?>
					<span class="review-reason"><?php
						echo $this->escape($entry->reason->get('title'));
						echo ' (' . ($entry->reason->get('value') > 0 ? '+' : '') . (int) $entry->reason->get('value') . ')';
					?></span>
<?php } ?>
					<time datetime="<?php echo $this->escape($log->get('created')); ?>"><?php
						echo Date::of($log->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
					?></time>
				</p>

<?php if ($entry->item && method_exists($entry->item, 'comment')) {
	$comment = $entry->item->comment();
?>
				<blockquote class="review-text">
<?php if ($comment->get('subject')) { ?>
					<p class="review-subject"><strong><?php echo $this->escape($comment->get('subject')); ?></strong></p>
<?php } ?>
					<?php echo nl2br($this->escape($comment->get('comment'))); ?>
				</blockquote>
<?php } else { ?>
				<p class="review-gone"><?php echo Lang::txt('COM_KARMA_REVIEW_ITEM_GONE'); ?></p>
<?php } ?>

				<p class="review-judgment">
					<label>
						<input type="radio" name="judgment[<?php echo $id; ?>]" value="1" />
						<?php echo Lang::txt('COM_KARMA_REVIEW_FAIR'); ?>
					</label>
					<label>
						<input type="radio" name="judgment[<?php echo $id; ?>]" value="-1" />
						<?php echo Lang::txt('COM_KARMA_REVIEW_UNFAIR'); ?>
					</label>
					<label>
						<input type="radio" name="judgment[<?php echo $id; ?>]" value="" checked="checked" />
						<?php echo Lang::txt('COM_KARMA_REVIEW_SKIP'); ?>
					</label>
				</p>
			</li>
<?php } ?>
		</ol>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_KARMA_REVIEW_SUBMIT'); ?>" />
		</p>

		<input type="hidden" name="type" value="<?php echo $this->escape($this->itemType); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="review" />
		<input type="hidden" name="task" value="save" />
		<?php echo Html::input('token'); ?>
	</form>
<?php } ?>

</section>
