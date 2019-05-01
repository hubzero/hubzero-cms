<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Members') . ': ' . Lang::txt('Plugins'), 'members');

if ($this->html)
{
	echo $this->html;
} else {
	Toolbar::cancel();
?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
		<p class="warning"><?php echo Lang::txt('No management interface found for this plugin.'); ?></p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="action" value="" />

		<?php echo Html::input('token'); ?>
	</form>
<?php } 