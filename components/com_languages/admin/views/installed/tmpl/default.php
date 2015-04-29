<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Add specific helper files for html generation
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$userId		= User::get('id');
$client		= $this->state->get('filter.client_id', 0) ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');
$clientId	= $this->state->get('filter.client_id', 0);
?>
<form action="<?php echo Route::url('index.php?option=com_languages&view=installed&client='.$clientId); ?>" method="post" id="adminForm" name="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_NUM'); ?>
				</th>
				<th width="20">
					&#160;
				</th>
				<th width="25%" class="title">
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_LANGUAGE'); ?>
				</th>
				<th>
					<?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JCLIENT'); ?>
				</th>
				<th>
					<?php echo Lang::txt('COM_LANGUAGES_HEADING_DEFAULT'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JVERSION'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JDATE'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JAUTHOR'); ?>
				</th>
				<th>
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
		<?php foreach ($this->rows as $i => $row) :
			$canCreate	= User::authorise('core.create',		'com_languages');
			$canEdit	= User::authorise('core.edit',			'com_languages');
			$canChange	= User::authorise('core.edit.state',	'com_languages');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td width="20">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td width="20">
					<?php echo JHtml::_('languages.id', $i, $row->language);?>
				</td>
				<td width="25%">
					<?php echo $this->escape($row->name); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->language); ?>
				</td>
				<td align="center">
					<?php echo $client;?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.isdefault', $row->published, $i, 'installed.',  !$row->published && $canChange);?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->version); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->creationDate); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->author); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->authorEmail); ?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
