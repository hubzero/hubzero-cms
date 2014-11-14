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

$canDo = CollectionsHelperPermissions::getActions('post');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_COLLECTIONS') . ': ' . JText::_('COM_COLLECTIONS_POSTS') . ': ' . $text, 'collection.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('collection');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if ($('#field-item_id').val() == '') {
		alert('<?php echo JText::_('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'); ?>');
	} else if ($('#field-collection_id').val() == '') {
		alert('<?php echo JText::_('COM_COLLECTIONS_ERROR_MISSING_COLLECTION_ID'); ?>');
	} else {
		<?php echo JFactory::getEditor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-item_id"><?php echo JText::_('COM_COLLECTIONS_FIELD_ITEM_ID'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[item_id]" id="field-item_id" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->get('item_id'))); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-collection_id"><?php echo JText::_('COM_COLLECTIONS_FIELD_COLLECTION_ID'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[collection_id]" id="field-collection_id" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->get('collection_id'))); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_COLLECTIONS_FIELD_DESCRIPTION'); ?></label><br />
				<?php echo JFactory::getEditor()->display('fields[description]', $this->escape($this->row->description('raw')), '', '', 35, 10, false, 'field-description', null, null, array('class' => 'minimal no-footer')); ?>
			</div>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape(stripslashes($editor->get('name')));
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo $this->row->get('created'); ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_ORIGINAL'); ?>:</th>
					<td>
						<?php echo ($this->row->get('original') ? JText::_('JYES') : JText::_('JNO')); ?>
						<input type="hidden" name="fields[original]" id="field-original" value="<?php echo $this->escape($this->row->get('original')); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>