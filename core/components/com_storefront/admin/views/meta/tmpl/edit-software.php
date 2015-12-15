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

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_PRODUCT_META') . ': ' . $text, 'storefront.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('category');

// Setup default values
if (empty($this->meta->eulaRequired))
{
	$this->meta->eulaRequired = 0;
}

if (empty($this->meta->eula))
{
	$this->meta->eula = '';
}

if (empty($this->meta->globalDownloadLimit))
{
	$this->meta->globalDownloadLimit = '';
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo 'Software download options'; ?></span></legend>

			<div class="input-wrap">
				<label for="eulaRequired"><?php echo 'Is EULA Required?'; ?>:</label>
				<select name="fields[eulaRequired]" id="eulaRequired">
					<option value="0"<?php if ($this->meta->eulaRequired == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->meta->eulaRequired == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="eula"><?php echo 'EULA (if required, can be overridden on a SKU level)' ?>: </label><br />
				<?php echo $this->editor('fields[eula]', $this->escape(stripslashes($this->meta->eula)), 50, 10, 'eula', array('buttons' => false)); ?>
			</div>

			<div class="input-wrap">
				<label for="field-globalDownloadLimit"><?php echo 'Total Downloads Limit'; ?>:</label><br />
				<input type="text" name="fields[globalDownloadLimit]" id="field-globalDownloadLimit" size="30" maxlength="100" value="<?php echo $this->meta->globalDownloadLimit; ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">

		<table class="meta">
			<tbody>
			<tr>
				<th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT') . ' ' . Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
				<td>
					<?php echo $this->row->getId(); ?>
					<input type="hidden" name="fields[pId]" id="field-id" value="<?php echo $this->escape($this->row->getId()); ?>" />
					<input type="hidden" name="id" id="id" value="<?php echo $this->escape($this->row->getId()); ?>" />
				</td>
			</tr>
			<tr>
				<th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT'); ?>:</th>
				<td>
					<?php echo $this->row->getName(); ?>
				</td>
			</tr>
			</tbody>
		</table>

	</div>
	<div class="clr"></div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
