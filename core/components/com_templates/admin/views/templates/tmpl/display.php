<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Templates\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_TEMPLATES'), 'thememanager');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_templates');
	Toolbar::divider();
}
Toolbar::help('templates');

// Include the component HTML helpers.
Html::behavior('tooltip');
Html::behavior('modal');
Html::behavior('multiselect');
?>

<form action="<?php echo Route::url('index.php?option=com_templates&controller=templates'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" title="<?php echo Lang::txt('COM_TEMPLATES_TEMPLATES_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_client_id" class="inputbox filter filter-submit">
				<option value="*"><?php echo Lang::txt('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo Html::select('options', \Components\Templates\Helpers\Utilities::getClientOptions(), 'value', 'text', $this->filters['client_id']);?>
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
					<?php echo Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.element', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JCLIENT', 'a.client_id', $this->filters['sort_Dir'], $this->filters['sort']); ?>
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
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$i = 0;
			foreach ($this->rows as $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="priority-3">
						<?php echo Components\Templates\Helpers\Utilities::thumb($item->element, $item->protected); ?>
					</td>
					<td class="template-name">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=files&id=' . (int) $item->extension_id); ?>">
							<?php echo Lang::txt( 'COM_TEMPLATES_TEMPLATE_DETAILS', ucfirst($item->name)); ?>
						</a>
						<p>
							<?php if ($this->preview && $item->client_id == '0'): ?>
								<a href="<?php echo Request::root().'index.php?tp=1&template=' . $item->element; ?>" rel="noopener" target="_blank">
									<?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW'); ?>
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
						<?php echo $this->escape($item->xml->get('version')); ?>
					</td>
					<td class="priority-5">
						<?php echo $this->escape($item->xml->get('creationDate')); ?>
					</td>
					<td class="priority-3">
						<?php if ($author = $item->xml->get('author')) : ?>
							<p><?php echo $this->escape($author); ?></p>
						<?php else : ?>
							&mdash;
						<?php endif; ?>
						<?php if ($email = $item->xml->get('authorEmail')) : ?>
							<p><?php echo $this->escape($email); ?></p>
						<?php endif; ?>
						<?php if ($url = $item->xml->get('authorUrl')) : ?>
							<p>
								<a href="<?php echo $this->escape($url); ?>">
									<?php echo $this->escape($url); ?>
								</a>
							</p>
						<?php endif; ?>
					</td>
				</tr>
				<?php
				$i++;
			endforeach;
			?>
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
