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

// get meta
$skuMeta = $this->row->getMeta();

$inventoryNotificationThreshold = '';
if (isset($skuMeta['inventoryNotificationThreshold']) && !empty($skuMeta['inventoryNotificationThreshold']))
{
	$inventoryNotificationThreshold = $skuMeta['inventoryNotificationThreshold'];
}

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_SKU') . ': ' . $text, 'storefront.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

$this->css();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert("<?php echo 'Title cannot be empty' ?>");
	}
	<?php
	if (0 && $this->pInfo->ptModel == 'software')
	{
	?>
	else if (document.getElementById('field-download-file').value == ''){
		alert("<?php echo 'Download file cannot be empty' ?>");
	}
	<?php
	}
	?>
	else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[sSku]" id="field-title" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getName())); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_PRICE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[sPrice]" id="field-title" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getPrice())); ?>" />
			</div>

			<?php
			if ($this->pInfo->ptId == 1) {
			?>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_WEIGHT'); ?>:</label><br/>
					<input type="text" name="fields[pWeight]" id="field-title" size="30" maxlength="100"
						   value="<?php echo $this->escape(stripslashes($this->row->getWeight())); ?>"/>
				</div>
			<?php
			}
			?>
		</fieldset>

		<?php
		if (!empty($this->allOptions))
		{
		?>
		<fieldset class="adminform">
			<legend><span><?php echo 'Product options'; ?></span></legend>

			<?php
			foreach ($this->allOptions as $optionGroup)
			{
			?>
				<div class="input-wrap">
					<label for="field-options-<?php echo $optionGroup->ogId; ?>"><?php echo $optionGroup->ogName; ?>:<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />

					<?php
					// First check if there are any options to display
					$optionsToDisplay = false;
					foreach ($optionGroup->options as $option)
					{
						if ($option->oActive || in_array($option->oId, $this->options))
						{
							$optionsToDisplay = true;
						}
					}
					?>

					<?php
					if ($optionsToDisplay)
					{
					?>
						<select name="fields[options][]" id="field-options-<?php echo $optionGroup->ogId; ?>">
							<option value="">-- please select an option --</option>
						<?php
						foreach ($optionGroup->options as $option)
						{
							if ($option->oActive || in_array($option->oId, $this->options))
							{
							?>
							<option value="<?php echo $option->oId; ?>"<?php if (in_array($option->oId, $this->options)) {
								echo ' selected="selected"';
							} ?>><?php echo $option->oName ?></option>
							<?php
							}
						}
						?>
						</select>
					<?php
					}
					else
					{
					?>
						<p class="warning">There are currently no available options for this option group. Please go to the <a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=options&task=display&id=' . $optionGroup->ogId); ?>" title="<?php echo Lang::txt('Edit option group'); ?>"><?php echo $optionGroup->ogName; ?> options administration</a> and make sure to create new or enable existing options.</p>
					<?php
					}
					?>
				</div>
			<?php
			}
			?>
		</fieldset>
		<?php
		}
		?>

		<?php
		// Product type specific meta options

		if ($this->pInfo->ptModel == 'software')
		{
			$view = new \Hubzero\Component\View(array('name'=>'meta', 'layout' => 'sku-software'));
			$view->parent = $this;
			$view->skuMeta = $skuMeta;
			$view->display();
		}

		?>

	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
					<td>
						<?php echo $this->row->getId(); ?>
						<input type="hidden" name="fields[sId]" id="field-sid" value="<?php echo $this->escape($this->row->getId()); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT'); ?>:</th>
					<td>
						<?php echo $this->pInfo->pName; ?>
						<input type="hidden" name="pId" id="pid" value="<?php echo $this->escape($this->pInfo->pId); ?>" />
					</td>
				</tr>
				<?php
				if ($this->pInfo->ptModel == 'software')
				{
				?>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_DOWNLOADED'); ?>:</th>
					<td>
						<?php
						echo $this->downloaded;
						if ($this->downloaded == 0 || $this->downloaded > 1)
						{
							echo(' times');
						}
						else
						{
							echo 'time';
						}
						?>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_DIRECT_URL'); ?>:</th>
					<td>
						<?php
						$directUrl = Request::root();
						$directUrl .= 'storefront/product/' . (!empty($this->pInfo->pAlias) ? $this->pInfo->pAlias : $this->pInfo->pId);
						if (!empty($this->options))
						{
							$directUrl .= '/';
							$i = 0;
							foreach ($this->options as $o)
							{
								if ($i)
								{
									$directUrl .= ',';
								}
								$directUrl .= $o;
								$i++;
							}
						}

						echo $directUrl;
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-sAllowMultiple"><?php echo Lang::txt('COM_STOREFRONT_ALLOW_MULTIPLE'); ?>:</label>
				<select name="fields[sAllowMultiple]" id="field-pAllowMultiple">
					<option value="0"<?php if ($this->row->getAllowMultiple() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->row->getAllowMultiple() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>

			<?php
			$showInventoryOptions = true;
			if ($this->pInfo->ptModel == 'software' && isset($skuMeta['serialManagement']) && $skuMeta['serialManagement'] == 'multiple')
			{
				$showInventoryOptions = false;
			}
			if ($showInventoryOptions)
			{
			?>

			<div class="input-wrap" data-hint="<?php echo 'Should the inventory level be kept tracked? If yes set the inventory.'; ?>">
				<label for="field-sTrackInventory"><?php echo 'Track Inventory'; ?>:</label>
				<select name="fields[sTrackInventory]" id="field-sTrackInventory">
					<option value="0"<?php if ($this->row->getTrackInventory() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->row->getTrackInventory() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>

			<div class="input-wrap" data-hint="<?php echo 'Number of items available for sale in the inventory. Non-negative integer.'; ?>">
				<label for="field-inventory"><?php echo 'Inventory'; ?>:</label>
				<input type="text" name="fields[sInventory]" id="field-inventory" size="30" maxlength="10" value="<?php echo $this->row->getInventoryLevel(); ?>" />
			</div>

			<?php
			}
			?>

			<div class="input-wrap" data-hint="<?php echo 'Inventory threshold: when reached or below an email notification is sent to the admin on each inventory change'; ?>">
				<label for="field-inventory-notification-threshold"><?php echo 'Inventory notification threshold'; ?>:</label>
				<input type="text" name="fields[meta][inventoryNotificationThreshold]" id="field-inventory-notification-threshold" size="30" maxlength="10" value="<?php echo $inventoryNotificationThreshold; ?>" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_STATE'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->getActiveStatus() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->getActiveStatus() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_UP'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[publish_up]', ($this->row->getPublishTime()->publish_up != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->getPublishTime()->publish_up)->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_up')); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_DOWN'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[publish_down]', ($this->row->getPublishTime()->publish_down != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->getPublishTime()->publish_down)->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-publish_down')); ?>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('Restrictions'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-restricted"><?php echo Lang::txt('Restrict by users'); ?>:</label>
				<select name="fields[restricted]" id="field-restricted">
					<option value="0"<?php if ($this->row->getRestricted() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->row->getRestricted() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>

			<?php
			if ($this->row->getRestricted()) {
			?>
				<p>
					<a class="options-link" href="<?php echo 'index.php?option=' . $this->option . '&controller=restrictions&id=' . $this->row->getId(); ?>">Manage restrictions</a></p>
			<?php
			}
			?>
		</fieldset>

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

	<?php echo Html::input('token'); ?>
</form>
