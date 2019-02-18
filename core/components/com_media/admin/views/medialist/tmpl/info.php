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

if ($this->data['type'] != 'folder'):
	$icon = 'file.svg';

	$path = Component::path('com_media') . '/admin/assets/img/';
	$ext = Filesystem::extension($this->data['name']);
	if (file_exists($path . $ext . '.svg')):
		$icon = $ext . '.svg';
	endif;
else:
	$icon = 'folder.svg';
endif;
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&file=' . urlencode($this->data['path'])); ?>" id="component-form" method="post" name="adminForm" autocomplete="off">
	<fieldset>
		<h2 class="modal-title">
			<?php echo Lang::txt('File Info'); ?>
		</h2>
	</fieldset>
	<div class="grid">
		<div class="col span5">
			<div class="media-preview">
				<div class="media-preview-inner">
					<?php if ($this->data['type'] == 'img'): ?>
						<div class="media-thumb img-preview <?php echo Filesystem::extension($this->data['name']); ?>" title="<?php echo $this->escape($this->data['name']); ?>" >
							<span class="media-preview-shim"></span><!--
							--><img src="<?php echo COM_MEDIA_BASEURL . $this->data['path']; ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->data['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->data['size']))); ?>" width="<?php echo ($this->data['width'] < 260) ? $this->data['width'] : '260'; ?>" />
						</div>
					<?php else: ?>
						<div class="media-thumb doc-item <?php echo Filesystem::extension($this->data['name']); ?>" title="<?php echo $this->escape($this->data['name']); ?>" >
							<span class="media-preview-shim"></span><!--
							--><img src="<?php echo $this->img($icon); ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->data['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->data['size']))); ?>" width="80" />
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col span7">
			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('Name:'); ?></span>
				<span class="media-info-value"><?php echo $this->escape($this->data['name']); ?></span>
			</div>

			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('Path:'); ?></span>
				<span class="media-info-value"><?php echo $this->escape($this->data['path']); ?></span>
			</div>

			<?php if ($this->data['type'] != 'folder'): ?>
				<?php if ($this->data['type'] == 'img'): ?>
					<div class="grid">
						<div class="col span4">
				<?php endif; ?>
				<div class="input-wrap">
					<span class="media-info-label"><?php echo Lang::txt('Size:'); ?></span>
					<span class="media-info-value"><?php echo Hubzero\Utility\Number::formatBytes($this->data['size']); ?></span>
				</div>
				<?php if ($this->data['type'] == 'img'): ?>
						</div>
						<div class="col span4">
							<div class="input-wrap">
								<span class="media-info-label"><?php echo Lang::txt('Width:'); ?></span>
								<span class="media-info-value"><?php echo $this->data['width']; ?>px</span>
							</div>
						</div>
						<div class="col span4">
							<div class="input-wrap">
								<span class="media-info-label"><?php echo Lang::txt('Height:'); ?></span>
								<span class="media-info-value"><?php echo $this->data['height']; ?>px</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('Last modified:'); ?></span>
				<span class="media-info-value"><?php echo Date::of($this->data['modified'])->toSql(); ?></span>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<?php echo Html::input('token'); ?>
</form>
