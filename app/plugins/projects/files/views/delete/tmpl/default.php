<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="removeit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

		<p><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_FILES_CONFIRM'); ?></p>

		<ul class="sample">
		<?php foreach ($this->items as $file)
		{
			// Display list item with file data
			$this->view('default', 'selected')
			     ->set('skip', false)
			     ->set('file', $file)
			     ->set('action', 'delete')
			     ->set('multi', 'multi')
			     ->display();
		} ?>
		</ul>

		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?>" id="submit-ajaxform" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?>" />
			<?php } else {  ?>
					<a id="cancel-action" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>" class="btn btn-cancel"><?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?></a>
			<?php } ?>
		</p>
	</fieldset>
</form>
<?php } ?>
</div>