<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = FeedbackHelper::getActions('quote');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('Success Story') . ': <small><small>[ ' . $text . ' ]</small></small>', 'feedback.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

if ($this->type != 'regular') 
{
	$short_quote = stripslashes($this->row->short_quote);
	$miniquote = stripslashes($this->row->miniquote);
	if (!$short_quote) 
	{
		$short_quote =  substr(stripslashes($this->row->quote), 0, 270);
	}
	if (!$miniquote) 
	{
		$miniquote =  substr(stripslashes($short_quote), 0, 150);
	}

	if (strlen($short_quote) >= 271) 
	{
		$short_quote = $short_quote . '...';
	}
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('item-form');
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// form field validation
	if (form.fullname.value == '') {
		alert('<?php echo JText::_('FEEDBACK_AUTHOR_MUST_HAVE_NAME'); ?>');
	} else if (form.org.value == '') {
		alert('<?php echo JText::_('FEEDBACK_AUTHOR_MUST_HAVE_AFFILIATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}

function getAuthorImage() 
{
	var filew = window.filer;
	if (filew) {
		var conimg = filew.document.forms['filelist'].conimg;
		if (conimg) {
			document.forms['adminForm'].elements['picture'].value = conimg.value;
		}
	}
}

function checkState(checkboxname)
{

	if (checkboxname.checked == false) {
 		checkboxname.checked = false;
	} 
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('FEEDBACK_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="save_for"> <?php if (!$this->row->id) { echo JText::_('FEEDBACK_CHOOSE_WHERE_TO_SAVE'); } else { echo JText::_('FEEDBACK_SAVE_QUOTE'); } ?> <span class="required">*</span></label></label></td> 
						<td>
<?php if ($this->type == 'regular') { ?>
						<input type="checkbox" name="replacequote" id="replacequote" value="1" checked="checked" /> <?php if ($this->row->id) { echo JText::_('FEEDBACK_REPLACE_ORIGINAL_QUOTE'); } else {echo JText::_('FEEDBACK_SAVE_IN_ARCHIVE');} ?>  <br />
							<br />
							<?php echo JText::_('Choosing one of the below options will <strong>copy</strong> the contents of this quote to the "selected" section.'); ?>
							<br />
							<br />
<?php } ?>
							<input type="checkbox" name="notable_quotes" id="notable_quotes" value="1" <?php if ($this->type =='selected' && $this->row->notable_quotes == 1)  { echo 'checked="checked"'; } ?> /> <?php echo JText::_('FEEDBACK_SELECT_FOR_QUOTES'); ?> <br />
							<input type="checkbox" name="flash_rotation" id="flash_rotation" value="1" <?php if ($this->type =='selected' && $this->row->flash_rotation == 1)  { echo 'checked="checked"'; } ?> /> <?php echo JText::_('FEEDBACK_SELECT_FOR_ROTATION'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="fullname"><?php echo JText::_('FEEDBACK_FULL_NAME'); ?>: <span class="required">*</span></label></td>
						<td><input type="text" name="fullname" id="fullname" value="<?php echo $this->escape(stripslashes($this->row->fullname)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="org"><?php echo JText::_('FEEDBACK_ORGANIZATION'); ?>:</label></td>
						<td><input type="text" name="org" id="org" value="<?php echo $this->escape(stripslashes($this->row->org)); ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="userid"><?php echo JText::_('FEEDBACK_USER_ID'); ?>:</label></td>
						<td>
							<input type="text" name="userid" id="userid" value="<?php echo $this->escape(stripslashes($this->row->userid)); ?>" size="50" <?php if ($this->row->id && $this->row->userid!=0) { echo 'readonly="true"'; } ?> />
							<?php
								if (!$this->row->id) {
									echo '<p>' . JText::_('FEEDBACK_USER_ID_EXPLANATION') . '</p>';
								}
							?>
						</td>
					</tr>
<?php if ($this->type == 'regular') { ?>
					<tr>
						<td class="key"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENTS'); ?>:</td>
						<td>
							<input type="checkbox" name="publish_ok" id="publish_ok" value="1" <?php if ($this->row->publish_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo ("disabled"); } ?>  />
							<label for="publish_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />
							<input type="checkbox" name="contact_ok" id="contact_ok" value="1" <?php if ($this->row->contact_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo ("disabled"); } ?> />
							<label for="contact_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
						</td>
					</tr>
<?php } else {  ?>
					<tr>
						<td class="key" valign="top"><label for="short_quote"><?php echo JText::_('FEEDBACK_SHORT_QUOTE'); ?>:</label></td>
						<td>
							<input type="hidden" name="publish_ok" id="publish_ok" value="1" />
							<input type="hidden" name="contact_ok" id="contact_ok" value="1" />
							<p><?php echo JText::_('FEEDBACK_SHORT_QUOTE_NOTE'); ?></p>
							<?php echo $editor->display('short_quote', $short_quote, '360px', '200px', '40', '10'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="miniquote"><?php echo JText::_('Mini Quote'); ?>:</label></td>
						<td>
							<input type="text" name="miniquote" id="miniquote" value="<?php echo $this->escape($miniquote); ?>" size="50" maxlength="150" />
							<p><?php echo JText::_('Mini quote is limited to 150 characters to appear on frontpage random quote module'); ?></p>
						</td>
					</tr>
<?php } ?>
					<tr>
						<td class="key" valign="top"><label for="quote"><?php echo JText::_('FEEDBACK_FULL_QUOTE'); ?>: <span class="required">*</span></label></td>
						<td><?php echo $editor->display('quote',  stripslashes($this->row->quote) , '350px', '200px', '50', '10'); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="date"><?php echo JText::_('FEEDBACK_QUOTE_SUBMITTED'); ?>:</label></td>
						<td><input type="text" name="date" id="date" value="<?php echo $this->escape($this->row->date); ?>"  size="50" /></td>
					</tr>
					<tr>
						<td class="key" valign="top"><label for="notes"><?php echo JText::_('FEEDBACK_EDITOR_NOTES'); ?>:</label> <p><?php echo JText::_('FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></p></td>
						<td><?php echo $editor->display('notes',  stripslashes($this->row->notes) , '350px', '200px', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('FEEDBACK_PICTURE'); ?></span></legend>
<?php
	if ($this->row->id != 0) {
		$pics = stripslashes($this->row->picture);
		$pics = explode('/', $pics);
		$file = end($pics);
?>
			<input type="hidden" name="picture" value="<?php echo $this->escape($this->row->picture); ?>" />
			<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;file=<?php echo $file; ?>&amp;id=<?php echo $this->row->userid; ?>&amp;qid=<?php echo $this->row->id; ?>&amp;type=<?php echo $this->type ?>"></iframe>
<?php
	} else {
		echo '<p class="alert">' . JText::_('FEEDBACK_MUST_BE_SAVED_BEFORE_PICTURE') . '</p>';
	}
?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="<?php echo $this->type ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
