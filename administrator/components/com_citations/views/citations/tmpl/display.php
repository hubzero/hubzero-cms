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

$canDo = CitationsHelper::getActions('citation');

JToolBarHelper::title(JText::_('CITATIONS'), 'citation.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences('com_citations', 600, 800);
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList();
}
JToolBarHelper::spacer();
JToolBarHelper::help('citations');

//set the escape callback
$this->setEscape("htmlentities");
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = $('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo JText::_('SEARCH'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search...'); ?>" />

			<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="sort"><?php echo JText::_('SORT'); ?>: </label>
			<select name="sort" id="sort">
				<option value="created DESC"<?php if ($this->filters['sort'] == 'created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('DATE'); ?></option>
				<option value="year"<?php if ($this->filters['sort'] == 'year') { echo ' selected="selected"'; } ?>><?php echo JText::_('YEAR'); ?></option>
				<option value="type"<?php if ($this->filters['sort'] == 'type') { echo ' selected="selected"'; } ?>><?php echo JText::_('TYPE'); ?></option>
				<option value="author ASC"<?php if ($this->filters['sort'] == 'author ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('AUTHORS'); ?></option>
				<option value="title ASC"<?php if ($this->filters['sort'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('TYPE'); ?></th>
				<th scope="col"><?php echo JText::_('TITLE'); ?> / <?php echo JText::_('AUTHORS'); ?></th>
				<th scope="col"><?php echo JText::_('PUBLISHED') ?></th>
				<th scope="col"><?php echo JText::_('YEAR') ?></th>
				<th scope="col"><?php echo JText::_('AFFILIATED'); ?></th>
				<th scope="col"><?php echo JText::_('FUNDED_BY'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td>
					<?php
						$type = "";
						foreach ($this->types as $t)
						{
							if ($row->type == $t['id'])
							{
								$type = $t['type_title'];
							}
						}
						echo ($type) ? $type : Jtext::_('GENERIC');
					?>
				</td>
				<td>
					<?php
						$title = html_entity_decode($row->title);
						$author = html_entity_decode($row->author);
						if (!preg_match('!\S!u', $title))
						{
							$title = utf8_encode($title);
						}

						if (!preg_match('!\S!u', $author))
						{
							$author = utf8_encode($author);
						}
					?>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
							<?php echo $this->escape($title); ?>
						</a>
						<br />
						<small><?php echo $this->escape($author); ?></small>
					<?php } else { ?>
						<span>
							<?php echo $this->escape($title); ?></a><br />
							<small><?php echo $this->escape($author); ?></small>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->published) : ?>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unpublish&id=' . $row->id); ?>"><span class="state publish"><span><?php echo JText::_('UNPUBLISH'); ?></span></span></a>
					<?php else : ?>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->id); ?>"><span class="state unpublish"><span><?php echo JText::_('PUBLISH'); ?></span></span></a>
					<?php endif; ?>
				</td>
				<td><?php echo $this->escape($row->year); ?></td>
				<td>
					<?php if ($row->affiliated == 1) { echo '<span class="state publish"><span>' . JText::_('CITATION_YES') . '</span></span>'; } ?>
				</td>
				<td>
					<?php if ($row->fundedby == 1) { echo '<span class="state publish"><span>' . JText::_('CITATION_YES') . '</span></span>'; } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
