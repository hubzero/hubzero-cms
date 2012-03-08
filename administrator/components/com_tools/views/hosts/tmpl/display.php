<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Tools' ), 'generic.png' );

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
				<th scope="col"><?php echo JText::_('Hostname'); ?></th>
				<th scope="col"><?php echo JText::_('Provisions'); ?></th>
				<th scope="col"><?php echo JText::_('Status'); ?></th>
				<th scope="col"><?php echo JText::_('Uses'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->rows) 
{
	foreach ($this->rows as $row)
	{
		$list = array();
		for ($i=0; $i<count($this->results); $i++)
		{
			$r = $this->results[$i];
			$list[$r->name] = (int)$r->value & (int)$row->provisions;
		}
?>
			<tr>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;hostname=<?php echo $row->hostname; ?>">
						<span><?php echo $row->hostname; ?></span>
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
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=toggle&amp;hostname=<?php echo $row->hostname; ?>&amp;item=<?php echo $key; ?>">
						<span><?php echo $key; ?></span>
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
						<span><?php echo $row->status; ?></span>
					</a>
				</td>
				<td>
					<?php echo $row->uses; ?>
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

	<?php echo JHTML::_( 'form.token' ); ?>
</form>