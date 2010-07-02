<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'MEETINGS' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
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

	// do field validation
	if ($('title').value == '') {
		alert( '<?php echo JText::_('ERROR_MEETING_MUST_HAVE_TITLE'); ?>' );
	} else if ($('meeting_id').value == '') {
		alert( '<?php echo JText::_('ERROR_MEETING_MUST_HAVE_OWNER'); ?>' );
	} else if ($('date_begin').value == '') {
		alert( '<?php echo JText::_('ERROR_MEETING_MUST_HAVE_START'); ?>' );
	} else if ($('date_end').value == '') {
		alert( '<?php echo JText::_('ERROR_MEETING_MUST_HAVE_END'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MEETING_LEGEND_DETAILS'); ?></legend>
			
			<table class="admintable">
			 <tbody>
			  <tr>
			   <td class="key"><label for="title"><?php echo JText::_('MEETING_LABEL_TITLE'); ?>: <span class="required">*</span></label></td>
			   <td><input type="text" name="meeting[title]" id="title" size="30" maxlength="250" value="<?php echo $this->row->title; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="owner"><?php echo JText::_('MEETING_LABEL_OWNER'); ?>: <span class="required">*</span></label></td>
			   <td><input type="text" name="meeting[owner]" id="owner" size="30" maxlength="250" value="<?php echo $this->row->owner; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="phone"><?php echo JText::_('MEETING_LABEL_CALL'); ?>:</label></td>
			   <td><input type="text" name="meeting[phone]" id="phone" size="30" maxlength="250" value="<?php echo $this->row->phone; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="url"><?php echo JText::_('MEETING_LABEL_URL'); ?>:</label></td>
			   <td><input type="text" name="meeting[url]" id="url" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="date_begin"><?php echo JText::_('MEETING_LABEL_START'); ?>: <span class="required">*</span></label></td>
			   <td><input type="text" name="meeting[date_begin]" id="date_begin" size="30" maxlength="250" value="<?php echo $this->row->date_begin; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="date_end"><?php echo JText::_('MEETING_LABEL_END'); ?>: <span class="required">*</span></label></td>
			   <td><input type="text" name="meeting[date_end]" id="date_end" size="30" maxlength="250" value="<?php echo $this->row->date_end; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="time_zone_A"><?php echo JText::_('MEETING_LABEL_TIMEZONE'); ?>: <span class="required">*</span></label></td>
			   <td><select name="meeting[time_zone_A]" id="time_zone_A"><?php 
				foreach ($this->timezones as $avalue => $alabel) 
				{
					$selected = ($avalue == $this->timezone || $alabel == $this->timezone)
							  ? ' selected="selected"'
							  : '';
					echo ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
				}
			?></select></td>
			  </tr>
	 		  <tr>
			   <td class="key"><label for="description"><?php echo JText::_('MEETING_LABEL_DESCRIPTION'); ?>:<br /><?php echo JText::_('MEETING_DESCRIPTION_HINT'); ?></label></td>
			   <td><?php echo $editor->display('meeting[description]', stripslashes($this->row->description), '360px', '200px', '30', '10'); ?></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('MEETING_LEGEND_ATTENDEES'); ?></legend>
			
			<p><?php echo JText::_('MEETING_ATTENDEES_HINT'); ?></p>

			<table class="admintable">
			 <tbody>
			  <tr>
			   <td class="key"><label for="hosts"><?php echo JText::_('MEETING_LABEL_HOSTS'); ?>:</label></td>
			   <td><input type="text" name="meeting[hosts]" size="30" id="hosts" maxlength="250" value="<?php echo $this->row->hosts; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="presenters"><?php echo JText::_('MEETING_LABEL_PRESENTERS'); ?>:</label></td>
			   <td><input type="text" name="meeting[presenters]" size="30" id="presenters" maxlength="250" value="<?php echo $this->row->presenters; ?>" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="participants"><?php echo JText::_('MEETING_LABEL_PARTICIPANTS'); ?>:</label></td>
			   <td><input type="text" name="meeting[participants]" size="30" id="participants" maxlength="250" value="<?php echo $this->row->participants; ?>" /></td>
			  </tr>
	 		  <tr>
			   <td class="key"><label for="guests"><?php echo JText::_('MEETING_LABEL_GUESTS'); ?>:</label></td>
			   <td><?php echo $editor->display('meeting[guests]', stripslashes($this->row->guests), '360px', '100px', '30', '6'); ?></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MEETING_LEGEND_PUBLISHING'); ?></legend>
			
			<table class="admintable">
			 <tbody>
			  <tr>
			   <td class="key"><label for="notify"><?php echo JText::_('MEETING_LABEL_NOTIFY'); ?>:</label></td>
			   <td><input type="checkbox" name="meeting[notify]" id="notify" value="yes" checked="checked" /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="access"><?php echo JText::_('MEETING_LABEL_PRIVATE'); ?>:</label></td>
			   <td><input type="checkbox" name="meeting[access]" value="denied"<?php if ($this->row->access == 'denied') { echo ' checked="checked"'; } ?>  />
			   <?php echo JText::_('MEETING_PRIVATE_HINT'); ?></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="expired"><?php echo JText::_('MEETING_LABEL_EXPIRED'); ?>:</label></td>
			   <td><input type="checkbox" name="meeting[expired]" value="true"<?php if ($this->row->expired == 'true') { echo ' checked="checked"'; } ?>  /></td>
			  </tr>
			  <tr>
			   <td class="key"><label for="deleted"><?php echo JText::_('MEETING_LABEL_DELETED'); ?>:</label></td>
			   <td><input type="checkbox" name="meeting[deleted]" value="true"<?php if ($this->row->deleted == 'true') { echo ' checked="checked"'; } ?>  /></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="meeting[images]" value="" />
	<input type="hidden" name="meeting[date_created]" value="<?php echo $this->row->date_created; ?>" />
	<input type="hidden" name="meeting[date_modified]" value="<?php echo $this->row->date_modified; ?>" />
	<input type="hidden" name="meeting[date_deleted]" value="<?php echo $this->row->date_deleted; ?>" />
	<input type="hidden" name="meeting[duration]" value="<?php echo $this->row->duration; ?>" />
	<input type="hidden" name="meeting[time_zone]" value="<?php echo $this->row->time_zone; ?>" />
	<input type="hidden" name="meeting[id]" id="meeting_id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="meeting[room_id]" value="<?php echo $this->row->room_id; ?>" />
	<input type="hidden" name="meeting[hits]" value="<?php echo $this->row->hits; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>