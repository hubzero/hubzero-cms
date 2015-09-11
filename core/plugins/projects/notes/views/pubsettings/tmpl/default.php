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

?>

<div id="abox-content">
<h3><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARING'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
		<input type="hidden" name="active" value="notes" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="p" value="<?php echo $this->page->get('id'); ?>" />

		<h4><?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK_TO_NOTE') . ' &ldquo;' . $this->page->get('title') . '&rdquo;:'; ?></h4>
		<p class="publink"><?php echo trim(Request::base(), DS) . Route::url('index.php?option=' . $this->option . '&action=get&s=' . $this->publicStamp->stamp); ?></p>
		<p class="about"><?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK_ABOUT'); ?></p>

		<?php if ($this->project->isPublic()) { ?>
		<h4><?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK_LIST_SHOW_ON_PUBLIC'); ?></h4>
		<label>
		<input type="radio" name="action" value="<?php echo 'publist'; ?>" <?php if ($this->publicStamp->listed) { echo 'checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LIST'); ?>
		</label>
		<label class="block">
		<input type="radio" name="action" value="<?php echo 'unlist'; ?>" <?php if (!$this->publicStamp->listed) { echo 'checked="checked"'; } ?>  /> <?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_UNLIST'); ?>
		</label>
		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_MY_CHOICE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
				<span>
					<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . DS . $this->page->get('pagename'); ?>"><?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?></a>
				</span>
			<?php } ?>
		</p>
		<?php } elseif ($this->ajax) { ?>
			<p class="submitarea">
			<input type="reset" id="cancel-action" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_CLOSE_THIS'); ?>" />
			</p>
		<?php } ?>
	</fieldset>
</form>
<?php } ?>
</div>
