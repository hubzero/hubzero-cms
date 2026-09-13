<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$hasOptional = false;
foreach ($this->mine as $entry)
{
	if ($entry['optional'])
	{
		$hasOptional = true;
		break;
	}
}

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_KARMA'); ?></h2>
</header>

<section class="main section">

	<h3><?php echo Lang::txt('COM_KARMA_STANDING_HEADING'); ?></h3>
	<p class="hint"><?php echo Lang::txt('COM_KARMA_STANDING_INTRO'); ?></p>

<?php if ($this->standing) { ?>
	<dl class="karma-standing">
<?php foreach ($this->standing as $alias => $value) { ?>
		<dt><?php echo $this->escape($alias); ?></dt>
		<dd><?php echo $this->escape(is_null($value) ? Lang::txt('COM_KARMA_STANDING_UNSET') : $value); ?></dd>
<?php } ?>
	</dl>
<?php } else { ?>
	<p><?php echo Lang::txt('COM_KARMA_STANDING_NONE'); ?></p>
<?php } ?>

	<h3><?php echo Lang::txt('COM_KARMA_SCALES_HEADING'); ?></h3>

<?php if (!count($this->mine)) { ?>
	<p><?php echo Lang::txt('COM_KARMA_NO_SCALES'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=save'); ?>" method="post">
		<table class="karma-scales">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_SCALE'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_YOURS'); ?></th>
<?php if ($hasOptional) { ?>
					<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_SHOW_ON_PROFILE'); ?></th>
<?php } ?>
				</tr>
			</thead>
			<tbody>
<?php foreach ($this->mine as $entry) { ?>
				<tr>
					<th scope="row">
						<?php echo $this->escape($entry['scale']->get('title')); ?>
<?php if ($entry['scale']->get('description')) { ?>
						<span class="hint"><?php echo $this->escape($entry['scale']->get('description')); ?></span>
<?php } ?>
					</th>
					<td><strong><?php echo $this->escape(is_null($entry['value']) ? Lang::txt('COM_KARMA_VALUE_HIDDEN') : $entry['value']); ?></strong></td>
<?php if ($hasOptional) { ?>
					<td>
<?php if ($entry['optional']) { ?>
						<input type="checkbox" name="publish[<?php echo $this->escape($entry['scale']->get('alias')); ?>]" id="publish-<?php echo $this->escape($entry['scale']->get('alias')); ?>" value="1"<?php if ($entry['published']) { echo ' checked="checked"'; } ?> />
						<label for="publish-<?php echo $this->escape($entry['scale']->get('alias')); ?>"><?php echo Lang::txt('COM_KARMA_SHOW_ON_PROFILE'); ?></label>
<?php } else { ?>
						<span class="hint"><?php echo Lang::txt('COM_KARMA_NOT_YOUR_CHOICE'); ?></span>
<?php } ?>
					</td>
<?php } ?>
				</tr>
<?php } ?>
			</tbody>
		</table>

<?php if ($hasOptional) { ?>
		<p class="submit">
			<input type="submit" value="<?php echo Lang::txt('COM_KARMA_SAVE_PREFERENCES'); ?>" />
		</p>
<?php } ?>

		<?php echo Html::input('token'); ?>
	</form>
<?php } ?>

	<h3><?php echo Lang::txt('COM_KARMA_RECENT_HEADING'); ?></h3>

<?php if (!count($this->recent)) { ?>
	<p><?php echo Lang::txt('COM_KARMA_RECENT_NONE'); ?></p>
<?php } else { ?>
	<table class="karma-ledger">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_WHEN'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_WHAT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_KARMA_COL_DELTA'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->recent as $row) { ?>
			<tr<?php if (!$row->isActive()) { echo ' class="reversed"'; } ?>>
				<td><time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></td>
				<td>
					<?php echo $this->escape($row->get('rule')); ?>
<?php if (!$row->isActive()) { ?>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_STATE_REVERSED'); ?></span>
<?php } ?>
				</td>
				<td><?php echo ($row->get('applied') > 0 ? '+' : '') . $this->escape($row->get('applied')); ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>

	<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=ledger'); ?>"><?php echo Lang::txt('COM_KARMA_RECENT_ALL'); ?></a></p>
<?php } ?>

</section>
