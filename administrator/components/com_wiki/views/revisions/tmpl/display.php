<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelper::getActions('page');

JToolBarHelper::title(JText::_('Wiki') . ': ' . JText::_('Page Revisions'), 'wiki.png');
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
				<th>Title</th>
				<td><?php echo $this->escape(stripslashes($this->page->get('title'))); ?></td>
				<th>Scope</th>
				<td><?php echo $this->escape(stripslashes($this->page->get('scope'))); ?></td>
			</tr>
			<tr>
				<th>(ID) Pagename</th>
				<td>(<?php echo $this->page->get('id'); ?>) <?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?></td>
				<th>Group</th>
				<td><?php echo $this->escape(stripslashes($this->page->get('group_cn'))); ?></td>
			</tr>
			<tr>
		</tbody>
	</table>

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('Search'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search...'); ?>" />
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Revision', 'revision', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Edit Summary', 'summary', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Approved', 'approved', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Minor edit', 'minor_edit', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
			$color_access = 'style="color: black;"';
			$class = 'trashed';
			$task = '0';
			$alt = JText::_('Trashed');
		break;
		
		case '1':
			$color_access = 'style="color: green;"';
			$class = 'approved';
			$task = '0';
			$alt = JText::_('Approved');
			break;
		case '0':
			$color_access = 'style="color: red;"';
			$class = 'unapprove';
			$task = '1';
			$alt = JText::_('Not approved');
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
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Edit revision'); ?>">
						<?php echo JText::sprintf('Revision %s', $this->escape(stripslashes($row->get('version')))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo JText::sprintf('Revision %s', $this->escape(stripslashes($row->get('version')))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('summary'))); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a <?php echo $color_access; ?> class="<?php echo $class; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=approve&amp;id=<?php echo $row->get('id'); ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;approve=<?php echo $task; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Set state'); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span <?php echo $color_access; ?> class="<?php echo $class; ?>">
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