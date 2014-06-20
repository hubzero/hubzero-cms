<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$ids = array();
foreach ($this->rows as $row)
{
	$ids[] = $row->id;
}

$canDo = CoursesHelper::getActions();

JHTML::_('behavior.modal');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
function setTask(task)
{
	$('#task').val(task);
}

jQuery(document).ready(function($){
	$('a.edit-asset').on('click', function(e) {
		e.preventDefault();

		window.top.document.assetform.open({'href': $(this).attr('href'), 'type': 'iframe', 'width': 570, 'height': 550, 'autoHeight': false});
	});
});

</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="4">
					<select name="asset" style="max-width: 15em;">
						<option value="0"><?php echo JText::_('COM_COURSES_SELECT'); ?></option>
						<?php if ($this->assets) { ?>
							<?php
							foreach ($this->assets as $asset)
							{
								if (in_array($asset->id, $ids))
								{
									continue;
								}
							?>
							<option value="<?php echo $this->escape(stripslashes($asset->id)); ?>"><?php echo $this->escape(stripslashes($asset->title)); ?> (<?php echo $this->escape(stripslashes($asset->type)); ?>)</option>
							<?php } ?>
						<?php } ?>
					</select>
					<input type="submit" value="<?php echo JText::_('COM_COURSES_ATTACH_ASSET'); ?>" onclick="setTask('link');" />
				</th>
				<th colspan="4" style="text-align:right;">
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=add&amp;scope=<?php echo $this->filters['asset_scope']; ?>&amp;scope_id=<?php echo $this->filters['asset_scope_id']; ?>&amp;course_id=<?php echo $this->filters['course_id']; ?>&amp;tmpl=<?php echo $this->filters['tmpl']; ?>" class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}"><?php echo JText::_('COM_COURSES_CREATE_ASSET'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_TITLE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_TYPE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_STATE'); ?></th>
				<th scope="col" colspan="3"><?php echo JText::_('COM_COURSES_COL_ORDERING'); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
$k = 0;
$n = count($this->rows);
foreach ($this->rows as $row)
{
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->escape($row->id); ?>
					<input style="visibility:hidden;" type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>&amp;scope=<?php echo $this->filters['asset_scope']; ?>&amp;scope_id=<?php echo $this->filters['asset_scope_id']; ?>&amp;course_id=<?php echo $this->filters['course_id']; ?>&amp;tmpl=<?php echo $this->filters['tmpl']; ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->type)); ?>
				</td>
				<td>
					<?php if ($row->state == 2) { ?>
						<span class="state delete">
							<span class="text"><?php echo JText::_('COM_COURSES_TRASHED'); ?></span>
						</span>
					<?php } else if ($row->state == 1) { ?>
						<span class="state publish">
							<span class="text"><?php echo JText::_('COM_COURSES_PUBLISHED'); ?></span>
						</span>
					<?php } else { ?>
						<span class="state unpublish">
							<span class="text"><?php echo JText::_('COM_COURSES_UNPUBLISHED'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php echo $this->pageNav->orderUpIcon($i, ($row->ordering != @$this->rows[$i-1]->ordering)); ?>
				</td>
				<td>
					<?php echo $this->pageNav->orderDownIcon($i, $n, ($row->ordering != @$this->rows[$i+1]->ordering)); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->ordering)); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="state delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=unlink&amp;asset=<?php echo $row->id; ?>&amp;scope=<?php echo $this->filters['asset_scope']; ?>&amp;scope_id=<?php echo $this->filters['asset_scope_id']; ?>&amp;course_id=<?php echo $this->filters['course_id']; ?>&amp;tmpl=<?php echo $this->filters['tmpl']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><img src="components/<?php echo $this->option; ?>/assets/img/trash.png" width="15" height="15" alt="<?php echo JText::_('COM_COURSES_REMOVE'); ?>" /></span>
					</a>
				<?php } ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="course_id" value="<?php echo $this->filters['course_id']; ?>" />
	<input type="hidden" name="tmpl" value="<?php echo $this->filters['tmpl']; ?>" />
	<input type="hidden" name="scope" value="<?php echo $this->filters['asset_scope']; ?>" />
	<input type="hidden" name="scope_id" value="<?php echo $this->filters['asset_scope_id']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
