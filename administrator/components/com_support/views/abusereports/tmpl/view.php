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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_SUPPORT_TICKETS') . ': ' . JText::_('COM_SUPPORT_ABUSE_REPORTS'), 'support.png');
JToolBarHelper::save();
//JToolBarHelper::cancel();

$reporter = JUser::getInstance($this->report->created_by);

$link = '';

if (is_object($this->reported))
{
	$author = JUser::getInstance($this->reported->author);

	if (is_object($author) && $author->get('username'))
	{
		$this->title .= $author->get('username');
	}
	else
	{
		$this->title .= JText::_('COM_SUPPORT_UNKNOWN');
	}
	$this->title .= ($this->reported->anon) ? '(' . JText::_('COM_SUPPORT_ANONYMOUS') . ')':'';

	$link = str_replace('/administrator', '', $this->reported->href);
}

JHTML::_('behavior.modal', 'a.modals');

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

<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_SUPPORT_REPORT_ITEM_REPORTED_AS_ABUSIVE'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<h4><?php echo '<a class="modals" href="' . $link . '">'.$this->escape($this->title) . '</a>: '; ?></h4>
							<p><?php echo (is_object($this->reported)) ? stripslashes($this->reported->text) : ''; ?></p>
							<?php if (is_object($this->reported) && isset($this->reported->subject) && $this->reported->subject!='') {
								echo '<p>' . $this->escape(stripslashes($this->reported->subject)) . '</p>';
							} ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_SUPPORT_COL_DATE'); ?></th>
					<td><?php echo $this->report->created; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_SUPPORT_REPORT_REPORTED_BY'); ?></th>
					<td><?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : JText::_('COM_SUPPORT_UNKNOWN'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_SUPPORT_COL_REASON'); ?></th>
					<td><?php echo $this->escape(stripslashes($this->report->report ? $this->report->report : $this->report->subject)); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_SUPPORT_REPORT_TAKE_ACTION'); ?></span></legend>

			<?php if ($this->report->state == 0) { ?>
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_SUPPORT_REPORT_RELEASE_ITEM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-release" value="release" />
					<label for="field-task-release"><?php echo JText::_('COM_SUPPORT_REPORT_RELEASE_ITEM'); ?></label>
				</div>

				<div class="input-wrap" data-hint="<?php echo JText::_('COM_SUPPORT_REPORT_MARK_AS_SPAM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-spam" value="spam" />
					<label for="field-task-spam"><?php echo JText::_('COM_SUPPORT_REPORT_MARK_AS_SPAM'); ?></label>
				</div>

				<div class="input-wrap" data-hint="<?php echo JText::_('COM_SUPPORT_REPORT_DELETE_ITEM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-remove" value="remove" />
					<label for="field-task-remove"><?php echo JText::_('COM_SUPPORT_REPORT_DELETE_ITEM'); ?></label>
					<span class="hint"><?php echo JText::_('COM_SUPPORT_REPORT_DELETE_ITEM_HINT'); ?></span><br />
					<textarea name="note" id="note" rows="5" cols="25"></textarea>
				</div>

				<div class="input-wrap">
					<input type="radio" name="task" value="cancel" id="field-task-cancel" checked="checked" />
					<label for="field-task-cancel"><?php echo JText::_('COM_SUPPORT_REPORT_DECIDE_LATER'); ?></label>
				</div>
			<?php } else { ?>
				<p class="warning"><?php echo JText::_('COM_SUPPORT_REPORT_ACTION_TAKEN'); ?></p>
				<input type="hidden" name="task" value="view" />
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->report->id; ?>" />
	<input type="hidden" name="parentid" value="<?php echo $this->parentid; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
