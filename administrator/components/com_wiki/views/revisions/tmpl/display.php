<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelperPermissions::getActions('page');

JToolBarHelper::title(JText::_('COM_WIKI') . ': ' . JText::_('COM_WIKI_PAGE') . ': ' . JText::_('COM_WIKI_REVISIONS'), 'wiki.png');
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
JToolBarHelper::help('revisions');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_WIKI_COL_TITLE'); ?></th>
				<td><?php echo $this->escape(stripslashes($this->page->get('title'))); ?></td>
				<th><?php echo JText::_('COM_WIKI_COL_SCOPE'); ?></th>
				<td><?php echo $this->escape(stripslashes($this->page->get('scope'))); ?></td>
			</tr>
			<tr>
				<th>(<?php echo JText::_('COM_WIKI_COL_ID'); ?>) <?php echo JText::_('COM_WIKI_COL_PAGENAME'); ?></th>
				<td>(<?php echo $this->page->get('id'); ?>) <?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?></td>
				<th><?php echo JText::_('COM_WIKI_COL_GROUP'); ?></th>
				<td><?php echo $this->escape(stripslashes($this->page->get('group_cn'))); ?></td>
			</tr>
		</tbody>
	</table>

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_WIKI_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_REVISION', 'revision', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_EDIT_SUMMARY', 'summary', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_APPROVED', 'approved', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_MINOR_EDIT', 'minor_edit', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach ($this->rows as $row)
{
	switch ($row->get('approved'))
	{
		case '2':
			$color_access = 'trashed';
			$class = 'trashed';
			$task = '0';
			$alt = JText::_('COM_WIKI_STATE_TRASHED');
		break;

		case '1':
			$color_access = 'public';
			$class = 'approved';
			$task = '0';
			$alt = JText::_('COM_WIKI_STATE_APPROVED');
			break;
		case '0':
			$color_access = 'private';
			$class = 'unapprove';
			$task = '1';
			$alt = JText::_('COM_WIKI_STATE_NOT_APPROVED');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&pageid=' . $this->filters['pageid'] . '&' . JUtility::getToken() . '=1'); ?>">
						<?php echo JText::sprintf('COM_WIKI_REVISION_NUM', $this->escape(stripslashes($row->get('version')))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo JText::sprintf('COM_WIKI_REVISION_NUM', $this->escape(stripslashes($row->get('version')))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('summary'))); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="access <?php echo $class . ' ' . $color_access; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=approve&id=' . $row->get('id') . '&pageid=' . $this->filters['pageid'] . '&approve=' . $task . '&' . JUtility::getToken() . '=1'); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="access <?php echo $class . ' ' . $color_access; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
					<span class="state <?php echo ($row->get('minor_edit') ? 'yes' : 'no'); ?>">
						<span><?php echo $this->escape($row->get('minor_edit')); ?></span>
					</span>
				</td>
				<td>
					<time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php echo $this->escape($row->get('created')); ?></time>
				</td>
				<td>
					<span class="glyph user">
						<?php echo $this->escape(stripslashes($row->get('created_by_name'))); ?>
					</span>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>