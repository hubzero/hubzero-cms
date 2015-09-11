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

$tmpl = Request::getVar('tmpl', '');
$no_html = Request::getInt('no_html', 0);

if (!$tmpl && !$no_html) {
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_SUPPORT_EDIT_FOLDER'); ?></h2>
</header><!-- / #content-header -->

<section class="main section">
<?php } ?>
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=savefolder'); ?>" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE'); ?></legend>

				<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />

				<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->id); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : Request::getInt('no_html', 0); ?>" />
				<input type="hidden" name="tmpl" value="<?php echo $this->escape($tmpl); ?>" />
				<input type="hidden" name="task" value="savefolder" />

				<?php echo Html::input('token'); ?>
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />
			</p>
		</form>
	</div>
<?php if (!$no_html) { ?>
</section><!-- / .main section -->
<?php
}