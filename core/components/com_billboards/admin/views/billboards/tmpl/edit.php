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

use Components\Billboards\Models\Collection;
use Components\Billboards\Models\Billboard;

// No direct access
defined('_HZEXEC_') or die();

// Change title depending on whether or not we're editing or creating a new billboard
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

// Menu items
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . $text, 'billboards');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('billboard');
?>

<script type="text/javascript">
function submitbutton(pressbutton) {
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// Do field validation:
	// Make sure there's a billboard name and that there's a css class if there's CSS
	if ($('#billboardname').val() == "") {
		alert("<?php echo Lang::txt('COM_BILLBOARDS_MUST_HAVE_A_NAME', true); ?>");
	} else {
		submitform(pressbutton);
	}
}

// @TODO: should probably put this somewhere else
jQuery(document).ready(function($){
	var styling        = $('#styling');
	var styling_table  = $('#styling_table');
	var slider         = styling_table.hide();

	styling.on('click', function(e) {
		e.preventDefault();
		slider.slideToggle();
	});
});
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_BILLBOARDS_CONTENT'); ?></span></legend>

				<div class="input-wrap">
					<label for="billboardname"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_NAME'); ?>:</label><br />
					<input type="text" name="billboard[name]" id="billboardname" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="billboardcollection"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_COLLECTION'); ?>:</label><br />
					<select name="billboard[collection_id]" id="billboardcollection">
						<?php $collections = Collection::all()->rows(); ?>
						<?php if ($collections->count() > 0) : ?>
							<?php foreach ($collections as $collection) : ?>
								<option value="<?php echo $collection->id; ?>"<?php echo ($collection->id == $this->row->collection_id) ? ' selected="selected"' : ''; ?>>
									<?php echo $collection->name; ?>
								</option>
							<?php endforeach; ?>
						<?php else : ?>
							<option value="0"><?php echo Lang::txt('Default Collection'); ?></option>
						<?php endif; ?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="ordering"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_ORDERING'); ?>:</label><br />
					<?php if ($this->row->id) : ?>
						<?php $query = Billboard::select('ordering', 'value')->select('name', 'text')->whereEquals('collection_id', $this->row->collection_id)->toString(); ?>
						<?php echo Html::select('ordering', 'billboard[ordering]', $query, null, $this->row->id); ?>
					<?php else : ?>
						<input type="hidden" name="billboard[ordering]" id="ordering" value="" />
						<span class="readonly"><?php echo Lang::txt('COM_BILLBOARDS_ASC'); ?></span>
					<?php endif; ?>
				</div>
				<div class="input-wrap">
					<label for="billboardheader"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_HEADER'); ?>:</label><br />
					<input type="text" name="billboard[header]" id="billboardheader" value="<?php echo $this->escape(stripslashes($this->row->header)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="billboard-image"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_BACKGROUND_IMG'); ?>:</label><br />
					<input type="file" name="billboard-image" id="billboard-image" />
				</div>
				<div class="input-wrap">
					<label for="billboard[text]"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_TEXT'); ?>:</label><br />
					<?php echo $this->editor('billboard[text]', $this->escape(stripslashes($this->row->text)), 45, 13, 'billboard-text', ['buttons' => false]); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_BILLBOARDS_LEARN_MORE'); ?></span></legend>
				<div class="input-wrap">
					<label for="billboardlearnmoretext"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TEXT'); ?>:</label><br />
					<input type="text" name="billboard[learn_more_text]" id="billboardlearnmoretext" value="<?php echo $this->escape(stripslashes($this->row->learn_more_text)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="billboardlearnmoretarget"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_TARGET'); ?>:</label><br />
					<input type="text" name="billboard[learn_more_target]" id="billboardlearnmoretarget" value="<?php echo $this->escape(stripslashes($this->row->learn_more_target)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="billboardlearnmoreclass"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_CLASS'); ?>:</label><br />
					<input type="text" name="billboard[learn_more_class]" id="billboardlearnmoreclass" value="<?php echo $this->escape(stripslashes($this->row->learn_more_class)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="billboardlearnmorelocation"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION'); ?>:</label><br />
					<select name="billboard[learn_more_location]" id="billboardlearnmorelocation">
						<option value="topleft"<?php echo ($this->row->learn_more_location == 'topleft') ? 'selected="selected"' : ''; ?>>
							<?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_LEFT'); ?>
						</option>
						<option value="topright"<?php echo ($this->row->learn_more_location == 'topright') ? 'selected="selected"' : ''; ?>>
							<?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_TOP_RIGHT'); ?>
						</option>
						<option value="bottomleft"<?php echo ($this->row->learn_more_location == 'bottomleft') ? 'selected="selected"' : ''; ?>>
							<?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_LEFT'); ?>
						</option>
						<option value="bottomright"<?php echo ($this->row->learn_more_location == 'bottomright') ? 'selected="selected"' : ''; ?>>
							<?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_BOTTOM_RIGHT'); ?>
						</option>
						<option value="relative"<?php echo ($this->row->learn_more_location == 'relative') ? 'selected="selected"' : ''; ?>>
							<?php echo Lang::txt('COM_BILLBOARDS_FIELD_LEARN_MORE_LOCATION_RELATIVE'); ?>
						</option>
					</select>
				</div>
			</fieldset>
			<?php if ($this->row->get('background_img', false)) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_BILLBOARDS_CURRENT_IMG'); ?></span></legend>
					<?php $image = new \Hubzero\Image\Processor(PATH_ROOT . DS . ltrim($this->row->background_img, DS)); ?>
					<?php if (count($image->getErrors()) == 0) : ?>
						<?php $image->resize(500); ?>
						<div style="padding: 10px;"><img src="<?php echo $image->inline(); ?>" alt="billboard image" /></div>
					<?php endif; ?>
				</fieldset>
			<?php endif; ?>
			<fieldset class="adminform">
				<!-- @TODO: remove inline styles -->
				<legend id="styling" style="cursor:pointer;"><?php echo Lang::txt('COM_BILLBOARDS_STYLING'); ?></legend>
				<br style="clear:both;" />

				<div id="styling_table">
					<div class="input-wrap">
						<label for="billboardalias"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_ALIAS'); ?>:</label><br />
						<input type="text" name="billboard[alias]" id="billboardalias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
					</div>
					<div class="input-wrap">
						<label for="billboardpadding"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_PADDING'); ?>:</label><br />
						<input type="text" name="billboard[padding]" id="billboardpadding" value="<?php echo $this->escape(stripslashes($this->row->padding)); ?>" />
					</div>
					<div class="input-wrap">
						<label for="billboardcss"><?php echo Lang::txt('COM_BILLBOARDS_FIELD_CSS'); ?>:</label><br />
						<textarea name="billboard[css]" id="billboardcss" cols="45" rows="13"><?php echo $this->escape(stripslashes($this->row->css)); ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="billboard[id]" value="<?php echo $this->row->id; ?>" />

	<?php echo Html::input('token'); ?>
</form>