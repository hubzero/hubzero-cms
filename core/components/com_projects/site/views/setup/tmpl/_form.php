<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
<input type="hidden" name="pid" id="pid" value="<?php echo $this->model->get('id'); ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="setup" id="insetup" value="<?php echo $this->model->inSetup() ? 1 : 0; ?>" />
<input type="hidden" name="active" value="<?php echo $this->section; ?>" />
<input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
<input type="hidden" name="gid" value="<?php echo $this->model->get('owned_by_group') ? $this->model->get('owned_by_group') : 0; ?>" />

<?php echo Html::input('token'); ?>
<?php echo Html::input('honeypot'); 