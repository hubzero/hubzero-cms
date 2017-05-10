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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Messages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_MESSAGES_MANAGER_MESSAGES'), 'inbox.png');

if ($canDo->get('core.create'))
{
	Toolbar::addNew('add');
}

if ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::publish('publish', 'COM_MESSAGES_TOOLBAR_MARK_AS_READ');
	Toolbar::unpublish('unpublish', 'COM_MESSAGES_TOOLBAR_MARK_AS_UNREAD');
}

if ($this->filters['state'] == -2 && $canDo->get('core.delete'))
{
	Toolbar::divider();
	Toolbar::deleteList('', 'delete', 'JTOOLBAR_EMPTY_TRASH');
}
elseif ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::trash('trash');
}

//Toolbar::addNew('module.add');
Toolbar::divider();
Toolbar::appendButton('Popup', 'options', 'COM_MESSAGES_TOOLBAR_MY_SETTINGS', 'index.php?option=com_messages&amp;controller=configs&amp;tmpl=component', 850, 400);

if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_messages');
}

Toolbar::divider();
Toolbar::help('JHELP_COMPONENTS_MESSAGING_INBOX');

// Include the component HTML helpers.
Html::behavior('tooltip');
Html::behavior('multiselect');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>

<form action="<?php echo Route::url('index.php?option=com_messages&view=messages'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6 filter-search">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MESSAGES_SEARCH_IN_SUBJECT'); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6 filter-select">
				<select name="filter_state" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo Html::select('options', Components\Messages\Helpers\Utilities::getStateOptions(), 'value', 'text', $this->filters['state']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title">
					<?php echo Html::grid('sort',  'COM_MESSAGES_HEADING_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_MESSAGES_HEADING_READ', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_MESSAGES_HEADING_FROM', 'a.user_id_from', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo Html::grid('sort', 'JDATE', 'a.date_time', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->rows as $i => $item) :
				$canChange = User::authorise('core.edit.state', 'com_messages');
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo Html::grid('id', $i, $item->message_id); ?>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_messages&task=view&message_id='.(int) $item->message_id); ?>">
							<?php echo $this->escape($item->subject); ?>
						</a>
					</td>
					<td class="center">
						<?php echo Components\Messages\Helpers\Utilities::state($item->state, $i, $canChange); ?>
					</td>
					<td>
						<?php echo $item->user_from; ?>
					</td>
					<td>
						<time><?php echo Date::of($item->date_time)->toLocal(Lang::txt('DATE_FORMAT_LC2')); ?></time>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
