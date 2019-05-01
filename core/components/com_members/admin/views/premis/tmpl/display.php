<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('COM_MEMBERS_PREMIS'), 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}
?>

<?php
	$this->view('_submenu', 'registration')
	     ->display();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" name="hubForm" id="item-form" method="post" enctype="multipart/form-data">
	<fieldset>
		<p><input type="file" class="option" name="upload" /></p>
		<input type="submit" class="option" value="Import" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
</form>