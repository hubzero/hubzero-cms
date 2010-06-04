<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'GROUP' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
JToolBarHelper::save();
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
	
	// form field validation
	if (form.description.value == '') {
		alert( <?php echo JText::_('CONTRIBUTOR_MUST_HAVE_FIRST_NAME'); ?> );
	} else if( form.cn.value == '' ) {
		alert( <?php echo JText::_('CONTRIBUTOR_MUST_HAVE_LAST_NAME'); ?> );
	} else {
		submitform( pressbutton );
	}
}
</script>
<?php
if ($this->getError()) {
	echo implode('<br />', $this->getError());
}
?>
<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>
			
			<input type="hidden" name="group[gidNumber]" value="<?php echo $this->group->gidNumber; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="firstname"><?php echo JText::_('GROUPS_ID'); ?>:</label></td>
						<td><?php echo $this->group->gidNumber; ?></td>
					</tr>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('TYPE'); ?>:</label></td>
						<td>
							<select name="group[type]">
								<option value="1"<?php echo ($this->group->type == '1') ? ' selected="selected"' : ''; ?>>hub</option>
								<option value="0"<?php echo ($this->group->type == '0') ? ' selected="selected"' : ''; ?>>system</option>
								<option value="2"<?php echo ($this->group->type == '2') ? ' selected="selected"' : ''; ?>>project</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="firstname"><?php echo JText::_('CN'); ?>:</label></td>
						<td><input type="text" name="group[cn]" id="cn" value="<?php echo stripslashes($this->group->cn); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="description"><?php echo JText::_('GROUPS_TITLE'); ?>:</label></td>
						<td><input type="text" name="group[description]" id="description" value="<?php echo htmlentities(stripslashes($this->group->description)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="join_policy"><?php echo JText::_('GROUPS_JOIN_POLICY'); ?>:</label></td>
						<td>
							<input type="radio" name="group[join_policy]" value="0"<?php if ($this->group->join_policy == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_PUBLIC'); ?><br />
							<input type="radio" name="group[join_policy]" value="1"<?php if ($this->group->join_policy == 1) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_RESTRICTED'); ?><br />
							<input type="radio" name="group[join_policy]" value="2"<?php if ($this->group->join_policy == 2) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_INVITE'); ?><br />
							<input type="radio" name="group[join_policy]" value="3"<?php if ($this->group->join_policy == 3) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_JOIN_POLICY_CLOSED'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="privacy"><?php echo JText::_('GROUPS_PRIVACY'); ?>:</label></td>
						<td>
							<input type="radio" name="group[privacy]" value="0"<?php if ($this->group->privacy == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?><br />
							<input type="radio" name="group[privacy]" value="1"<?php if ($this->group->privacy == 1) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?><br />
							<input type="radio" name="group[privacy]" value="4"<?php if ($this->group->privacy == 4) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="access"><?php echo JText::_('GROUPS_CONTENT_PRIVACY'); ?>:</label></td>
						<td>
							<input type="radio" name="group[access]" value="0"<?php if ($this->group->access == 0) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?><br />
							<input type="radio" name="group[access]" value="3"<?php if ($this->group->access == 3) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?><br />
							<input type="radio" name="group[access]" value="4"<?php if ($this->group->access == 4) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="restrict_msg"><?php echo JText::_('GROUPS_EDIT_CREDENTIALS'); ?>:</label></td>
						<td>
							<?php echo $editor->display('group[restrict_msg]', htmlentities(stripslashes($this->group->restrict_msg)), '360px', '200px', '40', '10'); ?>
						</td>
					</tr>
		 			<tr>
						<td class="key" valign="top"><label for="public_desc"><?php echo JText::_('GROUPS_EDIT_PUBLIC_TEXT'); ?>:</label></td>
						<td>
							<?php echo $editor->display('group[public_desc]', htmlentities(stripslashes($this->group->public_desc)), '360px', '200px', '40', '10'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="private_desc"><?php echo JText::_('GROUPS_EDIT_PRIVATE_TEXT'); ?>:</label></td>
						<td>
							<?php echo $editor->display('group[private_desc]', htmlentities(stripslashes($this->group->private_desc)), '360px', '200px', '40', '10'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<p><?php echo JText::_('GROUPS_ACCESS_EXPLANATION'); ?></p>
		<dl>
			<dt><?php echo JText::_('GROUPS_ACCESS_PUBLIC'); ?></dt>
			<dd><?php echo JText::_('GROUPS_ACCESS_PUBLIC_EXPLANATION'); ?></dd>
			<dt><?php echo JText::_('GROUPS_ACCESS_PROTECTED'); ?></dt>
			<dd><?php echo JText::_('GROUPS_ACCESS_PROTECTED_EXPLANATION'); ?></p>
			<dt><?php echo JText::_('GROUPS_ACCESS_PRIVATE'); ?></dt>
			<dd><?php echo JText::_('GROUPS_ACCESS_PRIVATE_EXPLANATION'); ?></dd>
		</dl>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>