<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Tools' ), 'tools.png' );

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
		<caption><?php echo $this->hostname; ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('Status'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
<?php
if ($this->output)
{
	foreach ($this->output as $line)
	{
		echo "$line<br />\n";
	}
}
?>
				</td>
			</td>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>