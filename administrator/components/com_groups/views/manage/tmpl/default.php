<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'GROUP' ).': <small><small>[ '.JText::_('Manage').' ]</small></small>', 'user.png' );
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	// do field validation
	submitform( pressbutton );
}
</script>
<h3><?php echo $this->group->description; ?> (<?php echo $this->group->cn; ?>)</h3>
<?php
if ($this->getError()) {
	echo '<p style="color: #c00;"><strong>'.$this->getError().'</p>';
}
?>
<form action="index.php" name="adminForm" method="post">
	<fieldset>
		<label>
			<?php echo JText::_('ADD_USERNAME'); ?>
			<input type="text" name="usernames" value="" />
		</label> 
		<label>
			<?php echo JText::_('TO'); ?> 
			<select name="tbl">
				<option value="invitees"><?php echo JText::_('INVITEES'); ?></option>
				<option value="applicants"><?php echo JText::_('APPLICANTS'); ?></option>
				<option value="members" selected="selected"><?php echo JText::_('MEMBERS'); ?></option>
				<option value="managers"><?php echo JText::_('MANAGERS'); ?></option>
			</select>
		</label>
		<input type="submit" name="action" value="<?php echo JText::_('GROUP_MEMBER_ADD'); ?>" />
	</fieldset>
	<br />
<?php
	$view = new JView( array('name'=>'manage', 'layout'=>'table') );
	$view->option = $this->option;
	$view->task = $this->task;
	$view->gid = $this->group->cn;
	$view->authorized = $this->authorized;
	
	$view->groupusers = $this->invitees;
	$view->table = 'invitees';
	$view->display();
	
	$view->groupusers = $this->pending;
	$view->table = 'pending';
	$view->display();
	
	$view->groupusers = $this->managers;
	$view->table = 'managers';
	$view->display();
	
	$view->groupusers = $this->members;
	$view->table = 'members';
	$view->display();
?>
	<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>