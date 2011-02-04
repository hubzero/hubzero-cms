<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editrevision' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('Wiki').': '.JText::_('Page Revision').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save( 'saverevision', JText::_('SAVE') );
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
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
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>
		
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('Page title'); ?>:</td>
						<td><?php echo stripslashes($this->page->title); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Page name'); ?>:</td>
						<td><?php echo stripslashes($this->page->pagename); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Scope'); ?>:</td>
						<td><?php echo stripslashes($this->page->scope); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Group'); ?>:</td>
						<td><?php echo stripslashes($this->page->group); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Edit summary'); ?>:</label></td>
						<td><input type="text" name="revision[summary]" size="55" maxlength="255" value="<?php echo stripslashes($this->revision->summary); ?>" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<label><?php echo JText::_('Text'); ?>:</label><br />
							<?php echo $editor->display('revision[pagetext]', stripslashes($this->revision->pagetext), '520px', '700px', '50', '10'); ?>
						</td>
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
						<td class="key"><label for="revid"><?php echo JText::_('ID'); ?>:</label></td>
						<td><?php echo $this->revision->id; ?><input type="hidden" name="revision[id]" id="revid" value="<?php echo $this->revision->id; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="pageid"><?php echo JText::_('Page ID'); ?>:</label></td>
						<td><?php echo $this->revision->pageid; ?><input type="hidden" name="revision[pageid]" id="pageid" value="<?php echo $this->revision->pageid; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="version"><?php echo JText::_('Revision #'); ?>:</label></td>
						<td><?php echo $this->revision->version; ?><input type="hidden" name="revision[version]" id-"version" value="<?php echo $this->revision->version; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="minor_edit"><?php echo JText::_('Minor edit'); ?>:</label></td>
						<td><input type="checkbox" name="revision[minor_edit]" id="minor_edit" value="1" <?php echo $this->revision->minor_edit ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label for="approved"><?php echo JText::_('Approves'); ?>:</label></td>
						<td><input type="checkbox" name="revision[approved]" id="approved" value="1" <?php echo $this->revision->approved ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Creator'); ?>:</label></td>
						<td><?php echo JHTML::_('list.users', 'created_by', $this->revision->created_by, 0, '', 'name', 1); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="created"><?php echo JText::_('Created'); ?>:</label></td>
						<td><input type="text" name="revision[created]" id="created" size="25" maxlength="19" value="<?php echo $this->revision->created; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="saverevision" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>