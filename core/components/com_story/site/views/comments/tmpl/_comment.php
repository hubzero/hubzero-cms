<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Helpers\Thread;

$entry   = $this->entry;
$comment = $entry->comment;
$indent  = Thread::indent($entry->indent);
$base    = 'index.php?option=' . $this->option . '&view=comments&discussion=' . $this->discussion->get('id');
$link    = Route::url($base . '&comment=' . $comment->get('id') . '#c' . $comment->get('id'));
$scores  = !$this->preference->get('hide_scores');

$classes = array('story-comment', 'depth-' . $indent, 'show-' . $entry->show);
if ($comment->isDeleted())
{
	$classes[] = 'is-deleted';
}
if ($entry->highlighted)
{
	$classes[] = 'is-highlighted';
}

?>
<article class="<?php echo implode(' ', $classes); ?>"
	id="c<?php echo (int) $comment->get('id'); ?>"
	style="margin-left: <?php echo $indent * 2; ?>em;">

<?php if ($comment->isDeleted()) { ?>
	<p class="story-comment-gone"><?php echo Lang::txt('COM_STORY_COMMENT_WITHDRAWN'); ?></p>

<?php } elseif ($entry->show == Thread::SHOW_STUB) { ?>
	<p class="story-comment-stub">
		<a href="<?php echo $link; ?>"><?php echo $this->escape($comment->get('subject') ?: Lang::txt('COM_STORY_COMMENT_NO_SUBJECT')); ?></a>
		<span class="story-comment-author"><?php echo $this->escape($comment->authorName()); ?></span>
<?php if ($scores) { ?>
		<span class="story-comment-score" title="<?php echo $this->escape(Thread::explain($entry)); ?>"><?php
			echo Lang::txt('COM_STORY_SCORE', Thread::number($entry->score, false));
		?></span>
<?php } ?>
		<a class="story-expand" href="<?php echo $link; ?>"><?php echo Lang::txt('COM_STORY_EXPAND'); ?></a>
	</p>

<?php } else { ?>
	<header class="story-comment-header">
		<h3 class="story-comment-subject">
			<a href="<?php echo $link; ?>"><?php echo $this->escape($comment->get('subject') ?: Lang::txt('COM_STORY_COMMENT_NO_SUBJECT')); ?></a>
		</h3>
		<p class="story-comment-byline">
			<span class="story-comment-author"><?php echo $this->escape($comment->authorName()); ?></span>
			<time datetime="<?php echo $this->escape($comment->get('created')); ?>"><?php
				echo Date::of($comment->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			?></time>
<?php if ($scores) { ?>
			<span class="story-comment-score" title="<?php echo $this->escape(Thread::explain($entry)); ?>"><?php
				echo Lang::txt('COM_STORY_SCORE', Thread::number($entry->score, false));
			?></span>
<?php } ?>
		</p>
	</header>

	<div class="story-comment-body">
<?php
	$body = $comment->get('comment');

	if ($entry->show == Thread::SHOW_TRUNCATED)
	{
		$body = Thread::shorten($body, (int) $this->preference->get('max_comment_size'));
	}

	echo nl2br($this->escape($body));
?>
<?php if ($entry->show == Thread::SHOW_TRUNCATED) { ?>
		<p class="story-comment-more">&hellip; <a href="<?php echo $link; ?>"><?php echo Lang::txt('COM_STORY_READ_THE_REST'); ?></a></p>
<?php } ?>
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
