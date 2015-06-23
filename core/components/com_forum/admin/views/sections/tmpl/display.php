<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Forum\Helpers\Permissions::getActions('section');

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SECTIONS'), 'forum.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('sections');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="scopeinfo"><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE'); ?>:</label>
		<select name="scopeinfo" id="scopeinfo" style="max-width: 20em;" onchange="document.adminForm.submit();">
			<option value=""<?php if ($this->filters['scopeinfo'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT'); ?></option>
			<option value="site:0"<?php if ($this->filters['scopeinfo'] == 'site:0') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_NONE'); ?></option>
			<?php
			$results = $this->forum->scopes();

			$list = array();

			foreach ($results as $result)
			{
				if (!isset($list[$result->scope]))
				{
					$list[$result->scope] = array();
				}
				$list[$result->scope][$result->scope_id] = $result;
			}

			$html = '';
			foreach ($list as $label => $optgroup)
			{
				if ($label == 'site')
				{
					continue;
				}
				$html .= ' <optgroup label="' . $label . '">';
				foreach ($optgroup as $result)
				{
					$html .= ' <option value="' . $result->scope . ':' . $result->scope_id . '"';
					if ($this->filters['scopeinfo'] == $result->scope . ':' . $result->scope_id)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>' . $this->escape(stripslashes($result->caption));
					$html .= '</option>'."\n";
				}
				$html .= '</optgroup>'."\n";
			}
			echo $html;
			?>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_FORUM_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->results)
{
	$k = 0;
	foreach ($this->results as $i => $row)
	{
		switch ($row->get('state'))
		{
			case '2':
				$task = 'publish';
				$alt = Lang::txt('JTRASHED');
				$cls = 'trash';
			break;
			case '1':
				$task = 'unpublish';
				$alt = Lang::txt('JPUBLISHED');
				$cls = 'publish';
			break;
			case '0':
			default:
				$task = 'publish';
				$alt = Lang::txt('JUNPUBLISHED');
				$cls = 'unpublish';
			break;
		}

		switch ($row->get('access'))
		{
			case 0:
				$color_access = 'public';
				$task_access  = '1';
				$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PUBLIC'));
				break;
			case 1:
				$color_access = 'registered';
				$task_access  = '2';
				$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_REGISTERED'));
				break;
			case 2:
				$color_access = 'special';
				$task_access  = '3';
				$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_SPECIAL'));
				break;
			case 3:
				$color_access = 'protected';
				$task_access  = '4';
				$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PROTECTED'));
				break;
			case 4:
				$color_access = 'private';
				$task_access  = '0';
				$row->set('access_level', Lang::txt('COM_FORUM_ACCESS_PRIVATE'));
				break;
		}

		$cat = $row->categories('count', array('state' => -1));
?>
			<tr class="<?php echo "row$k" . ($row->get('state') ==2 ? ' archived' : ''); ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_FORUM_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="access <?php echo $color_access; ?>"><?php echo $this->escape($row->get('access_level')); ?></span>
				</td>
				<td class="priority-3">
					<span class="scope">
						<span><?php echo $this->escape($row->get('scope')) . ' (' . (isset($list[$row->get('scope')][$row->get('scope_id')]) ? $this->escape($list[$row->get('scope')][$row->get('scope_id')]->caption) : $this->escape($row->get('scope_id'))) . ')'; ?></span>
					</span>
				</td>
				<td>
					<?php if ($cat > 0) { ?>
						<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=categories&section_id=' . $row->get('id')); ?>">
							<span><?php echo $cat; ?></span>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<span><?php echo $cat; ?></span>
						</span>
					<?php } ?>
				</td>
			</tr>
<?php
		$k = 1 - $k;
	}
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>