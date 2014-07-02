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
JToolBarHelper::title(JText::_('COM_PUBLICATIONS_LICENSE') . ': [ ' . $text . ' ]', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_LICENSE_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_PUBLICATIONS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="field-title" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_LICENSE_NAME_HINT'); ?>">
				<label for="field-name"><?php echo JText::_('COM_PUBLICATIONS_FIELD_NAME'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[name]" id="field-name" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
				<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_NAME_HINT'); ?></span>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_LICENSE_URL_HINT'); ?>">
				<label for="field-url"><?php echo JText::_('COM_PUBLICATIONS_FIELD_URL'); ?>:</label>
				<input type="text" name="fields[url]" id="field-url" maxlength="100" value="<?php echo $this->escape($this->row->url); ?>" />
				<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_URL_HINT'); ?></span>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_LICENSE_DESC_HINT'); ?>">
				<label for="field-info"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ABOUT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<textarea name="fields[info]" id="field-info" cols="40" rows="10"><?php echo $this->row->info; ?></textarea>
				<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_DESC_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-text"><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_CONTENT'); ?>:</label></td>
				<textarea name="fields[text]" id="field-text" cols="40" rows="10"><?php echo $this->row->text; ?></textarea>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_FIELD_ICON_HINT'); ?>">
				<label for="field-icon"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ICON'); ?>:</label>
				<input type="text" name="fields[icon]" id="field-icon" value="<?php echo $this->escape($this->row->icon); ?>" />
				<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ICON_HINT'); ?></span>
			</div>

			<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID'); ?></th>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_DEFAULT'); ?></th>
					<td><?php echo $this->row->main == 1 ? JText::_('COM_PUBLICATIONS_LICENSE_YES') : JText::_('COM_PUBLICATIONS_LICENSE_NO') ; ?></td>
				</tr>
			<?php if ($this->row->id) { ?>
				<tr>
					<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_ORDERING'); ?></th>
					<td><?php echo $this->row->ordering; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_LICENSE_CONFIGURATION'); ?></span></legend>

			<fieldset>
				<legend><?php echo JText::_('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN'); ?>">
					<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN'); ?></span>

					<input class="option" name="active" id="field-active1" type="radio" value="1" <?php echo $this->row->active == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-active1"><?php echo JText::_('JYES'); ?></label>
					<br />
					<input class="option" name="active" id="field-active0" type="radio" value="0" <?php echo $this->row->active == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-active0"><?php echo JText::_('JNO'); ?></label>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE_HINT'); ?>">
					<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CUSTOMIZABLE_HINT'); ?></span>

					<input class="option" name="customizable" id="field-customizable1" type="radio" value="1" <?php echo $this->row->customizable == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-customizable1"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?></label>
					<br />
					<input class="option" name="customizable" id="field-customizable0" type="radio" value="0" <?php echo $this->row->customizable == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-customizable0"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?></label>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('Agreement required'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('Do we require publication authors to agree to license terms?'); ?>">
					<span class="hint"><?php echo JText::_('Do we require publication authors to agree to license terms?'); ?></span>

					<input class="option" name="agreement" id="field-agreement1" type="radio" value="1" <?php echo $this->row->agreement == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-agreement1"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?></label>
					<br />
					<input class="option" name="agreement" id="field-agreement0" type="radio" value="0" <?php echo $this->row->agreement == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-agreement0"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?></label>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('Apps only'); ?></legend>

				<div class="input-wrap" data-hint="<?php echo JText::_('Is this license applicable to apps publications only?'); ?>">
					<span class="hint"><?php echo JText::_('Is this license applicable to apps publications only?'); ?></span>

					<input class="option" name="apps_only" id="field-apps_only1" type="radio" value="1" <?php echo $this->row->apps_only == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-apps_only1"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?></label>
					<br />
					<input class="option" name="apps_only" id="field-apps_only0" type="radio" value="0" <?php echo $this->row->apps_only == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-apps_only0"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?></label>
				</div>
			</fieldset>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>