<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$client    = $this->state->get('filter.client') == 'site' ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR');
$language  = $this->state->get('filter.language');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::url('index.php?option=com_languages&view=overrides'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_language_client" class="inputbox" onchange="this.form.submit()">
				<?php echo Html::select('options', $this->languages, null, 'text', $this->state->get('filter.language_client')); ?>
			</select>
		</div>
	</fieldset>

	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
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
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $canEdit = User::authorise('core.edit', 'com_languages');
		$i = 0;
		foreach ($this->items as $key => $text): ?>
			<tr class="row<?php echo $i % 2; ?>" id="overriderrow<?php echo $i; ?>">
				<td>
					<?php echo Html::grid('id', $i, $key); ?>
				</td>
				<td>
					<?php if ($canEdit): ?>
						<a id="key[<?php echo $this->escape($key); ?>]" href="<?php echo Route::url('index.php?option=com_languages&task=override.edit&id='.$key); ?>"><?php echo $this->escape($key); ?></a>
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

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
