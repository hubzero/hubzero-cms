<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('framework', true);
?>
	<label for="showmods"><?php echo Lang::txt('COM_MENUS_ITEM_FIELD_HIDE_UNASSIGNED');?></label>
	<input type="checkbox" id="showmods" />

	<table class="adminlist">
		<thead>
			<tr>
				<th class="left">
					<?php echo Lang::txt('COM_MENUS_HEADING_ASSIGN_MODULE');?>
				</th>
				<th>
					<?php echo Lang::txt('COM_MENUS_HEADING_DISPLAY');?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->modules as $i => &$module) : ?>
			<?php if (is_null($module->menuid)) : ?>
				<?php if (!$module->except || $module->menuid < 0) : ?>
					<tr class="nope row<?php echo $i % 2;?>">
				<?php else : ?>
					<tr class="row<?php echo $i % 2;?>">
				<?php endif; ?>
			<?php else : ?>
				<tr class="row<?php echo $i % 2;?>">
			<?php endif; ?>
				<td>
					<?php $link = Route::url('index.php?option=com_modules&client_id=0&task=edit&id=' . $module->id . '&tmpl=component&view=module&layout=modal'); ?>
					<a class="modal" href="<?php echo $link;?>" rel="{handler: 'iframe', size: {x: 900, y: 550}}" title="<?php echo Lang::txt('COM_MENUS_EDIT_MODULE_SETTINGS');?>">
						<?php echo Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?>
					</a>
				</td>
				<td class="center">
					<?php if (is_null($module->menuid)) : ?>
						<?php if ($module->except):?>
							<?php echo Lang::txt('JYES'); ?>
						<?php else : ?>
							<?php echo Lang::txt('JNO'); ?>
						<?php endif;?>
					<?php elseif ($module->menuid > 0) : ?>
						<?php echo Lang::txt('JYES'); ?>
					<?php elseif ($module->menuid < 0) : ?>
						<?php echo Lang::txt('JNO'); ?>
					<?php else : ?>
						<?php echo Lang::txt('JALL'); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
