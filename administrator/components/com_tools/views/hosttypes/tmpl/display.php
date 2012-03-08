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
				<th scope="col"><?php echo JText::_('Name'); ?></th>
				<th scope="col"><?php echo JText::_('Bit#'); ?></th>
				<th scope="col"><?php echo JText::_('Description'); ?></th>
				<th scope="col"><?php echo JText::_('References'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->rows) 
{
	foreach ($this->rows as $row)
	{
		if ($row->value > 0) 
		{
			$bit = log($row->value)/log(2);
		} 
		else 
		{
			$bit = '';
		}
?>
			<tr>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;item=<?php echo $row->name; ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
				</td>
				<td>
					<?php echo $bit; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;item=<?php echo $row->name; ?>">
						<span><?php echo $this->escape($row->description); ?></span>
					</a>
				</td>
				<td>
					<?php echo $row->refs; ?>
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