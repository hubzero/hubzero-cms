<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editcat' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('KNOWLEDGE_BASE').': '.JText::_('CATEGORY').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save( 'savecat', JText::_('SAVE') );
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm( <?php echo JText::_('RESET_HITS_WARNING'); ?> )){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert( <?php echo JText::_('ERROR_MISSING_TITLE'); ?> );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('PARENT_CATEGORY'); ?>:</label></td>
						<td><?php echo KbHtml::sectionSelect( $this->sections, $this->row->section, 'section' ); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
						<td><input type="text" name="title" size="30" maxlength="100" value="<?php echo stripslashes($this->row->title); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
						<td><input type="text" name="alias" size="30" maxlength="100" value="<?php echo stripslashes($this->row->alias); ?>" /></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align: top;"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
						<td><?php
						echo $editor->display('description', stripslashes($this->row->description), '360px', '200px', '50', '10'); 
						?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PARAMETERS'); ?></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="state"><?php echo JText::_('PUBLISH'); ?>:</label></td>
						<td><input type="checkbox" name="state" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align: top;"><label><?php echo JText::_('ACCESS_LEVEL'); ?>:</label></td>
						<td><?php echo JHTML::_('list.accesslevel', $this->row); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('STATE'); ?>:</label></td>
						<td><?php echo ($this->row->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savecat" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>