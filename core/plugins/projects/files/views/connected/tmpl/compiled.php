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

$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
?>

<div id="abox-content">
	<?php if ($this->getError()) : ?>
		<h3>
			<?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILED_PREVIEW'); ?>
		</h3>
		<p class="witherror"><?php echo $this->getError(); ?></p>
		<div class="witherror">
			<pre>
				<?php if (!empty($this->log)) : ?>
					<?php echo $this->log; ?>
				<?php endif ; ?>
			</pre>
		</div>
	<?php else : ?>
		<ul class="sample">
			<li>
				<?php echo \Components\Projects\Models\File::drawIcon($this->file->getExtension()); ?>
				<?php echo $this->file->getName(); ?>
				<?php echo $this->file->isFile()
					? '<input type="hidden" name="folder" value="' . urlencode($this->file->getName()) . '" />'
					: '<input type="hidden" name="asset" value="' . urlencode($this->file->getName()) . '" />'; ?>

				<?php if ($this->file->hasExtension('tex') && is_file(PATH_APP . $this->outputDir . DS . $this->embed)) : ?>
					<span class="rightfloat">
						<a href="<?php echo Route::url($this->url . '&action=compile&connection=' . $this->connection->id . $subdirlink . '&download=1&asset=' . $this->file->getName()); ?>" class="i-download">
							<?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD'); ?> PDF
						</a>
					</span>
				<?php endif; ?>
			</li>
		</ul>
	<?php endif; ?>
	<?php if (!empty($this->data) && $this->cType != 'application/pdf') : ?>
		<?php $this->data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $this->data); ?>
		<pre>
			<?php echo htmlentities($this->data); ?>
		</pre>
	<?php elseif ($this->embed && file_exists(PATH_APP . $this->outputDir . DS . $this->embed)) : ?>
		<?php $source = Route::url('index.php?option=' . $this->option . '&controller=media&alias=' . $this->model->get('alias') . '&media=Compiled:' . $this->embed ); ?>
		<div id="compiled-doc" embed-src="<?php echo $source; ?>" embed-width="<?php echo $this->oWidth; ?>" embed-height="<?php echo $this->oHeight; ?>">
			<object width="<?php echo $this->oWidth; ?>" height="<?php echo $this->oHeight; ?>" type="<?php echo $this->cType; ?>" data="<?php echo $source; ?>" id="pdf_content">
				<embed src="<?php echo $source; ?>" type="application/pdf" />
				<p>
					<?php echo Lang::txt('PLG_PROJECTS_FILES_PREVIEW_NOT_LOAD'); ?>
					<a href="<?php echo Route::url($this->url . '&action=compile&connection=' . $this->connection->id . $subdirlink . '&download=1&file=' . $this->file->getName()); ?>">
						<?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD_FILE'); ?>
					</a>
					<?php if ($this->image) : ?>
						<img alt="" src="<?php echo Route::url('index.php?option=' . $this->option . '&task=media&alias=' . $this->model->get('alias') . '&media=Compiled:' . $this->image); ?>" />
					<?php endif; ?>
				</p>
			</object>
		</div>
	<?php endif; ?>
</div>