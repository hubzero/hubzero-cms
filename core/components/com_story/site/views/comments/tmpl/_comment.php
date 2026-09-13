<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Helpers\Thread;

$comment = $this->comment;
$indent  = Thread::indent($this->indent);
$base    = 'index.php?option=' . $this->option . '&view=comments&discussion=' . $this->discussion->get('id');

?>
<article class="story-comment depth-<?php echo $indent; ?><?php echo $comment->isDeleted() ? ' is-deleted' : ''; ?>"
	id="c<?php echo (int) $comment->get('id'); ?>"
	style="margin-left: <?php echo $indent * 2; ?>em;">

<?php if ($comment->isDeleted()) { ?>
	<p class="story-comment-gone"><?php echo Lang::txt('COM_STORY_COMMENT_WITHDRAWN'); ?></p>
<?php } else { ?>
	<header class="story-comment-header">
		<h3 class="story-comment-subject">
			<a href="<?php echo Route::url($base . '#c' . $comment->get('id')); ?>"><?php
				echo $this->escape($comment->get('subject') ?: Lang::txt('COM_STORY_COMMENT_NO_SUBJECT'));
			?></a>
		</h3>
		<p class="story-comment-byline">
			<span class="story-comment-author"><?php echo $this->escape($comment->authorName()); ?></span>
			<time datetime="<?php echo $this->escape($comment->get('created')); ?>"><?php
				echo Date::of($comment->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			?></time>
<?php if (!$this->preference->get('hide_scores')) { ?>
			<span class="story-comment-score"><?php echo Lang::txt('COM_STORY_SCORE', (int) $comment->get('score')); ?></span>
<?php } ?>
		</p>
	</header>

	<div class="story-comment-body">
		<?php echo nl2br($this->escape($comment->get('comment'))); ?>
	</div>

	<footer class="story-comment-actions">
<?php if ($this->canReply) { ?>
		<a class="story-reply" href="<?php echo Route::url($base . '&task=new&parent=' . $comment->get('id')); ?>"><?php echo Lang::txt('COM_STORY_REPLY'); ?></a>
<?php } ?>
<?php if ($this->canEdit) { ?>
		<a class="story-edit" href="<?php echo Route::url($base . '&task=edit&comment=' . $comment->get('id')); ?>"><?php echo Lang::txt('JACTION_EDIT'); ?></a>
		<a class="story-withdraw" href="<?php echo Route::url($base . '&task=delete&comment=' . $comment->get('id') . '&' . Session::getFormToken() . '=1'); ?>"
			onclick="return confirm('<?php echo Lang::txt('COM_STORY_COMMENT_WITHDRAW_SURE'); ?>');"><?php echo Lang::txt('COM_STORY_WITHDRAW'); ?></a>
<?php } ?>
	</footer>
<?php } ?>
</article>
