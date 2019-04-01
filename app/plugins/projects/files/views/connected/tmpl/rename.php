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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Directory path breadcrumbs
$bc    = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent, false, $this->connection->adapter());
$bcEnd = $this->item->isDir() ? '<span class="folder">' . $this->item->getName() . '</span>' : '<span class="file">' . $this->item->getName() . '</span>';
$lang  = $this->item->isDir() ? 'folder' : 'file';
?>

<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME') . ' ' . $lang . ' ' . $bc . ' ' . $bcEnd; ?>
	</h3>
	<?php if ($this->getError()) : ?>
		<p class="witherror"><?php $this->getError(); ?></p>
	<?php else : ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
			<fieldset>
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="action" value="renameit" />
				<input type="hidden" name="type" value="<?php echo $lang; ?>" />
				<input type="hidden" name="oldname" value="<?php echo $this->item->getPath(); ?>" />
				<h5><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_NAME'); ?></h5>
				<label>
					<input type="text" name="newname" maxlength="250" value="<?php echo $this->item->getFilename(); ?>" />
				</label>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
				<input type="reset" class="btn btn-cancel" id="cancel-action" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?>" />
			</fieldset>
		</form>
	<?php endif; ?>
</div>
