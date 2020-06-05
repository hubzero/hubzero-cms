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
	 ->js('jquery.timepicker', 'system')
	 ->js();

$total = $this->todo->entries('count', $this->filters);
$rows  = $this->todo->entries('list', $this->filters);
$listName = $this->todo->getListName($this->model->get('id'), $this->filters);

$whatsleft = $total - $this->filters['start'] - $this->filters['limit'];
$team_ids = array('0' => '');
$which = $this->filters['state'] == 1 ? strtolower(Lang::txt('PLG_PROJECTS_TODO_COMPLETED')) : Lang::txt('PLG_PROJECTS_TODO_OUTSTANDING');
$where = $listName ? ' '.Lang::txt('PLG_PROJECTS_TODO_TODO_ON_THIS_LIST') : '';
$where.= $this->filters['mine'] == 1 ? ' '.Lang::txt('PLG_PROJECTS_TODO_IN_MY_TODOS') : '';

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

?>
<form action="<?php echo Route::url($url); ?>" method="post" id="plg-form">
	<?php // View header
	$this->view('_header')
		->set('option', $this->option)
		->set('model', $this->model)
		->set('todo', $this->todo)
		->set('filters', $this->filters)
		->set('title', $this->title)
		->set('listName', $listName)
		->display();
	?>

	<?php if (!$this->filters['state'] && $this->model->access('content')) { ?>
		<ul id="page_options" class="pluginOptions">
			<li>
				<a class="icon-add add btn showinbox"  href="<?php echo Route::url($url . '&action=new&list=' . $this->filters['todolist']); ?>">
					<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_TODO'); ?>
				</a>
			</li>
		</ul>
	<?php } ?>

	<div class="container">
		<?php
		// View menu
		$this->view('_menu')
			->set('option', $this->option)
			->set('model', $this->model)
			->set('todo', $this->todo)
			->set('filters', $this->filters)
			->display();
		?>
		<ul class="pinboard allow-sort" id="pinboard">
			<?php if (count($rows) > 0) { ?>
				<?php
					$order = 1;
					foreach ($rows as $row)
					{
						$this->view('_todo_pin')
							->set('order', $order)
							->set('todo', $row)
							->set('url', $url)
							->display();
					}
					$order++;
				?>
			<?php } elseif (!$this->filters['state'] && $this->model->access('content')) { ?>
				<li class="todo-add">
					<span>
						<a class="item-add showinbox" href="<?php echo Route::url($url . '&action=new&list=' . $this->filters['todolist']); ?>">
							<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_TODO'); ?>
						</a>
					</span>
				</li>
			<?php } else { ?>
				<li class="todo-empty"><?php echo Lang::txt('PLG_PROJECTS_TODO_NO_TODOS').$where.'.'; ?></li>
			<?php } ?>
			<li class="clear"></li>
		</ul>
		<?php
		// View footer
		$this->view('_footer')
			->set('option', $this->option)
			->set('model', $this->model)
			->set('todo', $this->todo)
			->set('filters', $this->filters)
			->set('uid', $this->uid)
			->display();
		?>
	</div>
</form>
