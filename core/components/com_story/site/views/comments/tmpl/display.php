<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Preference;

$base    = 'index.php?option=' . $this->option . '&view=comments&discussion=' . $this->discussion->get('id');
$current = $this->preference->get('mode');

?>
<header id="content-header">
	<h2><?php echo $this->escape($this->discussion->get('title')); ?></h2>
<?php if ($this->story->get('id')) { ?>
	<p class="story-back"><a href="<?php echo Route::url($this->story->link()); ?>"><?php echo Lang::txt('COM_STORY_BACK_TO_STORY'); ?></a></p>
<?php } ?>
</header>

<section class="main section">

	<nav class="story-modes">
		<span class="story-modes-label"><?php echo Lang::txt('COM_STORY_MODE'); ?></span>
<?php foreach (Preference::modes() as $mode) { ?>
		<a class="story-mode<?php echo ($mode == $current) ? ' is-current' : ''; ?>"
			href="<?php echo Route::url($base . '&mode=' . $mode); ?>"><?php
			echo Lang::txt('COM_STORY_MODE_' . strtoupper($mode));
		?></a>
<?php } ?>
<?php if (!User::isGuest()) { ?>
		<a class="story-preferences" href="<?php echo Route::url('index.php?option=' . $this->option . '&view=comments&task=preferences'); ?>"><?php echo Lang::txt('COM_STORY_PREFERENCES'); ?></a>
<?php } ?>
	</nav>

<?php if ($current == Preference::MODE_NONE) { ?>
	<p class="info"><?php echo Lang::txt('COM_STORY_COMMENTS_HIDDEN'); ?></p>
<?php } elseif (!count($this->thread)) { ?>
	<p class="info"><?php echo Lang::txt('COM_STORY_NO_COMMENTS'); ?></p>
<?php } else { ?>
	<div class="story-comments">
<?php
	$canReply = !$this->refusal;

	foreach ($this->thread as $entry)
	{
		$this->view('_comment')
			->set('option', $this->option)
			->set('discussion', $this->discussion)
			->set('preference', $this->preference)
			->set('entry', $entry)
			->set('canReply', $canReply)
			->set('canEdit', !User::isGuest() && $entry->comment->get('created_by') == User::get('id') && !$entry->comment->isDeleted())
			->display();
	}
?>
	</div>
<?php } ?>

<?php if ($this->refusal) { ?>
	<p class="warning"><?php echo $this->escape($this->refusal); ?></p>
<?php } else { ?>
	<p class="story-add"><a class="btn" href="<?php echo Route::url($base . '&task=new'); ?>"><?php echo Lang::txt('COM_STORY_ADD_COMMENT'); ?></a></p>
<?php } ?>

</section>
