<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('curation.js');

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE') . ' - ' . $this->row->type . ': ' . Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED'), 'publications');
Toolbar::save('saveadvanced');
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;

$blockSelection = array('active' => array());

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=saveadvanced'); ?>" method="post" id="item-form" name="adminForm">
	<p><a class="button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->row->id); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_BACK') . ' ' . $this->row->type . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE'); ?></a></p>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING'); ?></span></legend>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="saveadvanced" />
		<input type="hidden" name="neworder" id="neworder" value="" />

		<p class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING_HINT'); ?></p>

		<div class="input-wrap">
			<textarea cols="50" rows="10" name="curation"><?php echo json_encode($manifest); ?></textarea>
		</div>
	</fieldset>
	<?php echo Html::input('token'); ?>
</form>