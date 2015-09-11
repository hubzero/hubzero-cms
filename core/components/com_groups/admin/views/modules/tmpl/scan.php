<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title($this->group->get('description').': '.$this->module->get('title').' - '. Lang::txt('COM_GROUPS_MODULES_SCAN'), 'groups.png');
Toolbar::custom('markscanned', 'check', 'check', 'COM_GROUPS_MODULES_MARK_SCANNED', false);
Toolbar::spacer();
Toolbar::custom('scanagain', 'check', 'check', 'COM_GROUPS_MODULES_SCAN_AGAIN', false);
Toolbar::cancel();

// page version content
$content = $this->module->get('content');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'markscanned')
	{
		if (!confirm('<?php echo Lang::txt('COM_GROUPS_MODULES_MARK_SCANNED_CONFIRM'); ?>'))
		{
			return false;
		}
	}
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" method="post" name="adminForm" id="item-form">

	<?php
		unset($this->issues->count);
		$severe = $elevated = $minor = array();
		foreach ($this->issues as $lang => $languageIssues)
		{
			foreach ($languageIssues as $type => $languageIssue)
			{
				foreach ($languageIssue as $line => $issue)
				{
					array_push($$type, 'Line ' . $line . '. ' . $this->escape($issue));
				}
			}
		}
	?>
	<?php if (count($severe) > 0) : ?>
		<p class="error">
			<?php echo Lang::txt('COM_GROUPS_MODULE_SCAN_SEVERE', implode('<br />', $severe)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($elevated) > 0) : ?>
		<p class="warning">
			<?php echo Lang::txt('COM_GROUPS_MODULE_SCAN_ELEVATED', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($minor) > 0) : ?>
		<p class="info">
			<?php echo Lang::txt('COM_GROUPS_MODULE_SCAN_MINOR', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

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