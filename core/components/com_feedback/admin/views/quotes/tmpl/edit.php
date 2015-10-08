<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Feedback\Helpers\Permissions::getActions('quote');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FEEDBACK') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('quote');

$short_quote = stripslashes($this->row->get('short_quote'));
$miniquote   = stripslashes($this->row->get('miniquote'));
if (!$short_quote)
{
	$short_quote =  substr(stripslashes($this->row->get('quote')), 0, 270);
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

	<?php echo $this->editor()->save('text'); ?>

	// form field validation
	if ($('#field-fullname').val() == '') {
		alert('<?php echo Lang::txt('COM_FEEDBACK_AUTHOR_MUST_HAVE_NAME'); ?>');
	} else if ($('#field-org').val() == '') {
		alert('<?php echo Lang::txt('COM_FEEDBACK_AUTHOR_MUST_HAVE_AFFILIATION'); ?>');
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
				$('#uploadImages').append('<img style="margin-left: 15px" src="' + e.target.result + '" width="100" height="100" alt="" />');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_FEEDBACK_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="fields[notable_quote]" id="field-notable_quote" value="1" <?php if ($this->row->get('notable_quote') == 1) { echo 'checked="checked"'; } ?> />
					<label for="field-notable_quote"><?php echo Lang::txt('COM_FEEDBACK_SELECT_FOR_QUOTES'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-fullname"><?php echo Lang::txt('COM_FEEDBACK_FULL_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[fullname]" id="field-fullname" value="<?php echo $this->escape(stripslashes($this->row->get('fullname'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-org"><?php echo Lang::txt('COM_FEEDBACK_ORGANIZATION'); ?>:</label><br />
					<input type="text" name="fields[org]" id="field-org" value="<?php echo $this->escape(stripslashes($this->row->org)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION'); ?>">
					<label for="field-user_id"><?php echo Lang::txt('COM_FEEDBACK_USER_ID'); ?>:</label><br />
					<input type="text" name="fields[user_id]" id="field-user_id" value="<?php echo $this->escape(stripslashes($this->row->get('user_id'))); ?>" <?php if ($this->row->get('id') && $this->row->get('user_id')) { echo 'readonly="readonly"'; } ?> />
					<?php
						if (!$this->row->get('id'))
						{
							echo '<span class="hint">' . Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION') . '</span>';
						}
					?>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENTS'); ?>:</legend>

					<div class="input-wrap">
						<input type="checkbox" name="fields[publish_ok]" id="publish_ok" value="1" <?php if ($this->row->get('publish_ok') == 1) { echo ' checked="checked"'; } if ($this->row->get('id')) { echo (' disabled="disabled"'); } ?>  />
						<label for="publish_ok"><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />

						<input type="checkbox" name="fields[contact_ok]" id="contact_ok" value="1" <?php if ($this->row->get('contact_ok') == 1) { echo ' checked="checked"'; } if ($this->row->get('id')) { echo (' disabled="disabled"'); } ?> />
						<label for="contact_ok"><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
					</div>
				</fieldset>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?>">
					<label for="field-short_quote"><?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE'); ?>:</label><br />
					<?php echo $this->editor('fields[short_quote]', $short_quote, 40, 10, 'field-short_quote'); ?>
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT'); ?>">
					<label for="miniquote"><?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE'); ?>:</label><br />
					<input type="text" name="fields[miniquote]" id="miniquote" value="<?php echo $this->escape($miniquote); ?>" maxlength="150" />
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-quote"><?php echo Lang::txt('COM_FEEDBACK_FULL_QUOTE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[quote]', stripslashes($this->row->get('quote')), 50, 10, 'field-quote'); ?>
				</div>

				<div class="input-wrap">
					<label for="field-date"><?php echo Lang::txt('COM_FEEDBACK_QUOTE_SUBMITTED'); ?>:</label><br />
					<input type="text" name="fields[date]" id="field-date" value="<?php echo $this->escape($this->row->get('date', Date::toSql())); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?>">
					<label for="field-notes"><?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES'); ?>:</label><br />
					<?php echo $this->editor('fields[notes]', stripslashes($this->row->get('notes')), 50, 10, 'field-notes'); ?>
					<span class="hint"><?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_FEEDBACK_PICTURE'); ?></span></legend>

				<div class="input-wrap">
					<?php
					$pictures = $this->row->files();

					foreach ($pictures as $counter => $picture)
					{
						list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());

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

						$img = substr($picture->getPathname(), strlen(PATH_ROOT));
						?>
						<div id="picture-<?php echo $counter; ?>">
							<input type="hidden" name="existingPictures[<?php echo $counter; ?>]" id="existingPictures<?php echo $counter; ?>" value="<?php echo $picture->getFilename(); ?>" />
							<a class="fancybox-inline" href="<?php echo $img; ?>">
								<img src="<?php echo $img; ?>" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" alt="" />
							</a>
							<button type="button" class="delete-image" id="<?php echo $counter; ?>"><?php echo Lang::txt('COM_FEEDBACK_DELETE'); ?></button>
						</div>
						<br />
						<?php
					}
					?>
					<input id="imgInp" type="file" name="files[]" multiple="multiple" />
					<div id="uploadImages"></div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
