<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Get the results for each action
$canDo = Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_TITLE'), 'langmanager');

if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}

if ($canDo->get('core.edit') && $this->pagination->total)
{
	Toolbar::editList();
}

if ($canDo->get('core.delete') && $this->pagination->total)
{
	Toolbar::deleteList();
}

if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option);
}
Toolbar::divider();
Toolbar::help('overrides');

$client    = $this->filters['client'] == 'site' ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR');
$language  = $this->filters['language'];
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_language_client" class="inputbox filter filter-submit">
				<?php echo Html::select('options', $this->languages, null, 'text', $this->filters['language_client']); ?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_LANGUAGES_VIEW_OVERRIDES_KEY', 'key', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'COM_LANGUAGES_VIEW_OVERRIDES_TEXT', 'text', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4">
					<?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JCLIENT'); ?>
				</th>
				<th class="priority-6">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_NUM'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$canEdit = User::authorise('core.edit', $this->option);
		$i = 0;
		foreach ($this->items as $key => $text): ?>
			<tr class="row<?php echo $i % 2; ?>" id="overriderrow<?php echo $i; ?>">
				<td>
					<?php echo Html::grid('id', $i, $key); ?>
				</td>
				<td>
					<?php if ($canEdit): ?>
						<a id="key[<?php echo $this->escape($key); ?>]" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $key); ?>"><?php echo $this->escape($key); ?></a>
					<?php else: ?>
						<?php echo $this->escape($key); ?>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<span id="string[<?php echo $this->escape($key); ?>]"><?php echo $this->escape($text); ?></span>
				</td>
				<td class="priority-4">
					<?php echo $language; ?>
				</td>
				<td>
					<?php echo $client; ?>
				</td>
				<td class="priority-6">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
			</tr>
			<?php $i++;
		endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
