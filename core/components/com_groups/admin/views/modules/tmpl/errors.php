<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title($this->group->get('description').': '.$this->module->get('title').' - ' . Lang::txt('COM_GROUPS_MODULES_ERRORS'), 'groups.png');
Toolbar::custom('errorscheckagain', 'check', 'check', 'COM_GROUPS_MODULES_CHECK_AGAIN', false);
Toolbar::cancel();

// page version content
$content = $this->module->get('content');
?>

<form action="i<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" method="post" name="adminForm" id="item-form">

	<p class="error">
		<?php echo Lang::txt('COM_GROUPS_MODULES_ERROR_LIST', $this->module->get('title'), $this->error); ?>
	</p>

	<h3><?php echo Lang::txt('COM_GROUPS_MODULES_RAW_CODE'); ?></h3>
	<div class="code">
		<?php
			$lines = explode("\n", $content);
			$lineCode = '';
			for ($i=1; $i <= count($lines); $i++)
			{
				$lineCode .= "&nbsp;".$i."&nbsp;<br>";
			}
		?>
		<table>
			<tr>
				<td class="lines"><?php echo $lineCode; ?></td>
				<td class="code">
					<?php echo highlight_string($content); ?>
				</td>
			</tr>
		</table>
	</div>

	<h3><?php echo Lang::txt('COM_GROUPS_MODULES_UPDATE_CONTENT'); ?></h3>
	<textarea name="module[content]" rows="40"><?php echo $content; ?></textarea>

	<input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>