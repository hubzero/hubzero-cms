<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

?>
<div id="plg-header">
	<h3 class="todo"><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?> <a href="<?php echo Route::url($url); ?>"> <?php } ?><?php echo $this->title; ?><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?></a><?php } ?>
	<?php if ($this->listName) { ?> &raquo; <a href="<?php echo Route::url($url).'/?list='.$this->filters['todolist']; ?>"><span class="indlist <?php echo 'pin_'.$this->filters['todolist'] ?>"><?php echo $this->listName; ?></span></a> <?php } ?>
	<?php if ($this->filters['assignedto']) { ?> &raquo; <span class="indlist mytodo"><a href="<?php echo Route::url($url).'/?mine=1'; ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_MY_TODOS')); ?></a></span> <?php } ?>
	<?php if ($this->filters['state']) { ?> &raquo; <span class="indlist completedtd"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_COMPLETED')); ?></span> <?php } ?>
	</h3>
</div>