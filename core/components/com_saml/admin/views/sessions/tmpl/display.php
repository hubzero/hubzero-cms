<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SAML') . ': ' . Lang::txt('COM_SAML_SESSIONS'), 'saml');
if (User::authorise('core.delete', $this->option))
{
	Toolbar::custom('deleteexpired', 'expired', '', 'COM_SAML_DELETE_EXPIRED', false);
	Toolbar::spacer();
	Toolbar::deleteList('COM_SAML_CONFIRM_DELETE_SESSIONS');
}
?>

<p class="info">
	<?php
	echo $this->sessionTableIsAuthoritative
		? Lang::txt('COM_SAML_EXPIRED_SCOPE_FULL')
		: Lang::txt('COM_SAML_EXPIRED_SCOPE_ENDED_ONLY');
	?>
</p>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter-sp"><?php echo Lang::txt('COM_SAML_SERVICE_PROVIDER'); ?>:</label>
				<select name="sp" id="filter-sp" class="filter filter-submit">
					<option value="0"><?php echo Lang::txt('COM_SAML_ALL_SERVICE_PROVIDERS'); ?></option>
					<?php foreach ($this->sps as $sp) { ?>
						<option value="<?php echo $sp->get('id'); ?>"<?php echo $this->filters['sp'] == $sp->get('id') ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($sp->get('name'))); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('JSTATUS'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php echo ($this->filters['state'] < 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_ALL_STATES'); ?></option>
					<option value="1"<?php echo ($this->filters['state'] === 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_SESSION_ACTIVE'); ?></option>
					<option value="0"<?php echo ($this->filters['state'] === 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_SESSION_ENDED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Lang::txt('COM_SAML_COL_USER'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_SAML_SERVICE_PROVIDER'); ?></th>
				<th scope="col"><?php echo Lang::txt('JSTATUS'); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_SAML_COL_STARTED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_SAML_COL_ENDED', 'ended', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('id'); ?></label>
				</td>
				<td>
					<?php echo $this->escape($row->user ? $row->user->get('username', Lang::txt('COM_SAML_UNKNOWN')) : Lang::txt('COM_SAML_UNKNOWN')); ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape($row->sp && $row->sp->get('id') ? stripslashes($row->sp->get('name')) : Lang::txt('COM_SAML_UNKNOWN')); ?>
				</td>
				<td>
					<?php if ($row->isActive()) { ?>
						<span class="state publish"><span><?php echo Lang::txt('COM_SAML_SESSION_ACTIVE'); ?></span></span>
					<?php } else { ?>
						<span class="state unpublish"><span><?php echo Lang::txt('COM_SAML_SESSION_ENDED'); ?></span></span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
				</td>
				<td class="priority-4">
					<?php if ($row->get('ended')) { ?>
						<time datetime="<?php echo $row->get('ended'); ?>"><?php echo $row->get('ended'); ?></time>
					<?php } else { ?>
						&mdash;
					<?php } ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
