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

$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = false;
}

$ids = array();
foreach ($this->rows as $row)
{
	$ids[] = $row->id;
}

$canDo = CoursesHelper::getActions('asset');

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
	$('task').value = task;
}

/*window.addEvent('domready', function(){
	addDeleteQueryEvent();
});
function addDeleteQueryEvent()
{
	$$('.views .delete').each(function(el) {
		$(el).addEvent('click', function(e){
			new Event(e).stop();

			var res = confirm('Are you sure you wish to delete this item?');
			if (!res) {
				return false;
			}

			var href = $(this).href;
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}

			var myAjax = new Ajax(href, {
				method: 'get',
				update: $('custom-views'),
				evalScripts: false,
				onSuccess: function() {
					addDeleteQueryEvent();
				}
			}).request();
			
			return false;
		});
	});
}

window.addEvent("domready", function() {
	SqueezeBox.initialize({});
	$$("a.modal").each(function(el) {
		el.addEvent("click", function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});
});*/
window.addEvent('domready', function() {
	//window.top.document.updateUploader && window.top.document.updateUploader();
	$$('a.edit-asset').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			window.top.document.assetform.fromElement(el);
		});
	});
});

</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist" summary="<?php echo JText::_('COM_COURSES_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th colspan="4">
					<select name="asset" style="max-width: 15em;">
						<option value="0"><?php echo JText::_('Select asset...'); ?></option>
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
					<input type="submit" value="<?php echo JText::_('Attach asset'); ?>" onclick="setTask('link');" />
				</th>
				<th colspan="4" style="text-align:right;">
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=add&amp;scope=<?php echo $this->filters['asset_scope']; ?>&amp;scope_id=<?php echo $this->filters['asset_scope_id']; ?>&amp;course_id=<?php echo $this->filters['course_id']; ?>&amp;tmpl=<?php echo $this->filters['tmpl']; ?>" class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}">Create asset</a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Title'); ?></th>
				<th scope="col"><?php echo JText::_('Type'); ?></th>
				<th scope="col"><?php echo JText::_('State'); ?></th>
				<th scope="col" colspan="3"><?php echo JText::_('Ordering'); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<!-- <tfoot>
			<tr>
				<td colspan="10"><?php //echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot> -->
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
				<!-- <td>
					<?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?>
				</td> -->
				<td>
					<?php if ($row->state == 2) { ?>
						<span class="state delete">
							<span class="text"><?php echo JText::_('Trashed'); ?></span>
						</span>
					<?php } else if ($row->state == 1) { ?>
						<span class="state publish">
							<span class="text"><?php echo JText::_('Published'); ?></span>
						</span>
					<?php } else { ?>
						<span class="state unpublish">
							<span class="text"><?php echo JText::_('Unpublished'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php 
					echo $this->pageNav->orderUpIcon( $i, ($row->ordering != @$this->rows[$i-1]->ordering) ); ?>
				</td>
				<td>
					<?php 
					echo $this->pageNav->orderDownIcon( $i, $n, ($row->ordering != @$this->rows[$i+1]->ordering) ); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->ordering)); ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a class="state delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=unlink&amp;asset=<?php echo $row->id; ?>&amp;scope=<?php echo $this->filters['asset_scope']; ?>&amp;scope_id=<?php echo $this->filters['asset_scope_id']; ?>&amp;course_id=<?php echo $this->filters['course_id']; ?>&amp;tmpl=<?php echo $this->filters['tmpl']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><img src="components/<?php echo $this->option; ?>/assets/img/trash.png" width="15" height="15" alt="<?php echo JText::_('[ x ]'); ?>" /></span>
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
