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

$canDo = FeedbackHelperPermissions::getActions('quote');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_FEEDBACK') . ': ' . $text, 'feedback.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('quote');

jimport('joomla.html.editor');
$editor = JEditor::getInstance();

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
		alert('<?php echo JText::_('COM_FEEDBACK_AUTHOR_MUST_HAVE_NAME'); ?>');
	} else if (form.org.value == '') {
		alert('<?php echo JText::_('COM_FEEDBACK_AUTHOR_MUST_HAVE_AFFILIATION'); ?>');
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

jQuery(document).ready(function($) {
	$('.fancybox-inline').fancybox({
		padding: 0,
		helpers: {
			overlay: {
					locked: false
			}
		},
	});

	$('.fancybox-inline').on('click', function(e){
		e.preventDefault();
	});

	$('.delete-image').on('click', function(e){
		$('#picture-' + e.target.id).remove();
	});

	function readURL(input) {
		var files = Array.prototype.slice.call($(input)[0].files);
		files.forEach(function(file) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#uploadImages').append('<img style="margin-left: 15px" src="' + e.target.result + '" width="100" height="100" alt="test" />');
			}
			reader.readAsDataURL(file);
		});
	}

	$("#imgInp").change(function(e){
		$('#uploadImages').html("");
		readURL(this);
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_FEEDBACK_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="notable_quote" id="field-notable_quote" value="1" <?php if ($this->row->notable_quote == 1)  { echo 'checked="checked"'; } ?> />
				<label for="field-notable_quote"><?php echo JText::_('COM_FEEDBACK_SELECT_FOR_QUOTES'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-fullname"><?php echo JText::_('COM_FEEDBACK_FULL_NAME'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fullname" id="field-fullname" value="<?php echo $this->escape(stripslashes($this->row->fullname)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-org"><?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>:</label><br />
				<input type="text" name="org" id="field-org" value="<?php echo $this->escape(stripslashes($this->row->org)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_FEEDBACK_USER_ID_EXPLANATION'); ?>">
				<label for="field-user_id"><?php echo JText::_('COM_FEEDBACK_USER_ID'); ?>:</label><br />
				<input type="text" name="user_id" id="field-user_id" value="<?php echo $this->escape(stripslashes($this->row->user_id)); ?>" <?php if ($this->row->id && $this->row->user_id!=0) { echo 'readonly="readonly"'; } ?> />
				<?php
					if (!$this->row->id) {
						echo '<span class="hint">' . JText::_('COM_FEEDBACK_USER_ID_EXPLANATION') . '</span>';
					}
				?>
			</div>

			<fieldset>
				<legend><?php echo JText::_('COM_FEEDBACK_AUTHOR_CONSENTS'); ?>:</legend>

				<div class="input-wrap">
					<input type="checkbox" name="publish_ok" id="publish_ok" value="1" <?php if ($this->row->publish_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo (' disabled="disabled"'); } ?>  />
					<label for="publish_ok"><?php echo JText::_('COM_FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />

					<input type="checkbox" name="contact_ok" id="contact_ok" value="1" <?php if ($this->row->contact_ok == 1) { echo ' checked="checked"'; } if ($this->row->id) { echo (' disabled="disabled"'); } ?> />
					<label for="contact_ok"><?php echo JText::_('COM_FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
				</div>
			</fieldset>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?>">
				<label for="field-short_quote"><?php echo JText::_('COM_FEEDBACK_SHORT_QUOTE'); ?>:</label><br />
				<?php echo $editor->display('short_quote', $short_quote, '', '', '40', '10', false, 'field-short_quote'); ?>
				<span class="hint"><?php echo JText::_('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_FEEDBACK_MINIQUOTE_HINT'); ?>">
				<label for="miniquote"><?php echo JText::_('COM_FEEDBACK_MINIQUOTE'); ?>:</label><br />
				<input type="text" name="miniquote" id="miniquote" value="<?php echo $this->escape($miniquote); ?>" maxlength="150" />
				<span class="hint"><?php echo JText::_('COM_FEEDBACK_MINIQUOTE_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-quote"><?php echo JText::_('COM_FEEDBACK_FULL_QUOTE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo $editor->display('quote',  stripslashes($this->row->quote) , '', '', '50', '10', false, 'field-quote'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-date"><?php echo JText::_('COM_FEEDBACK_QUOTE_SUBMITTED'); ?>:</label><br />
				<input type="text" name="date" id="field-date" value="<?php echo $this->escape($this->row->date); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?>">
				<label for="field-notes"><?php echo JText::_('COM_FEEDBACK_EDITOR_NOTES'); ?>:</label><br />
				<?php echo $editor->display('notes',  stripslashes($this->row->notes) , '', '', '50', '10', false, 'field-notes'); ?>
				<span class="hint"><?php echo JText::_('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_FEEDBACK_PICTURE'); ?></span></legend>

			<div class="input-wrap">
				<?php
				$counter = 0;
				if (isset($this->pictures))
				{
					foreach ($this->pictures as $picture)
					{
						$file = $this->path . $this->id . DS . $picture;
						if (file_exists(JPATH_ROOT . $file))
						{
							$this_size = filesize(JPATH_ROOT . $file);

							list($ow, $oh, $type, $attr) = getimagesize(JPATH_ROOT . $file);

							// scale if image is bigger than 120w x120h
							$num = max($ow/120, $oh/120);
							if ($num > 1)
							{
								$mw = round($ow/$num);
								$mh = round($oh/$num);
							}
							else
							{
								$mw = $ow;
								$mh = $oh;
							}
							?>
							<div id="picture-<?php echo $counter; ?>">
								<input type="hidden" name="existingPictures[<?php echo $counter; ?>]" id="existingPictures[<?php echo $counter; ?>]" value="<?php echo $picture; ?>" />
								<a class="fancybox-inline" href="<?php echo $file; ?>">
									<img src="<?php echo $file; ?>" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" alt="" />
								</a>
								<button type="button" class="delete-image" id="<?php echo $counter; ?>"><?php echo JText::_('COM_FEEDBACK_DELETE'); ?></button>
							</div>
							<br />
							<?php
							$counter++;
						}
					}
				}
				?>
				<input id="imgInp" type="file" name="files[]" multiple="multiple" />
				<div id="uploadImages"></div>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
