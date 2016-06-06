<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Add specific helper files for html generation
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$userId   = User::get('id');
$client   = $this->state->get('filter.client_id', 0) ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');
$clientId = $this->state->get('filter.client_id', 0);
?>
<form action="<?php echo Route::url('index.php?option=com_languages&view=installed&client=' . $clientId); ?>" method="post" id="adminForm" name="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" class="priority-6">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_NUM'); ?>
				</th>
				<th>
					&#160;
				</th>
				<th scope="col" class="title">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_LANGUAGE'); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL'); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Lang::txt('JCLIENT'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_DEFAULT'); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Lang::txt('JVERSION'); ?>
				</th>
				<th scope="col" class="priority-6">
					<?php echo Lang::txt('JDATE'); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Lang::txt('JAUTHOR'); ?>
				</th>
				<th scope="col" class="priority-6">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_AUTHOR_EMAIL'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$canCreate = User::authorise('core.create',     'com_languages');
		$canEdit   = User::authorise('core.edit',       'com_languages');
		$canChange = User::authorise('core.edit.state', 'com_languages');

		foreach ($this->rows as $i => $row) :
		?>
			<tr class="row<?php echo $i % 2; if (isset($row->missing)) { echo ' archived'; } ?>">
				<td class="priority-6">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php if (!isset($row->missing)) { echo Html::languages('id', $i, $row->language); } ?>
				</td>
				<td>
					<?php echo $this->escape($row->name); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->language); ?>
				</td>
				<td class="priority-3">
					<?php echo $client; ?>
				</td>
				<td>
					<?php echo Html::grid('isdefault', $row->published, $i, 'installed.', !$row->published && $canChange);?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->version); ?>
				</td>
				<td class="priority-6">
					<?php echo $this->escape($row->creationDate); ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->author); ?>
				</td>
				<td class="priority-6">
					<?php echo $this->escape($row->authorEmail); ?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>
