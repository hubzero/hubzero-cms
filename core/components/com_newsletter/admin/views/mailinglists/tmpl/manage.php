<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('mailinglist');

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $this->list->name, 'list');
if ($canDo->get('core.edit'))
{
	Toolbar::addNew('addemail', 'COM_NEWSLETTER_TOOLBAR_ADDEMAILS');
	Toolbar::deleteList('COM_NEWSLETTER_MAILINGLIST_DELETE_EMAILS_CHECK', 'deleteemail', 'COM_NEWSLETTER_TOOLBAR_REMOVE');
	Toolbar::spacer();
}
Toolbar::custom('export', 'export', '', 'COM_NEWSLETTER_TOOLBAR_EXPORT', false);
Toolbar::spacer();
Toolbar::cancel();

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS'); ?>:</label>
		<select name="status">
			<option value="all" <?php if ($this->filters['status'] == 'all') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ALL'); ?></option>
			<option value="active" <?php if ($this->filters['status'] == 'active') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ACTIVE'); ?></option>
			<option value="removed" <?php if ($this->filters['status'] == 'removed') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_REMOVED'); ?></option>
			<option value="unsubscribed" <?php if ($this->filters['status'] == 'unsubscribed') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_UNSUBSCRIBED'); ?></option>
			<option value="inactive" <?php if ($this->filters['status'] == 'inactive') { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_INACTIVE'); ?></option>
		</select>
		<input type="submit" value="<?php echo Lang::txt('Go'); ?>" id="btn-manage" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_CONFIRMED'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_ADDED', 'date_added', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_CONFIRMED', 'date_confirmed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
				// initiate paging
				echo $this->list_emails->pagination;
				$k = 0;

				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->list_emails) > 0) { ?>
				<?php foreach ($this->list_emails as $le) { ?>
					<tr>
						<td width="30">
							<input type="checkbox" name="email_id[]" id="cb<?php echo $k;?>" value="<?php echo $le->id; ?>" class="checkbox-toggle" />
						</td>
						<td>
							<a href="mailto:<?php echo $le->email; ?>"><?php echo $this->escape($le->email); ?></a>
							<?php
								if ($le->unsubscribe->reason)
								{
									echo '<p><strong>' . Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_UNSUBSCRIBE_REASON') . '</strong> ' . $le->unsubscribe->reason . '</p>';
								}
							?>
						</td>
						<td>
							<?php echo ucfirst($le->status); ?>
						</td>
						<td>
							<?php
								if ($le->confirmed)
								{
									echo Lang::txt('JYES');
								}
								else
								{
									$resendLink = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=sendconfirmation&id='.$le->id.'&mid='.$this->list->id);
									echo Lang::txt('JNO') . '(<a href="'.$resendLink.'">' . Lang::txt('Send Confirmation') . '</a>)';
								}
							?>
						</td>
						<td>
							<time datetime="<?php echo $le->date_added; ?>"><?php echo Date::of($le->date_added)->format('l, F d, Y @ g:ia'); ?></time>
						</td>
						<td>
							<?php
								if ($le->date_confirmed && $le->date_confirmed != '0000-00-00 00:00:00')
								{
									echo '<time datetime="' . $le->date_confirmed . '">' . Date::of($le->date_confirmed)->format('l, F d, Y @ g:ia') . '</time>';
								}
								else
								{
									echo Lang::txt('NA');
								}
							 ?>
						</td>
					</tr>
				<?php $k++; } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6">
						<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_EMAILS', "javascript:submitbutton('addemail');"); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="id[]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="mid" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
