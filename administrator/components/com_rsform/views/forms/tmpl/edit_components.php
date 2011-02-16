<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table border="0" width="100%" class="adminrsform">
	<tr>
		<td class="components" valign="top">
			<p><strong><?php echo JText::_('RSFP_FORM_FIELDS'); ?></strong></p>
			<?php $this->triggerEvent('rsfp_onBeforeShowComponents');?>
			<a href="javascript:void(0);" onclick="displayTemplate('1');return false;" class="component" id="textfield"><?php echo JText::_('RSFP_COMP_TEXTBOX');?></a>
			<div title="componentEdit" id="componentEdit1" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('2');return false;" class="component" id="textarea"><?php echo JText::_('RSFP_COMP_TEXTAREA');?></a>
			<div title="componentEdit" id="componentEdit2" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('3');return false;" class="component" id="select"><?php echo JText::_('RSFP_COMP_DROPDOWN');?></a>
			<div title="componentEdit" id="componentEdit3" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('4');return false;" class="component" id="check"><?php echo JText::_('RSFP_COMP_CHECKBOX');?></a>
			<div title="componentEdit" id="componentEdit4" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('5');return false;" class="component" id="radio"><?php echo JText::_('RSFP_COMP_RADIO');?></a>
			<div title="componentEdit" id="componentEdit5" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('13');return false;" class="component" id="button"><?php echo JText::_('RSFP_COMP_SUBMITBUTTON');?></a>
			<div title="componentEdit" id="componentEdit13" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('14');return false;" class="component" id="password"><?php echo JText::_('RSFP_COMP_PASSWORD');?></a>
			<div title="componentEdit" id="componentEdit14" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('9');return false;" class="component" id="upload"><?php echo JText::_('RSFP_COMP_FILE');?></a>
			<div title="componentEdit" id="componentEdit9" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('10');return false;" class="component" id="freetext"><?php echo JText::_('RSFP_COMP_FREETEXT');?></a>
			<div title="componentEdit" id="componentEdit10" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('6');return false;" class="component" id="calendar"><?php echo JText::_('RSFP_COMP_CALENDAR');?></a>
			<div title="componentEdit" id="componentEdit6" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('7');return false;" class="component" id="button"><?php echo JText::_('RSFP_COMP_BUTTON');?></a>
			<div title="componentEdit" id="componentEdit7" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('12');return false;" class="component" id="image"><?php echo JText::_('RSFP_COMP_IMAGEBUTTON');?></a>
			<div title="componentEdit" id="componentEdit12" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('8');return false;" class="component" id="captcha"><?php echo JText::_('RSFP_COMP_CAPTCHA');?></a>
			<div title="componentEdit" id="componentEdit8" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('11');return false;" class="component" id="hidden"><?php echo JText::_('RSFP_COMP_HIDDEN');?></a>
			<div title="componentEdit" id="componentEdit11" class="componentEdit"></div>

			<a href="javascript:void(0);" onclick="displayTemplate('15');return false;" class="component" id="ticket"><?php echo JText::_('RSFP_COMP_TICKET');?></a>
			<div title="componentEdit" id="componentEdit15" class="componentEdit"></div>

			<!-- Multipage -->
			<p><strong><?php echo JText::_('RSFP_MULTIPAGE'); ?></strong></p>
			<a href="javascript:void(0);" onclick="displayTemplate('41');return false;" class="component" id="pagebreak"><?php echo JText::_('RSFP_PAGE_BREAK'); ?></a>
			<div title="componentEdit" id="componentEdit41" class="componentEdit"></div>
			
			<?php $this->triggerEvent('rsfp_bk_onAfterShowComponents'); ?>
			
			<input type="hidden" name="componentIdToEdit" id="componentIdToEdit" value="-1" />
			<input type="hidden" name="componentEditForm" id="componentEditForm" value="-1" />
		</td>
		<td valign="top" class="componentPreview">
			<div class="rsform_error" id="rsform_layout_msg" <?php if ($this->form->FormLayoutAutogenerate) { ?>style="display: none"<?php } ?>>
				<?php echo JText::_('RSFP_AUTOGENERATE_LAYOUT_DISABLED'); ?>
			</div>
			<div class="rsform_error" id="rsform_submit_button_msg" <?php if ($this->hasSubmitButton) { ?>style="display: none"<?php } ?>>
				<img src="components/com_rsform/assets/images/submit-help.jpg" alt="" /> <?php echo JText::_('RSFP_NO_SUBMIT_BUTTON'); ?>
			</div>
				<table border="0" id="componentPreview" class="adminlist">
					<thead>
					<tr>
						<th class="title" width="1"><input type="hidden" value="-2" name="previewComponentId"/><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->fields); ?>);"/></th>
						<th class="title"><?php echo JText::_('NAME');?></th>
						<th class="title"><?php echo JText::_('RSFP_CAPTION');?></th>
						<th class="title"><?php echo JText::_('PREVIEW');?></th>
						<th class="title" width="5"><?php echo JText::_('EDIT');?></th>
						<th class="title" width="5"><?php echo JText::_('DELETE');?></th>
						<th width="100"><?php echo JText::_('Ordering'); ?> <?php echo JHTML::_('grid.order',$this->fields); ?></th>
						<th class="title" width="5"><?php echo JText::_('PUBLISHED');?></th>
						<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_REQUIRED');?></th>
						<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_VALIDATIONRULE');?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$i = 0;
					$k = 0;
					$n = count($this->fields);
					// hack to show order down icon
					$n++;
					foreach ($this->fields as $field) { ?>
					<tr class="row<?php echo $k; ?><?php if ($field->type_id == 41) { ?> rsform_page<?php } ?>">
						<td><input type="hidden" name="previewComponentId" value="<?php echo $field->id; ?>" /><?php echo JHTML::_('grid.id', $i, $field->id); ?></td>
						<td><?php echo $field->name; ?></td>
						<?php echo $field->preview; ?>
						<td align="center"><span class="hasTip" title="<?php echo JText::_('RSFP_EDIT_COMPONENT'); ?>"><a href="javascript:void(0);" onclick="displayTemplate('<?php echo $field->type_id; ?>','<?php echo $field->id; ?>');"><img src="components/com_rsform/assets/images/icons/edit.png" border="0" width="16" height="16" alt="<?php echo JText::_('RSFP_EDIT_COMPONENT'); ?>" /></a></span></td>
						<td align="center"><span class="hasTip" title="<?php echo JText::_('RSFP_REMOVE_COMPONENT'); ?>"><a href="javascript:void(0);" onclick="if (confirm('<?php echo JText::sprintf('RSFP_REMOVE_COMPONENT_CONFIRM', $field->name); ?>')) removeComponent('<?php echo $this->form->FormId; ?>','<?php echo $field->id; ?>');"><img src="components/com_rsform/assets/images/icons/remove.png" border="0" width="16" height="16" alt="<?php echo JText::_('RSFP_REMOVE_COMPONENT'); ?>" /></a></span></td>
						<td class="order">
							<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering'); ?></span>
							<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' ); ?></span>
							<input type="text" name="order[]" size="5" value="<?php echo $field->ordering; ?>" disabled="disabled" class="text_area" style="text-align:center" />
						</td>
						<td align="center"><?php echo JHTML::_('grid.published', $field, $i, 'tick.png', 'publish_x.png', 'components'); ?></td>
						<td align="center"><?php echo $field->required; ?></td>
						<td align="center"><?php echo $field->validation; ?></td>
					</tr>
					<?php
					$i++;
					$k=1-$k;
					}
					?>
					</tbody>
				</table>
		</td>
	</tr>
</table>