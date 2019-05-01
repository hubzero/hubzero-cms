<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CACHE_PURGE_EXPIRED_CACHE'), 'cache');
Toolbar::custom('purge', 'delete.png', 'delete_f2.png', 'COM_CACHE_PURGE_EXPIRED', false);
Toolbar::divider();
if (User::authorise('core.admin', 'com_cache'))
{
	Toolbar::preferences('com_cache');
	Toolbar::divider();
}
Toolbar::help('purge_expired');
?>

<form action="<?php echo Route::url('index.php?option=com_cache'); ?>" method="post" name="adminForm" id="item-form">

	<p class="mod-purge-instruct"><?php echo Lang::txt('COM_CACHE_PURGE_INSTRUCTIONS'); ?></p>
	<p class="warning"><?php echo Lang::txt('COM_CACHE_RESOURCE_INTENSIVE_WARNING'); ?></p>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_cache" />

	<?php echo Html::input('token'); ?>
</form>
