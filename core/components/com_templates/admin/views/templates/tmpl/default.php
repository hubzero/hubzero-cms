<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('modal');
Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::url('index.php?option=com_templates&view=templates'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Lang::txt('COM_TEMPLATES_TEMPLATES_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo Html::select('options', TemplatesHelper::getClientOptions(), 'value', 'text', $this->state->get('filter.client_id'));?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist" id="template-mgr">
		<thead>
			<tr>
				<th class="priority-3">
					&#160;
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.element', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JCLIENT', 'a.client_id', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Lang::txt('JVERSION'); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Lang::txt('JDATE'); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Lang::txt('JAUTHOR'); ?>
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
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="priority-3">
					<?php echo Html::templates('thumb', $item->element, $item->protected); ?>
				</td>
				<td class="template-name">
					<a href="<?php echo Route::url('index.php?option=com_templates&view=template&id='.(int) $item->extension_id); ?>">
						<?php echo  Lang::txt( 'COM_TEMPLATES_TEMPLATE_DETAILS', ucfirst($item->name)) ;?>
					</a>
					<p>
						<?php if ($this->preview && $item->client_id == '0'): ?>
							<a href="<?php echo Request::root().'index.php?tp=1&template='.$item->element; ?>" target="_blank">
								<?php echo  Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW'); ?>
							</a>
						<?php elseif ($item->client_id == '1'): ?>
							<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?>
						<?php else: ?>
							<span class="hasTip" title="<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>::<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_DESC'); ?>">
								<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>
							</span>
						<?php endif; ?>
					</p>
				</td>
				<td>
					<?php echo $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR'); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($item->xmldata->get('version')); ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape($item->xmldata->get('creationDate')); ?>
				</td>
				<td class="priority-3">
					<?php if ($author = $item->xmldata->get('author')) : ?>
						<p><?php echo $this->escape($author); ?></p>
					<?php else : ?>
						&mdash;
					<?php endif; ?>
					<?php if ($email = $item->xmldata->get('authorEmail')) : ?>
						<p><?php echo $this->escape($email); ?></p>
					<?php endif; ?>
					<?php if ($url = $item->xmldata->get('authorUrl')) : ?>
						<p>
							<a href="<?php echo $this->escape($url); ?>">
								<?php echo $this->escape($url); ?>
							</a>
						</p>
					<?php endif; ?>
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
