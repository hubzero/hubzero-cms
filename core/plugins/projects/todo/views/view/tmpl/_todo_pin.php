<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use \Hubzero\Utility\Str;

$defaultColor = 'grey';
$order = $this->order;
$url = $this->url;

$todo = $this->todo;
$todoColor = $todo->get('color');
$truncatedContent = Str::truncate($todo->get('content'), 150);
$id = $todo->get('id');
$isComplete = $todo->isComplete();

$color = $todoColor ? $todoColor : $defaultColor;
$colorClass = "pin_$color";
$closedClass = $isComplete ? 'tdclosed' : '';
$commentsLink = Route::url("$url&action=view") . "/?todoid=$id";
$commentsLinkTitle = Lang::txt('PLG_PROJECTS_TODO_TODO_VIEW_COMMENTS_AND_EDIT');
?>
<li class="<?php echo "$colorClass $closedClass"; ?> droptarget " id="todo-<?php echo $id; ?>">
	<div id="td-<?php echo $id; ?>">

		<input type="hidden" name="order" id="order-<?php echo $id; ?>" value="<?php echo $order; ?>" />

		<span class="pin handle">&nbsp;</span>

		<?php if ($isComplete): ?>
			<span class="complete">&nbsp;</span>
		<?php endif; ?>

		<span class="todo-content" id="td-content-<?php echo $id; ?>"><?php echo $truncatedContent; ?></span>
		<span class="todo-options" id="td-options-<?php echo $id; ?>">
			<?php
				$this->view('_todo_pin_assignment_info')
					->set('id', $id)
					->set('isComplete', $isComplete)
					->set('todo', $todo)
					->display();
			?>
		</span>

		<span class="comment-blurb">
			<a href="<?php echo $commentsLink; ?>" title="<?php echo $commentsLinkTitle; ?>"><?php echo $todo->comments('count'); ?>&nbsp;&raquo;</a>
		</span>
	</div>
</li>
