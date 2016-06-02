<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::url('index.php?option=com_templates&view=styles'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_TEMPLATES_STYLES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_template" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo Lang::txt('COM_TEMPLATES_FILTER_TEMPLATE'); ?></option>
				<?php echo Html::select('options', TemplatesHelper::getTemplateOptions($this->state->get('filter.client_id')), 'value', 'text', $this->state->get('filter.template'));?>
			</select>

			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo Html::select('options', TemplatesHelper::getClientOptions(), 'value', 'text', $this->state->get('filter.client_id'));?>
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
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_STYLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo Html::grid('sort', 'JCLIENT', 'a.client_id', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.template', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'a.home', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Lang::txt('COM_TEMPLATES_HEADING_ASSIGNED'); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCreate = User::authorise('core.create',     'com_templates');
				$canEdit   = User::authorise('core.edit',       'com_templates');
				$canChange = User::authorise('core.edit.state', 'com_templates');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($this->preview && $item->client_id == '0'): ?>
						<a target="_blank"href="<?php echo Request::root().'index.php?tp=1&templateStyle='.(int) $item->id ?>"  class="jgrid hasTip" title="<?php echo  htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW')); ?>::<?php echo htmlspecialchars($item->title);?>" ><span class="state preview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW'); ?></span></span></a>
					<?php elseif ($item->client_id == '1'): ?>
						<span class="jgrid hasTip" title="<?php echo  htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN')); ?>" ><span class="state nopreview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?></span></span></span>
					<?php else: ?>
						<span class="jgrid hasTip" title="<?php echo  htmlspecialchars(Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW')); ?>" ><span class="state nopreview"><span class="text"><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?></span></span></span>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
					<a href="<?php echo Route::url('index.php?option=com_templates&task=style.edit&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->title);?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title);?>
					<?php endif; ?>
				</td>
				<td class="center priority-2">
					<?php echo $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
				</td>
				<td>
					<label for="cb<?php echo $i;?>">
						<a href="<?php echo Route::url('index.php?option=com_templates&view=template&id='.(int) $item->e_id); ?>  ">
							<?php echo ucfirst($this->escape($item->template));?>
						</a>
					</label>
				</td>
				<td class="center priority-3">
					<?php if ($item->home == '0' || $item->home == '1'):?>
						<?php echo Html::grid('isdefault', $item->home!='0', $i, 'styles.', $canChange && $item->home!='1');?>
					<?php elseif ($canChange):?>
						<a href="<?php echo Route::url('index.php?option=com_templates&task=styles.unsetDefault&cid[]=' . $item->id . '&' . Session::getFormToken() . '=1');?>">
							<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>Lang::txt('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
						</a>
					<?php else:?>
						<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>$item->language_title), true);?>
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

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
