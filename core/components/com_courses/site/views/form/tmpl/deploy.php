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

$this->css('form.css')
     ->css('tablesorter.themes.blue.css', 'system')
     ->js('timepicker.js')
     ->js('deploy.js')
     ->js('jquery.tablesorter.min', 'system');
?>

<section class="main section courses-form">
	<form action="<?php echo Route::url($this->base); ?>" method="post" id="deployment">
		<?php require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="createDeployment" />
			<input type="hidden" name="formId" value="<?php echo $this->pdf->getId() ?>" />
			<?php if ($tmpl = Request::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
			<?php endif; ?>
			<div class="navbar">
				<div><a href="<?php echo Request::base(true); ?>/courses/form" id="cancel"><?php echo Lang::txt('JCANCEL'); ?></a></div>
				<button id="submit" type="submit"><?php echo Lang::txt('COM_COURSES_CREATE_DEPLOYMENT'); ?></button>
			</div>
		</fieldset>
	</form>
</section>