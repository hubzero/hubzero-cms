<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&view=queue';

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_STORY_QUEUE'); ?></h2>
	<p class="story-queue-intro"><?php echo Lang::txt('COM_STORY_QUEUE_INTRO'); ?></p>
</header>

<section class="main section">

	<p class="story-add">
		<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&view=submissions&task=new'); ?>"><?php
			echo Lang::txt('COM_STORY_SUBMIT');
		?></a>
	</p>

<?php if (!count($this->rows)) { ?>
	<p class="info"><?php echo Lang::txt('COM_STORY_QUEUE_EMPTY'); ?></p>
<?php } else { ?>
	<ol class="story-queue">
<?php foreach ($this->rows as $row) {
	$id   = (int) $row->get('id');
	$mine = isset($this->voted[$id]) ? $this->voted[$id] : 0;
?>
		<li class="story-queue-entry band-<?php echo (int) $row->band($this->config); ?>">
			<div class="story-queue-vote">
<?php if ($this->canVote && (int) $row->get('created_by') != User::get('id')) { ?>
				<a class="story-vote-up<?php echo ($mine > 0) ? ' is-mine' : ''; ?>"
					href="<?php echo Route::url($base . '&task=vote&submission=' . $id . '&vote=up&' . Session::getFormToken() . '=1'); ?>"
					title="<?php echo Lang::txt('COM_STORY_VOTE_UP'); ?>">&#9650;</a>
				<a class="story-vote-down<?php echo ($mine < 0) ? ' is-mine' : ''; ?>"
					href="<?php echo Route::url($base . '&task=vote&submission=' . $id . '&vote=down&' . Session::getFormToken() . '=1'); ?>"
					title="<?php echo Lang::txt('COM_STORY_VOTE_DOWN'); ?>">&#9660;</a>
<?php } ?>
			</div>

			<div class="story-queue-body">
				<h3 class="story-queue-subject"><?php
					echo $row->get('url')
						? '<a href="' . $this->escape($row->get('url')) . '" rel="nofollow noopener">' . $this->escape($row->get('subject')) . '</a>'
						: $this->escape($row->get('subject'));
				?></h3>
				<p class="story-queue-meta">
					<time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php
						echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
					?></time>
<?php if ($row->get('state') == 'hold') { ?>
					<span class="story-queue-held"><?php echo Lang::txt('COM_STORY_STATE_HOLD'); ?></span>
<?php } ?>
				</p>
<?php if ($row->get('body')) { ?>
				<div class="story-queue-text"><?php echo nl2br($this->escape($row->get('body'))); ?></div>
<?php } ?>
			</div>
		</li>
<?php } ?>
	</ol>

	<?php echo $this->rows->pagination; ?>
<?php } ?>

</section>
