<?php
defined('_JEXEC') or die('Restricted access');                              

JToolBarHelper::title('<a href="index.php?option='.$this->option.'">' . JText::_( 'Newsletter Campaigns' ) . '</a>', 'addedit.png');
JToolBarHelper::addNew();
JToolBarHelper::preferences($this->option, '550');
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('Campaigns'); ?></th>
				<th><?php echo JText::_('Issue #'); ?></th>
				<th>Sent?</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->newsletters as $n) : ?>
				<tr>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=campaign&task=edit&id='.$n->id); ?>">
							<?php echo $n->name; ?>
						</a>	
					</td>
					<td><?php echo $n->issue; ?></td>
					<td>
						<?php if($n->sent) : ?>
							<font color="green">Yes</font>
						<?php else : ?>
							<font color="red">No</font>
						<?php endif; ?>	
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="campaign" />
	<input type="hidden" name="task" value="add" />
</form>	