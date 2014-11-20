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

$canDo = ServicesHelperPermissions::getActions('service');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_SERVICES') . ': ' . JText::_('COM_SERVICES_SERVICES') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert( '<?php echo JText::_('COM_SERVICES_ERROR_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-category"><?php echo JText::_('COM_SERVICES_FIELD_CATEGORY'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[category]" id="field-category" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_SERVICES_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_SERVICES_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_SERVICES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
				<span class="hint"><?php echo JText::_('COM_SERVICES_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_SERVICES_FIELD_DESCRIPTION'); ?>:</label><br />
				<textarea name="fields[description]" id="field-description" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_SERVICES_UNITS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-currency"><?php echo JText::_('COM_SERVICES_FIELD_CURRENCY'); ?>:</label><br />
				<input type="text" name="fields[currency]" id="field-currency" maxlength="10" value="<?php echo $this->escape(stripslashes($this->row->currency)); ?>" />
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-unitprice"><?php echo JText::_('COM_SERVICES_FIELD_UNITPRICE'); ?>:</label><br />
					<input type="text" name="fields[unitprice]" id="field-unitprice" value="<?php echo $this->escape(stripslashes($this->row->unitprice)); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-pointsprice"><?php echo JText::_('COM_SERVICES_FIELD_POINTSPRICE'); ?>:</label><br />
					<input type="text" name="fields[pointsprice]" id="field-pointsprice" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->pointsprice)); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-minunits"><?php echo JText::_('COM_SERVICES_FIELD_MINUNITS'); ?>:</label><br />
					<input type="text" name="fields[minunits]" id="field-minunits" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->minunits)); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-maxunits"><?php echo JText::_('COM_SERVICES_FIELD_MAXUNITS'); ?>:</label><br />
					<input type="text" name="fields[maxunits]" id="field-maxunits" value="<?php echo $this->escape(stripslashes($this->row->maxunits)); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-unitsize"><?php echo JText::_('COM_SERVICES_FIELD_UNITSIZE'); ?>:</label><br />
					<input type="text" name="fields[unitsize]" id="field-unitsize" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->unitsize)); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-unitmeasure"><?php echo JText::_('COM_SERVICES_FIELD_UNITMEASURE'); ?>:</label><br />
					<input type="text" name="fields[unitmeasure]" id="field-unitmeasure" value="<?php echo $this->escape(stripslashes($this->row->unitmeasure)); ?>" />
				</div>
			</div>
			<div class="clr"></div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_SERVICES_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->id); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<input class="option" type="checkbox" name="fields[restricted]" id="field-restricted" value="1"<?php if ($this->row->restricted) { echo ' checked="checked"'; } ?> />
				<label for="field-restricted"><?php echo JText::_('COM_SERVICES_FIELD_RESTRICTED'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-status"><?php echo JText::_('COM_SERVICES_FIELD_STATUS'); ?>:</label><br />
				<select name="fields[status]" id="field-status">
					<option value="0"<?php if ($this->row->status == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->status == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->status == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
