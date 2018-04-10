<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canDo = Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_INSTALLED_TITLE'), 'langmanager');

if ($canDo->get('core.edit.state'))
{
	Toolbar::makeDefault('setDefault');
	Toolbar::divider();
}

if ($canDo->get('core.admin'))
{
	// Add install languages link to the lang installer component
	Toolbar::appendButton('Link', 'extension', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
	Toolbar::divider();

	Toolbar::preferences('com_languages');
	Toolbar::divider();
}

Toolbar::help('installed');

$userId   = User::get('id');
$client   = $this->filters['client_id'] ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');
$clientId = $this->filters['client_id'];
$pagination = $this->rows->pagination;
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=installed&client=' . $clientId); ?>" method="post" id="adminForm" name="adminForm">

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
					<?php echo $pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$canCreate = User::authorise('core.create', $this->option);
		$canEdit   = User::authorise('core.edit', $this->option);
		$canChange = User::authorise('core.edit.state', $this->option);

		foreach ($this->rows as $i => $row) :
		?>
			<tr class="row<?php echo $i % 2; if (isset($row->missing)) { echo ' archived'; } ?>">
				<td class="priority-6">
					<?php echo $pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php
					if (!$row->missing) :
						echo '<input type="radio" id="cb' . $i . '" name="cid" value="' . $this->escape($row->language) . '" onclick="Joomla.isChecked(this.checked);" title="' . ($i+1) . '"/>';
					endif;
					?>
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
					<?php echo Html::grid('isdefault', $row->published, $i, '', !$row->published && $canChange);?>
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

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>
