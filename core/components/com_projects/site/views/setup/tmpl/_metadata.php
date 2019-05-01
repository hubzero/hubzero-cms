<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->exists())
{
	return;
}
?>
<div class="info_blurb grid">
	<div class="col span1">
		<img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=media'); ?>" alt="" />
	</div>
	<div class="col span6">
		<?php echo '<span class="prominent">' . Lang::txt('COM_PROJECTS_PROJECT'). '</span>: ' . $this->escape($this->model->get('title')); ?>
		(<span><?php echo $this->model->get('alias'); ?></span>)
		<span class="block faded"><?php echo Lang::txt('COM_PROJECTS_CREATED') . ' ' . $this->model->created('date'); ?></span>
	</div>
	<div class="col span5 omega">
	</div>
</div>
