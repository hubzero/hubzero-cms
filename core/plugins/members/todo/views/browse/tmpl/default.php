<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->css()
	 ->css('todo.css', 'plg_projects_todo')
	 ->js()
	 ->js('jquery.timepicker', 'system');

$filters = array('projects' => $this->projects);
$url = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=todo';

$cfilters = array(
	'mine'  => 1,
	'active'=> 1,
	'editor'=> 1
);

?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_TODO'); ?>
</h3>
<?php if ($this->model->entries('count', $cfilters)) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url($url . '&action=new'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_TODO_ADD_TODO'); ?>
			</a>
		</li>
	</ul>
<?php } ?>
<?php if ((User::get('id') == $this->member->get('id') && empty($this->projects)) || !$this->todo->entries('count', $filters)) { ?>
	<div class="introduction">
		<div class="introduction-message">
			<p><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_EMPTY'); ?></p>
		</div>
		<div class="introduction-questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START_EXPLANATION', Route::url('index.php?option=com_projects')); ?></p>
		</div>
	</div><!-- / .introduction -->
<?php } else { ?>
	<div class="container">
		<?php if ($this->getError()) : ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php endif; ?>
		<?php 
		// Get shared todo items from blog plugin
		$results = Event::trigger('projects.onShared', array(
			'todo',
			$this->model,
			$this->projects,
			$this->member->get('id'),
			$this->filters
		));
		echo implode("\n", $results);
		?>
	</div>
<?php }
