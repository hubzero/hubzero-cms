<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Templates\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_STYLES'), 'thememanager');
if ($canDo->get('core.edit.state'))
{
	Toolbar::makeDefault('setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
	Toolbar::divider();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::custom('duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
	Toolbar::divider();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::divider();
}
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_templates');
	Toolbar::divider();
}
Toolbar::help('styles');

Html::behavior('tooltip');
Html::behavior('multiselect');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TEMPLATES_STYLES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<label for="filter_template"><?php echo Lang::txt('COM_TEMPLATES_FILTER_TEMPLATE'); ?></label>
			<select name="filter_template" id="filter_template" class="inputbox filter filter-submit">
				<option value="0"><?php echo Lang::txt('COM_TEMPLATES_FILTER_TEMPLATE'); ?></option>
				<?php echo Html::select('options', \Components\Templates\Helpers\Utilities::getTemplateOptions($this->filters['client_id']), 'value', 'text', $this->filters['template']); ?>
			</select>

			<label for="filter_client_id"><?php echo Lang::txt('JGLOBAL_FILTER_CLIENT'); ?></label>
			<select name="filter_client_id" id="filter_client_id" class="inputbox filter filter-submit">
				<option value="*"><?php echo Lang::txt('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo Html::select('options', \Components\Templates\Helpers\Utilities::getClientOptions(), 'value', 'text', $this->filters['client_id']); ?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					&#160;
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_STYLE', 'title', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo Html::grid('sort', 'JCLIENT', 'client_id', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'template', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'home', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Lang::txt('COM_TEMPLATES_HEADING_ASSIGNED'); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->rows as $i => $item) :

				$path = $item->path();

				$canCreate = User::authorise('core.create', $this->option);
				$canEdit   = $path ? User::authorise('core.edit', $this->option) : false;
				$canChange = User::authorise('core.edit.state', $this->option);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($path) : ?>
						<?php echo Html::grid('id', $i, $item->id, false, 'id'); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($path && $this->preview && $item->client_id == '0'): ?>
						<a rel="noopener" target="_blank" href="<?php echo Request::root().'index.php?tp=1&templateStyle='.(int) $item->id; ?>" class="jgrid hasTip" title="<?php echo htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW')); ?>::<?php echo htmlspecialchars($item->title); ?>" ><span class="state preview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW'); ?></span></span></a>
					<?php elseif ($path && $item->client_id == '1'): ?>
						<span class="jgrid hasTip" title="<?php echo htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN')); ?>"><span class="state nopreview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?></span></span></span>
					<?php else: ?>
						<span class="jgrid hasTip" title="<?php echo htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW')); ?>"><span class="state nopreview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?></span></span></span>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<?php if (!$path) : ?>
						<p class="smallsub"><?php echo Lang::txt('COM_TEMPLATES_ERROR_MISSING_FILES'); ?></p>
					<?php endif; ?>
				</td>
				<td class="center priority-2">
					<?php echo $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
				</td>
				<td>
					<label for="cb<?php echo $i;?>">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=templates&id=' . (int) $item->e_id); ?>">
							<?php echo ucfirst($this->escape($item->template)); ?>
						</a>
					</label>
				</td>
				<td class="center priority-3">
					<?php if ($item->home == '0' || $item->home == '1'):?>
						<?php echo Html::grid('isdefault', $item->home!='0', $i, 'styles.', $canChange && $item->home!='1');?>
					<?php elseif ($canChange):?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unsetDefault&id=' . $item->id . '&' . Session::getFormToken() . '=1');?>">
							<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => Lang::txt('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
						</a>
					<?php else:?>
						<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => $item->language_title), true);?>
					<?php endif;?>
				</td>
				<td class="center priority-4">
					<?php if ($item->assigned > 0) : ?>
						<span class="state yes" title="<?php echo Lang::txts('COM_TEMPLATES_ASSIGNED', $item->assigned); ?>">
							<span class="text"><?php echo Lang::txts('COM_TEMPLATES_ASSIGNED', $item->assigned); ?></span>
						</span>
					<?php else : ?>
						&#160;
					<?php endif; ?>
				</td>
				<td class="priority-5 center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
