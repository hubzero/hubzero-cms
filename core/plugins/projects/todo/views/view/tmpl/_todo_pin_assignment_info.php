<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$id = $this->id;
$isComplete = $this->isComplete;
$todo = $this->todo;
$overdue = $todo->isOverdue();

$overdueNotice = $overdue ? ' ('.Lang::txt('PLG_PROJECTS_TODO_OVERDUE').')' : '';

if ($isComplete): ?>
	<span class="todo-assigned"><?php echo $todo->closer('name'); ?></span>
	<span class="todo-due"><?php echo Lang::txt('PLG_PROJECTS_TODO_CHECKED_OFF') . ' ' . $todo->closed('date'); ?></span>
<?php else: ?>
	<span class="todo-assigned" id="<?php echo "td-assigned-$id"; ?>"><?php echo $todo->owner('name'); ?></span>
	<?php if ($todo->due()): ?>
		<span class="todo-due" id="<?php echo "td-due-$id"; ?>">
			<?php
			echo Lang::txt('PLG_PROJECTS_TODO_DUE') . ' ' . $todo->due('date') . $overdueNotice; ?>
		</span>
	<?php	endif; ?>
<?php endif; ?>
