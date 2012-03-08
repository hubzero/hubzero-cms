<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Tools' ), 'generic.png' );
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
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><?php echo JText::_('Tables'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=hosts">
						<?php echo Jtext::_('Modify host table'); ?>
					</a>
				</td>
			</tr>
			<tr class="row1">
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=hosttypes">
						<?php echo Jtext::_('Modify hosttype table'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>