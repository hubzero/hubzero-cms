<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Tools' ), 'tools.png' );
JToolBarHelper::preferences('com_tools', '550');
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<!-- 
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SEARCH'); ?></label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	<div class="clr"></div>
	-->

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th colspan="7">
					<?php echo $this->escape($this->tool->id); ?> - <?php echo $this->escape(stripslashes($this->tool->title)); ?> (<?php echo $this->escape($this->tool->toolname); ?>)
				</th>
			</tr>
			<tr>
				<th scope="col"></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('TOOLID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('TOOLINSTANCE'), 'toolname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('TOOLVERSION'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('TOOLREVISION'), 'registered', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('TOOLSTATE'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
	$row = &$this->rows[$i];
	
	switch ($row['state'])
	{
		case 0: $state = 'unpublished'; break;
		case 1: $state = 'registered';  break;
		case 2: $state = 'created';     break;
		case 3: $state = 'uploaded';    break;
		case 4: $state = 'installed';   break;
		case 5: $state = 'updated';     break;
		case 6: $state = 'approved';    break;
		case 7: $state = 'published';   break;
		case 8: $state = 'retired';     break;
		case 9: $state = 'abandoned';   break;
		default: $state = 'unknown';    break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="radio" name="id" id="cb<?php echo $i; ?>" value="<?php echo $row['id'] ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row['id']); ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;version=<?php echo $row['id']; ?>&amp;id=<?php echo $this->tool->id; ?>">
						<?php echo $this->escape(stripslashes($row['instance'])); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row['version']); ?>
				</td>
				<td>
					<?php echo $this->escape($row['revision']); ?>
				</td>
				<td>
					<span class="state <?php echo $state; ?>" title="<?php echo $this->escape(JText::_(strtoupper($this->option) . '_' . strtoupper($state))); ?>">
						<span><?php echo $this->escape(JText::_(strtoupper($this->option) . '_' . strtoupper($state))); ?></span>
					</span>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>