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

defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');

$field     = Request::getCmd('field');
$function  = 'jSelectUser_' . $field;
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>
<h2 class="modal-title"><?php echo Lang::txt('Users'); ?></h2>
<form action="<?php echo Route::url('index.php?option=com_members&controllers=members&task=modal&tmpl=component&groups=' . Request::getVar('groups', '', 'default', 'BASE64') . '&excluded=' . Request::getVar('excluded', '', 'default', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="filter clearfix">
		<div class="col width-70 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" size="40" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_IN_NAME'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			<button type="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('', '<?php echo Lang::txt('JLIB_FORM_SELECT_USER') ?>');"><?php echo Lang::txt('JOPTION_NO_USER')?></button>
		</div>
		<div class="col width-30 fltrt">
			<label for="filter_group_id"><?php echo Lang::txt('COM_MEMBERS_FILTER_USER_GROUP'); ?></label>
			<?php echo Html::access('usergroup', 'filter_group_id', $this->filters['group_id'], 'onchange="this.form.submit()"'); ?>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_GROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			foreach ($this->rows as $row) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $row->get('id'); ?>', '<?php echo $this->escape(addslashes($row->get('name'))); ?>');">
						<?php echo $row->get('name'); ?>
					</a>
				</td>
				<td align="center">
					<?php echo $row->get('username'); ?>
				</td>
				<td align="left">
					<?php
					$groups = array();
					foreach ($row->accessgroups as $agroup)
					{
						$groups[] = $this->accessgroups->seek($agroup->get('group_id'))->get('title');
					}
					$row->set('group_names', implode('<br />', $groups));
					echo $row->get('group_names'); ?>
				</td>
			</tr>
		<?php
		$i++;
		endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
