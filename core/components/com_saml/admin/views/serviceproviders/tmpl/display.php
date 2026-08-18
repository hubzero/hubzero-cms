<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SAML') . ': ' . Lang::txt('COM_SAML_SERVICE_PROVIDERS'), 'saml');
if (User::authorise('core.admin', $this->option))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if (User::authorise('core.edit.state', $this->option))
{
	Toolbar::publishList('publish', 'COM_SAML_ENABLE');
	Toolbar::unpublishList('unpublish', 'COM_SAML_DISABLE');
	Toolbar::spacer();
}
if (User::authorise('core.create', $this->option))
{
	Toolbar::addNew();
	Toolbar::custom('import', 'copy', '', 'COM_SAML_IMPORT_METADATA', false);
}
if (User::authorise('core.edit', $this->option))
{
	Toolbar::editList();
}
if (User::authorise('core.delete', $this->option))
{
	Toolbar::deleteList('COM_SAML_CONFIRM_DELETE');
}

$now = time();
$certWarn = 30 * 24 * 60 * 60;
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('JSTATUS'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php echo ($this->filters['state'] < 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_ALL_STATES'); ?></option>
					<option value="1"<?php echo ($this->filters['state'] === 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_STATE_ENABLED'); ?></option>
					<option value="0"<?php echo ($this->filters['state'] === 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_STATE_DISABLED'); ?></option>
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
				<th scope="col"><?php echo Html::grid('sort', 'COM_SAML_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_SAML_COL_ENTITY_ID', 'entity_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_SAML_COL_ACS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_SAML_COL_CERT_EXPIRES'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'JSTATUS', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
			if ($row->isEnabled())
			{
				$class = 'publish';
				$task = 'unpublish';
				$alt = Lang::txt('COM_SAML_STATE_ENABLED');
			}
			else
			{
				$class = 'unpublish';
				$task = 'publish';
				$alt = Lang::txt('COM_SAML_STATE_DISABLED');
			}

			$cert = $row->certificateInfo();
			$acsHost = parse_url($row->get('acs_url'), PHP_URL_HOST);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('id'); ?></label>
				</td>
				<td>
					<?php if (User::authorise('core.edit', $this->option)) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<span><?php echo $this->escape(stripslashes($row->get('name'))); ?></span>
						</a>
					<?php } else { ?>
						<span><?php echo $this->escape(stripslashes($row->get('name'))); ?></span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<code><?php echo $this->escape($row->get('entity_id')); ?></code>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($acsHost ?: $row->get('acs_url')); ?>
				</td>
				<td class="priority-4">
					<?php if ($cert && $cert['valid_to']) { ?>
						<?php if ($cert['valid_to'] < $now) { ?>
							<span class="error"><?php echo Lang::txt('COM_SAML_CERT_EXPIRED'); ?></span>
						<?php } elseif ($cert['valid_to'] < ($now + $certWarn)) { ?>
							<span class="warning"><?php echo Date::of($cert['valid_to'])->toLocal('Y-m-d'); ?></span>
						<?php } else { ?>
							<time datetime="<?php echo gmdate('Y-m-d\TH:i:s\Z', $cert['valid_to']); ?>"><?php echo Date::of($cert['valid_to'])->toLocal('Y-m-d'); ?></time>
						<?php } ?>
					<?php } else { ?>
						<span class="warning"><?php echo Lang::txt('COM_SAML_CERT_NONE'); ?></span>
					<?php } ?>
				</td>
				<td>
					<?php if (User::authorise('core.edit.state', $this->option)) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_SAML_SET_STATE', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
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
