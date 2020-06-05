<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$action = Route::url("index.php?option=$this->option&controller=$this->controller");
$boost = $this->boost;
$task = $this->task;
$typeOptions = $this->typeOptions;
?>

<form action="<?php echo $action; ?>"
	method="post"
	name="adminForm"
	id="item-form">

	<div class="grid">
		<div class="col span7">
			<?php
				$this->view('_boost_details_fieldset')
					->set('boost', $boost)
					->set('typeOptions', $typeOptions)
					->display();
			?>
		</div>
	</div>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="boosts" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
</form>
