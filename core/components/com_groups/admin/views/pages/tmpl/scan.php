<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title($this->group->get('description').': '.$this->page->get('title').' - ' . Lang::txt('COM_GROUPS_PAGES_SCAN'), 'groups.png');
Toolbar::custom('markscanned', 'check', 'check', 'COM_GROUPS_PAGES_MARK_SCANNED', false);
Toolbar::spacer();
Toolbar::custom('scanagain', 'check', 'check', 'COM_GROUPS_PAGES_SCAN_AGAIN', false);
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

// page version content
$content = $this->page->version()->get('content');
$action = Route::url(
	"index.php?option=$this->option&controller=$this->controller&gid=$this->group->cn&task=markscanned"
);
?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-confirm="<?php echo Lang::txt('COM_GROUPS_PAGES_MARK_SCANNED_CONFIRM'); ?>" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<input type="hidden" name="page[id]" value="<?php echo $this->page->get('id'); ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>

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
			<?php echo Lang::txt('COM_GROUPS_PAGES_SCAN_SEVERE', implode('<br />', $severe)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($elevated) > 0) : ?>
		<p class="warning">
			<?php echo Lang::txt('COM_GROUPS_PAGES_SCAN_ELEVATED', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($minor) > 0) : ?>
		<p class="info">
			<?php echo Lang::txt('COM_GROUPS_PAGES_SCAN_MINOR', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

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

</form>
