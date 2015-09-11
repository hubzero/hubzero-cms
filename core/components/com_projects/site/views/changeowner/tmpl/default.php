<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

// Do some text cleanup
$title = $this->escape($this->model->get('title'));

?>
<div id="project-wrap">
	<section class="main section">
		<?php
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 0)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 0)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display();
		?>
		<p class="warning"><?php echo Lang::txt('COM_PROJECTS_INFO_OWNER_DELETED'); ?></p>
		<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" id="hubForm">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="task" value="fixownership" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<h4><?php echo Lang::txt('COM_PROJECTS_OWNER_DELETED_OPTIONS'); ?></h4>
			<label><input class="option" name="keep" type="radio" value="1" checked="checked" /> <?php echo Lang::txt('COM_PROJECTS_OWNER_KEEP_PROJECT'); ?></label>
			<label><input class="option" name="keep" type="radio" value="0" /> <?php echo Lang::txt('COM_PROJECTS_OWNER_DELETE_PROJECT'); ?></label>
			<p class="submitarea">
				<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_MY_CHOICE'); ?>"  />
			</p>
			</fieldset>
		</form>
	</section><!-- / .main section -->
</div>