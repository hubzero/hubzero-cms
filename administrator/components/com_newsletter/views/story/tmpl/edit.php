<?php

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter '.$this->type.' Story') . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend>Campaign Story</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">Type</td>
					<td>
						<?php echo $this->type; ?> Story
						<input type="hidden" name="type" value="<?php echo strtolower($this->type) ;?>" />
						<input type="hidden" name="story[id]" value="<?php echo $this->story['id']; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">Title</td>
					<td><input type="text" name="story[title]" value="<?php echo $this->story['title']; ?>" /></td>
				</tr>
				<tr>
					<td class="key">Story</td>                                                                                               
					<?php 
						$params = array("full_paths"=>true); 
					?>
					<td><?php echo $editor->display("story[story]", stripslashes($this->story['story']), '100%', '300px', '50', '10', true, $params); ?></td>
				</tr>
			</tbody>
		</table>	
	</fielset>
	<input type="hidden" name="story[campaign]" value="<?php echo $this->campaign; ?>" />
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="story" />
	<input type="hidden" name="task" value="save" />
</form>	