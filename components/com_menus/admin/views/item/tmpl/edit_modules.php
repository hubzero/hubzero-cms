<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

Html::behavior('framework', true);
?>
	<script type="text/javascript">
	// Hide/show all rows which are not assigned.
	jQuery(document).ready(function($){
		$('#showmods').on('click', function(e) {
			$('.adminlist tr.nope').toggle();
		});
	});
	</script>

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
					<?php $link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ; ?>
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
