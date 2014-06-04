<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
JToolBarHelper::title(JText::_( 'Newsletter Mailing List [' . $this->list->name .']' ), 'list.png');

//add buttons to toolbar
JToolBarHelper::addNew('addemail', 'Add Emails');
JToolBarHelper::deleteList('Are you sure you want to delete the selected email(s) from the mailing list?', 'deleteemail', 'Remove');
JToolBarHelper::spacer();
JToolBarHelper::custom('export', 'export', '', 'Export List', false);
JToolBarHelper::spacer();
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<label>Status: 
			<select name="status">
				<option value="all" <?php if ($this->filters['status'] == 'all') { echo 'selected="selected"'; } ?>>All Statuses</option>
				<option value="active" <?php if ($this->filters['status'] == 'active') { echo 'selected="selected"'; } ?>>Active</option>
				<option value="removed" <?php if ($this->filters['status'] == 'removed') { echo 'selected="selected"'; } ?>>Removed by Admin</option>
				<option value="unsubscribed" <?php if ($this->filters['status'] == 'unsubscribed') { echo 'selected="selected"'; } ?>>Unsubscribed</option>
				<option value="inactive" <?php if ($this->filters['status'] == 'inactive') { echo 'selected="selected"'; } ?>>Inactive (Awaiting Confirmation)</option>
			</select>
		</label>
		<input type="submit" value="Go" onclick="javascript:submitbutton('manage');" />
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->list_emails); ?>);" /></th>
				<th>
					<?php if ($this->filters['sort'] == 'email ASC') : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=email DESC">
							<?php echo JText::_('Email &uarr;'); ?>
						</a>
					<?php else : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=email ASC">
							<?php echo JText::_('Email'); ?>
							<?php echo ($this->filters['sort'] == 'email DESC') ? ' &darr;' : ''; ?>
						</a>
					<?php endif; ?>
				</th>
				<th><?php echo JText::_('Status'); ?></th>
				<th><?php echo JText::_('Confirmed?'); ?></th>
				<th>
					<?php if ($this->filters['sort'] == 'date_added ASC') : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=date_added DESC">
							<?php echo JText::_('Date Added &uarr;'); ?>
						</a>
					<?php else : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=date_added ASC">
							<?php echo JText::_('Date Added'); ?>
							<?php echo ($this->filters['sort'] == 'date_added DESC') ? ' &darr;' : ''; ?>
						</a>
					<?php endif; ?>
				</th>
				<th>
					<?php if ($this->filters['sort'] == 'date_confirmed ASC') : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=date_confirmed DESC">
							<?php echo JText::_('Date Confirmed &uarr;'); ?>
						</a>
					<?php else : ?>
						<a href="index.php?option=com_newsletter&amp;controller=mailinglist&amp;task=manage&amp;id[]=<?php echo $this->list->id; ?>&amp;status=<?php echo $this->filters['status']; ?>&amp;sort=date_confirmed ASC">
							<?php echo JText::_('Date Confirmed'); ?>
							<?php echo ($this->filters['sort'] == 'date_confirmed DESC') ? ' &darr;' : ''; ?>
						</a>
					<?php endif; ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->list_emails) > 0) : ?>
				<?php foreach($this->list_emails as $k => $le) : ?>
					<tr>
						<td width="30">
							<input type="checkbox" name="email_id[]" id="cb<?php echo $k;?>" value="<?php echo $le->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<a href="mailto:<?php echo $le->email; ?>"><?php echo $le->email; ?></a>
							<?php
								if ($le->unsubscribe_reason)
								{
									echo '<p><strong>Unsubscribe Reason:</strong> ' . $le->unsubscribe_reason . '</p>';
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
									echo 'Yes';
								}
								else
								{
									$resendLink = JRoute::_('index.php?option=com_newsletter&controller=mailinglist&task=sendconfirmation&id='.$le->id.'&mid='.$this->list->id);
									echo 'No (<a href="'.$resendLink.'">Send Confirmation</a>)';
								}
							?>
						</td>
						<td>
							<?php echo date('F, l d, Y @ g:ia', strtotime($le->date_added)); ?>
						</td>
						<td>
							<?php
								if($le->date_confirmed && $le->date_confirmed != '0000-00-00 00:00:00')
								{
									echo date('F, l d, Y @ g:ia', strtotime($le->date_confirmed));
								}
								else
								{
									echo 'n/a';
								}
							 ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6">
						Currently there are no emails in this mailing list. <a onclick="javascript:submitbutton('addemail');" href="javascript:void(0);">Click here to add emails.</a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="id[]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>