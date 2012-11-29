<?php
defined('_JEXEC') or die('Restricted access');                              

JToolBarHelper::title('<a href="index.php?option='.$this->option.'">' . JText::_( 'Newsletter Templates' ) . '</a>', 'addedit.png');
JToolBarHelper::addNew();
?>

<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th>Campaign Templates</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->templates as $t) : ?>
				<tr>
					<td><a href="index.php?option=com_newsletter&amp;controller=template&amp;task=edit&amp;id=<?php echo $t->id; ?>"><?php echo $t->name; ?></a></td>
				</tr>
			<?php endforeach; ?>	
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="template" />
	<input type="hidden" name="task" value="add" />
</form>	