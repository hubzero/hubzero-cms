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

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'), 'addedit.png');

$this->css('batchcreate');
$this->js('batchcreate');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=process'); ?>" method="post" name="adminForm" id="item-form" class="batchupload" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_IMPORT'); ?></span></legend>

		<div class="grid">
			<div class="col span7">
				<div class="input-wrap">
					<label for="projectid">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ADD_IN_PROJECT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					</label>
					<?php 
					// Draw project list
					$this->view('_selectprojects')
					     ->set('projects', $this->projects)
					     ->display(); ?>
				</div>
				<div class="input-wrap file-import" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ATTACH_HINT'); ?>">
					<label for="field-file">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DATA'); ?><span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					</label>
					<input type="file" name="file" id="field-file" />
				</div>
				<div class="input-wrap">
					<input type="submit" name="batch_submit" id="batch_submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_UPLOAD_AND_PREPROCESS'); ?>" />
				</div>
			</div>
			<div class="col span5">
				<p><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_XSD_INSTRUCT'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=xsd'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_XSD'); ?></a></p>
			</div>
		</div>

		<div class="output-wrap" id="results">
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="process" />
		<input type="hidden" name="base" value="files" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>