<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('Tools'), 'tools.png');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Hostname', 'hostname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Provisions', 'provisions', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Status', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Uses', 'uses', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Zone', 'zone_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">Broken Containers</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows) 
{
	$i = 0;
	foreach ($this->rows as $row)
	{
		$list = array();
		for ($k=0; $k<count($this->hosttypes); $k++)
		{
			$r = $this->hosttypes[$k];
			$list[$r->name] = (int)$r->value & (int)$row->provisions;
		}
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->hostname; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;hostname=<?php echo $row->hostname; ?>">
						<span><?php echo $this->escape($row->hostname); ?></span>
					</a>
				</td>
				<td>
<?php 
					foreach ($list as $key => $value)
					{
						if ($value != '0') 
						{
							echo '<strong>';
						}
?>
					<a class="<?php echo ($value != '0') ? 'active' : 'inactive'; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=toggle&amp;hostname=<?php echo $row->hostname; ?>&amp;item=<?php echo $key; ?>">
						<span><?php echo $this->escape($key); ?></span>
					</a>
<?php
						if ($value != '0') 
						{
							echo '</strong>';
						}
						echo '<br />';
					}
?>
				</td>
				<td>
					<a class="state <?php echo ($row->status == 'up') ? 'publish' : 'unpublish'; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=status&amp;hostname=<?php echo $row->hostname; ?>">
						<span><?php echo $this->escape($row->status); ?></span>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->uses); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->zone)); ?>
				</td>
				<td>
					<?php
						$db = MwUtils::getMWDBO();
						$sql = "select count(*) from display where status='broken' and hostname='{$row->hostname}'";
						$db->setQuery($sql);
						$bc = $db->loadResult();
						echo $bc;
					?>
				</td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
