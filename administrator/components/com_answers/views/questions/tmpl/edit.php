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

$canDo = AnswersHelper::getActions('question');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_ANSWERS_TITLE') . ': ' . JText::_('COM_ANSWERS_QUESTIONS') . ': ' . $text, 'answers.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('question');
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
	if (document.getElementById('q_subject').value == ''){
		alert( 'Question must have a subject' );
	} else if (document.getElementById('q_tags').value == ''){
		alert( 'Question must have at least one tag' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<input type="checkbox" name="question[anonymous]" id="anonymous" value="1" <?php echo ($this->row->get('anonymous')) ? 'checked="checked"' : ''; ?> />
					<label for="anonymous"><?php echo JText::_('COM_ANSWERS_FIELD_ANONYMOUS'); ?></label>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<input type="checkbox" name="question[email]" id="email" value="1" <?php echo ($this->row->get('email')) ? 'checked="checked"' : ''; ?> />
					<label for="email"><?php echo JText::_('COM_ANSWERS_FIELD_NOTIFY'); ?></label>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="q_subject"><?php echo JText::_('COM_ANSWERS_FIELD_SUBJECT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="question[subject]" id="q_subject" size="30" maxlength="250" value="<?php echo $this->escape($this->row->subject('raw')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-question"><?php echo JText::_('COM_ANSWERS_FIELD_QUESTION'); ?>:</label><br />
				<?php echo JFactory::getEditor()->display('question[question]', $this->escape($this->row->content('raw')), '', '', 50, 15, false, 'field-question', null, null, array('class' => 'minimal no-footer')); ?>
			</div>

			<div class="input-wrap">
				<label for="q_tags"><?php echo JText::_('COM_ANSWERS_FIELD_TAGS'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<textarea name="question[tags]" id="q_tags" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_ANSWERS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id', 0); ?>
						<input type="hidden" name="question[id]" value="<?php echo $this->row->get('id'); ?>" />
					</td>
				</tr>
			<?php if ($this->row->get('id')) { ?>
				<tr>
					<th><?php echo JText::_('COM_ANSWERS_FIELD_CREATED'); ?>:</th>
					<td><?php echo $this->row->get('created'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ANSWERS_FIELD_CREATOR'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->row->creator('name'))); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_ANSWERS_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<label for="created_by"><?php echo JText::_('COM_ANSWERS_FIELD_CREATOR'); ?>:</label><br />
				<input type="text" name="question[created_by]" id="created_by" size="25" maxlength="50" value="<?php echo $this->row->get('created_by', JFactory::getUser()->get('id')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="created"><?php echo JText::_('COM_ANSWERS_FIELD_CREATED'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $this->row->get('created', JFactory::getDate()->toSql()), 'question[created]', 'created', 'Y-m-d H:i:s', array('class' => 'calendar-field')); ?></td>
			</div>

			<div class="input-wrap">
				<label for="state"><?php echo JText::_('COM_ANSWERS_FIELD_STATE'); ?>:</label><br />
				<select name="question[state]" id="state">
					<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_STATE_OPEN'); ?></option>
					<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_ANSWERS_STATE_CLOSED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
