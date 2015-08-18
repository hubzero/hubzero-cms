<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');

$uri = JFactory::getUri();
$return = base64_encode($uri);

$userId = User::get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$modMenuId = (int) $this->get('ModMenuId');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task != 'menus.delete' || confirm('<?php echo Lang::txt('COM_MENUS_MENU_CONFIRM_DELETE', true);?>')) {
			Joomla.submitform(task);
		}
	}
</script>
<form action="<?php echo Route::url('index.php?option=com_menus&view=menus');?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th rowspan="2">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th rowspan="2">
					<?php echo Html::grid('sort',  'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th colspan="3" class="priority-4">
					<?php echo Lang::txt('COM_MENUS_HEADING_NUMBER_MENU_ITEMS'); ?>
				</th>
				<th rowspan="2">
					<?php echo Lang::txt('COM_MENUS_HEADING_LINKED_MODULES'); ?>
				</th>
				<th class="nowrap priority-5" rowspan="2">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			<tr>
				<th class="priority-4">
					<?php echo Lang::txt('COM_MENUS_HEADING_PUBLISHED_ITEMS'); ?>
				</th>
				<th class="priority-4">
					<?php echo Lang::txt('COM_MENUS_HEADING_UNPUBLISHED_ITEMS'); ?>
				</th>
				<th class="priority-4">
					<?php echo Lang::txt('COM_MENUS_HEADING_TRASHED_ITEMS'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canCreate = User::authorise('core.create', 'com_menus');
			$canEdit   = User::authorise('core.edit', 'com_menus');
			$canChange = User::authorise('core.edit.state', 'com_menus');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_menus&view=items&menutype='.$item->menutype) ?> ">
						<?php echo $this->escape($item->title); ?>
					</a>
					<p class="smallsub">(<span><?php echo Lang::txt('COM_MENUS_MENU_MENUTYPE_LABEL') ?></span>
						<?php if ($canEdit) : ?>
							<?php echo '<a href="'.Route::url('index.php?option=com_menus&task=menu.edit&id='.$item->id).' title='.$this->escape($item->description).'">'.
							$this->escape($item->menutype).'</a>'; ?>)
						<?php else : ?>
							<?php echo $this->escape($item->menutype)?>)
						<?php endif; ?>
					</p>
				</td>
				<td class="priority-4 center btns">
					<a href="<?php echo Route::url('index.php?option=com_menus&view=items&menutype='.$item->menutype.'&filter_published=1');?>">
						<?php echo $item->count_published; ?>
					</a>
				</td>
				<td class="priority-4 center btns">
					<a href="<?php echo Route::url('index.php?option=com_menus&view=items&menutype='.$item->menutype.'&filter_published=0');?>">
						<?php echo $item->count_unpublished; ?>
					</a>
				</td>
				<td class="priority-4 center btns">
					<a href="<?php echo Route::url('index.php?option=com_menus&view=items&menutype='.$item->menutype.'&filter_published=-2');?>">
						<?php echo $item->count_trashed; ?>
					</a>
				</td>
				<td class="left">
					<?php if (isset($this->modules[$item->menutype])) : ?>
					<ul>
						<?php foreach ($this->modules[$item->menutype] as &$module) : ?>
						<li>
							<?php if ($canEdit) : ?>
								<a class="button" href="<?php echo Route::url('index.php?option=com_modules&task=module.edit&id='.$module->id.'&return='.$return); //.'&tmpl=component&layout=modal');?>" rel="{handler: 'iframe', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}"  title="<?php echo Lang::txt('COM_MENUS_EDIT_MODULE_SETTINGS');?>">
								<?php echo Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?></a>
							<?php else :?>
								<?php echo Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php elseif ($modMenuId) : ?>
					<a href="<?php echo Route::url('index.php?option=com_modules&task=module.add&eid=' . $modMenuId . '&params[menutype]='.$item->menutype); ?>">
						<?php echo Lang::txt('COM_MENUS_ADD_MENU_MODULE'); ?></a>
					<?php endif; ?>
				</td>
				<td class="priority-5 center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
