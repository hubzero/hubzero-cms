<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editpage' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS_PAGE' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'user.png' );
JToolBarHelper::save('savepage');
JToolBarHelper::cancel('cancelpage');

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
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2><?php echo stripslashes($this->event->title); ?></h2>
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PAGE'); ?></legend>
			
			<input type="hidden" name="event" value="<?php echo $this->event->id; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="savepage" />
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('TITLE'); ?>:</label></td>
						<td><input type="text" name="title" id="title" value="<?php echo htmlentities(stripslashes($this->page->title), ENT_QUOTES); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="alias"><?php echo JText::_('ALIAS'); ?>:</label></td>
						<td>
							<input type="text" name="alias" id="alias" value="<?php echo stripslashes($this->page->alias); ?>" size="50" />
							<br /><span>A short identifier for this page. Ex: "agenda". Alpha-numeric characters only. No spaces.</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="pagetext"><?php echo JText::_('PAGE_TEXT'); ?>:</label><br />
							<?php echo $editor->display('pagetext', htmlentities(stripslashes($this->page->pagetext)), '100%', '350px', '40', '10'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<table>
			<tbody>
				<tr>
					<th>Ordering</th>
					<td><?php echo $this->page->ordering; ?></td>
				</tr>
				<tr>
					<th>Created</th>
					<td><?php echo $this->page->created; ?></td>
				</tr>
				<tr>
					<th>Created by</th>
					<td><?php echo $this->page->created_by; ?></td>
				</tr>
				<tr>
					<th>Last Modified</th>
					<td><?php echo $this->page->modified; ?></td>
				</tr>
				<tr>
					<th>Modified by</th>
					<td><?php echo $this->page->modified_by; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>