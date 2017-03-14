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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		<input type="submit" value="<?php echo Lang::txt('Go'); ?>" onclick="javascript:submitbutton('manage');" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->list_emails); ?>);" /></th>
				<th>
					<?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS'); ?></th>
				<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_CONFIRMED'); ?></th>
				<th>
					<?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_ADDED', 'date_added', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_CONFIRMED', 'date_confirmed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->list_emails) > 0) : ?>
				<?php foreach ($this->list_emails as $k => $le) : ?>
					<tr>
						<td width="30">
							<input type="checkbox" name="email_id[]" id="cb<?php echo $k;?>" value="<?php echo $le->id; ?>" onclick="isChecked(this.checked);" />
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
							<?php echo Date::of($le->date_added)->format('l, F d, Y @ g:ia'); ?>
						</td>
						<td>
							<?php
								if ($le->date_confirmed && $le->date_confirmed != '0000-00-00 00:00:00')
								{
									echo Date::of($le->date_confirmed)->format('l, F d, Y @ g:ia');
								}
								else
								{
									echo Lang::txt('NA');
								}
							 ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6">
						<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_EMAILS',"javascript:submitbutton('addemail');"); ?>
					</td>
				</tr>
			<?php endif; ?>
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