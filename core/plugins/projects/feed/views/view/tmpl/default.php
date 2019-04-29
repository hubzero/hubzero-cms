<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div id="plg-header">
	<h3 class="newsupdate"><?php echo $this->title; ?></h3>
</div>

<?php
	// New update form
	$this->view('default', 'addupdate')
		->set('option', $this->option)
		->set('model', $this->model)
		->display();
?>

<div id="latest_activity" class="infofeed" data-frequency="60" data-base="<?php echo Route::url($this->model->link() . '&active=feed'); ?>">
	<?php
	// Display item list
	$this->view('default', 'activity')
		->set('option', $this->option)
		->set('model', $this->model)
		->set('activities', $this->activities)
		->set('total', $this->total)
		->set('filters', $this->filters)
		->set('limit', $this->limit)
		->display();
	?>

	<form id="hubForm" method="post" action="<?php echo Route::url($this->model->link()); ?>">
		<div>
			<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="action" value="" />
		</div>
	</form>
</div>