<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resource Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<h3><?php echo stripslashes($this->parent->title); ?></h3>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('Choose a method for adding a new child resource'); ?></legend>
		<?php if ($this->getError()) { echo ResourcesHtml::error($this->getError()); } ?>
		
		<table class="admintable">
			<tbody>
				<tr>
					<td>
						<input type="radio" name="method" id="child_create" value="create" checked="checked" /> 
						<label for="child_create"><?php echo JText::_('Create new'); ?></label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="method" id="child_existing" value="existing" />
						<label for="child_existing"><?php echo JText::_('Add existing'); ?></label> - <?php echo JText::_('Resource ID'); ?>: <input type="text" name="childid" id="childid" value="" />
					</td>
				</tr>
				<tr>
					<td><input type="submit" name="Submit" value="<?php echo JText::_('Next >'); ?>" /></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	</fieldset>
</form>