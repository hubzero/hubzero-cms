<?php

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter Template') . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo $text; ?> Campaign Template</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">Name</td>
					<td><input type="text" name="template[name]" value="<?php echo $this->template['name']; ?>" /></td>
				</tr>
				<tr>
					<td class="key">Template</td>
					<td><textarea name="template[template]" rows="30"><?php echo $this->template['template']; ?></textarea>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="template[id]" value="<?php echo $this->template['id']; ?>" />
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="template" />
	<input type="hidden" name="task" value="save" />
</form>