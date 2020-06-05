<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title($this->group->get('description').': '.$this->page->get('title').' - ' . Lang::txt('COM_GROUPS_PAGES_ERRORS'), 'groups.png');
Toolbar::custom('errorscheckagain', 'check', 'check', 'COM_GROUPS_PAGES_CHECK_AGAIN', false);
Toolbar::cancel();

// page version content
$content = $this->page->version()->get('content');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" method="post" name="adminForm" id="item-form">

	<p class="error">
		<?php echo Lang::txt('COM_GROUPS_PAGES_ERROR_LIST', $this->page->get('title'), $this->error); ?>
	</p>

	<h3><?php echo Lang::txt('COM_GROUPS_PAGES_VIEW_RAW_CODE'); ?></h3>
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
					<?php highlight_string($content); ?>
				</td>
			</tr>
		</table>
	</div>

	<h3><?php echo Lang::txt('COM_GROUPS_PAGES_UPDATE_CONTENT'); ?></h3>
	<textarea name="page[content]" rows="40"><?php echo $content; ?></textarea>

	<input type="hidden" name="page[id]" value="<?php echo $this->page->get('id'); ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>