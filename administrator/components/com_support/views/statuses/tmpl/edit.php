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
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_SUPPORT_TICKETS') . ': ' . JText::_('COM_SUPPORT_STATUS') . ': ' . $text, 'support.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();

$this->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick.js', 'system');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_SUPPORT_STATUS_ERROR_NO_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}

jQuery(document).ready(function($){
	var col = $('#field-color');

	col.colpick({
		layout: 'hex',
		colorScheme: 'dark',
		submit: 1,
		onSubmit: function(hsb,hex,rgb,el) {
			col.val(hex);
		}
		/*onChange:function(hsb,hex,rgb,el,bySetColor) {
			col.val(hex);
		}*/
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_SUPPORT_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_SUPPORT_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo JText::_('COM_SUPPORT_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-open"><?php echo JText::_('COM_SUPPORT_FIELD_FOR'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
					<select name="fields[open]" id="field-open">
						<option value="1"<?php if ($this->row->get('open') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_FIELD_FOR_OPEN'); ?></option>
						<option value="0"<?php if ($this->row->get('open') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_FIELD_FOR_CLOSED'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-color"><?php echo JText::_('Color'); ?>:</label>
					<input type="text" name="fields[color]" id="field-color" value="<?php echo $this->escape($this->row->get('color')); ?>" />
				</div>
			</div>
			<div class="clr"></div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_SUPPORT_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id'); ?>
						<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
