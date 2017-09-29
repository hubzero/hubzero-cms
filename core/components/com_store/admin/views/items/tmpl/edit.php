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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Store\Helpers\PErmissions::getActions('item');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

Toolbar::title(Lang::txt('COM_STORE_MANAGER') . $text, 'store');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('item');

$created = NULL;
if (intval($this->row->created) <> 0)
{
	$created = Date::of($this->row->created)->toLocal(Lang::txt('COM_STORE_DATE_FORMAT_HZ1'));
}

?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo isset($this->row->id) ? Lang::txt('COM_STORE_STORE') . ' ' . Lang::txt('COM_STORE_ITEM') . ' #' . $this->row->id . ' ' . Lang::txt('COM_STORE_DETAILS') : Lang::txt('COM_STORE_NEW_ITEM'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-category"><?php echo Lang::txt('COM_STORE_CATEGORY'); ?>:</label><br />
					<select name="category" id="field-category">
						<option value="service"<?php if ($this->row->category == 'service') { echo ' selected="selected"'; } ?>>Service</option>
						<option value="wear"<?php if ($this->row->category == 'wear') { echo ' selected="selected"'; } ?>>Wear</option>
						<option value="office"<?php if ($this->row->category == 'office') { echo ' selected="selected"'; } ?>>Office</option>
						<option value="fun"<?php if ($this->row->category == 'fun') { echo ' selected="selected"'; } ?>>Fun</option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-price"><?php echo Lang::txt('COM_STORE_PRICE'); ?>:</label>
					<input type="text" name="price" id="field-price" value="<?php echo $this->escape(stripslashes($this->row->price)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_STORE_TITLE'); ?>:</label>
					<input type="text" name="title" id="field-title" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_STORE_WARNING_DESCR'); ?>">
					<label for="field-description"><?php echo Lang::txt('COM_STORE_DESCRIPTION'); ?>:</label>
					<textarea name="description" id="field-description" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_STORE_WARNING_DESCR'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STORE_OPTIONS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="published" id="field-published" value="1" <?php echo ($this->row->published) ? 'checked="checked"' : ''; ?> />
					<label for="field-published"><?php echo Lang::txt('COM_STORE_PUBLISHED'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="checkbox" name="available" id="field-available" value="1" <?php echo ($this->row->available) ? 'checked="checked"' : ''; ?> />
					<label for="field-available"><?php echo ucfirst(Lang::txt('COM_STORE_INSTOCK')); ?></label>
				</div>
				<div class="input-wrap">
					<input type="checkbox" name="featured" id="field-featured" value="1" <?php echo ($this->row->featured) ? 'checked="checked"' : ''; ?> />
					<label for="field-featured"><?php echo Lang::txt('COM_STORE_FEATURED'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="field-sizes"><?php echo Lang::txt('COM_STORE_AV_SIZES'); ?>:</label><br />
					<input type="text" name="sizes" id="field-sizes" size="15" value="<?php echo (isset($this->row->size)) ? $this->escape(stripslashes($this->row->size)) : '' ; ?>" /><br /><?php echo Lang::txt('COM_STORE_SAMPLE_SIZES'); ?>:
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STORE_PICTURE'); ?></span></legend>
			<?php
				if ($this->row->id != 0) {
			?>
				<iframe style="width: 100%" height="350" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&tmpl=component&id=' . $this->row->id); ?>"></iframe>
			<?php
				} else {
					echo '<p class="alert">' . Lang::txt('COM_STORE_MUST_BE_SAVED_BEFORE_PICTURE') . '</p>';
				}
			?>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
