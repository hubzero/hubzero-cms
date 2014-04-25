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

JToolBarHelper::title(JText::_('Success Story') . ': ' . $text, 'feedback.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor = JEditor::getInstance();

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

			<fieldset>
				<legend><?php if (!$this->row->id) { echo JText::_('FEEDBACK_CHOOSE_WHERE_TO_SAVE'); } else { echo JText::_('FEEDBACK_SAVE_QUOTE'); } ?></legend>

				<div class="input-wrap">
					<?php if ($this->type == 'regular') { ?>
						<input type="checkbox" name="replacequote" id="field-replacequote" value="1" checked="checked" /> 
						<label for="field-replacequote"><?php if ($this->row->id) { echo JText::_('FEEDBACK_REPLACE_ORIGINAL_QUOTE'); } else {echo JText::_('FEEDBACK_SAVE_IN_ARCHIVE');} ?></label><br />
						<br />
						<?php echo JText::_('Choosing one of the below options will <strong>copy</strong> the contents of this quote to the "selected" section.'); ?>
						<br />
						<br />
					<?php } ?>

					<input type="checkbox" name="notable_quotes" id="field-notable_quotes" value="1" <?php if ($this->type =='selected' && $this->row->notable_quotes == 1)  { echo 'checked="checked"'; } ?> /> 
					<label for="field-notable_quotes"><?php echo JText::_('FEEDBACK_SELECT_FOR_QUOTES'); ?></label>
					<br />
					<input type="checkbox" name="flash_rotation" id="field-flash_rotation" value="1" <?php if ($this->type =='selected' && $this->row->flash_rotation == 1)  { echo 'checked="checked"'; } ?> /> 
					<label for="field-flash_rotation"><?php echo JText::_('FEEDBACK_SELECT_FOR_ROTATION'); ?></label>
				</div>
			</fieldset>

			<div class="input-wrap">
				<label for="field-fullname"><?php echo JText::_('FEEDBACK_FULL_NAME'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<input type="text" name="fullname" id="field-fullname" value="<?php echo $this->escape(stripslashes($this->row->fullname)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-org"><?php echo JText::_('FEEDBACK_ORGANIZATION'); ?>:</label><br />
				<input type="text" name="org" id="field-org" value="<?php echo $this->escape(stripslashes($this->row->org)); ?>" />
			</div>

			<div class="input-wrap" data-hint="JText::_('FEEDBACK_USER_ID_EXPLANATION')">
				<label for="field-userid"><?php echo JText::_('FEEDBACK_USER_ID'); ?>:</label><br />
				<input type="text" name="userid" id="field-userid" value="<?php echo $this->escape(stripslashes($this->row->userid)); ?>" <?php if ($this->row->id && $this->row->userid!=0) { echo 'readonly="true"'; } ?> />
				<?php
					if (!$this->row->id) {
						echo '<span class="hint">' . JText::_('FEEDBACK_USER_ID_EXPLANATION') . '</span>';
					}
				?>
			</div>

		<?php if ($this->type == 'regular') { ?>
			<fieldset>
				<legend><?php echo JText::_('FEEDBACK_AUTHOR_CONSENTS'); ?>:</legend>

				<div class="input-wrap">
					<input type="checkbox" name="publish_ok" id="publish_ok" value="1" <?php if ($this->row->publish_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo ('disabled="disabled"'); } ?>  />
					<label for="publish_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />

					<input type="checkbox" name="contact_ok" id="contact_ok" value="1" <?php if ($this->row->contact_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo ('disabled="disabled"'); } ?> />
					<label for="contact_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
				</div>
			</fieldset>
		<?php } else { ?>
			<div class="input-wrap" data-hint="<?php echo JText::_('FEEDBACK_SHORT_QUOTE_NOTE'); ?>">
				<label for="field-short_quote"><?php echo JText::_('FEEDBACK_SHORT_QUOTE'); ?>:</label><br />
				<?php echo $editor->display('short_quote', $short_quote, '', '', '40', '10', false, 'field-short_quote'); ?>
				<span class="hint"><?php echo JText::_('FEEDBACK_SHORT_QUOTE_NOTE'); ?></span>

				<input type="hidden" name="publish_ok" id="publish_ok" value="1" />
				<input type="hidden" name="contact_ok" id="contact_ok" value="1" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('Mini quote is limited to 150 characters to appear on frontpage random quote module'); ?>">
				<label for="miniquote"><?php echo JText::_('Mini Quote'); ?>:</label><br />
				<input type="text" name="miniquote" id="miniquote" value="<?php echo $this->escape($miniquote); ?>" maxlength="150" />
				<span class="hint"><?php echo JText::_('Mini quote is limited to 150 characters to appear on frontpage random quote module'); ?></span>
			</div>
		<?php } ?>

			<div class="input-wrap">
				<label for="field-quote"><?php echo JText::_('FEEDBACK_FULL_QUOTE'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<?php echo $editor->display('quote',  stripslashes($this->row->quote) , '', '', '50', '10', false, 'field-quote'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-date"><?php echo JText::_('FEEDBACK_QUOTE_SUBMITTED'); ?>:</label><br />
				<input type="text" name="date" id="field-date" value="<?php echo $this->escape($this->row->date); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?>">
				<label for="field-notes"><?php echo JText::_('FEEDBACK_EDITOR_NOTES'); ?>:</label><br />
				<?php echo $editor->display('notes',  stripslashes($this->row->notes) , '', '', '50', '10', false, 'field-notes'); ?>
				<span class="hint"><?php echo JText::_('FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
<!--
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
-->
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="<?php echo $this->type ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
