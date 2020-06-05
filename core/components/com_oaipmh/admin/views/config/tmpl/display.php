<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Oaipmh\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_OAIPMH_SETTINGS'), 'oaipmh');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_oaipmh', 500);
	Toolbar::spacer();
}
Toolbar::help('oaipmh');

$this->css();

$lang = Lang::getTag();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php include_once Component::path($this->option) . '/admin/help/' . $lang . '/oaipmh.phtml'; ?>

	<?php echo Html::input('token'); ?>
</form>