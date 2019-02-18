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

$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&folder=' . urlencode($this->currentFolder['path']));
?>
		<div class="media-item media-item-thumb">
			<div class="media-preview">
				<div class="media-preview-inner">
					<a class="media-thumb folder-item" href="<?php echo Route::url('index.php?option=com_media&controller=media&folder=' . $this->currentFolder['path']); ?>" target="folderframe">
						<span class="media-preview-shim"></span><!--
						--><img src="<?php echo $this->img('folder.svg'); ?>" alt="<?php echo $this->escape($this->currentFolder['name']); ?>" width="80" />
					</a>
					<span class="media-options-btn"></span>
				</div>
			</div>
			<div class="media-info">
				<div class="media-name">
					<?php echo substr($this->currentFolder['name'], 0, 10) . (strlen($this->currentFolder['name']) > 10 ? '...' : ''); ?>
				</div>
				<div class="media-options">
					<ul>
						<li>
							<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info&' . Session::getFormToken() . '=1&folder=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('Info'); ?></a>
						</li>
						<?php if (User::authorise('core.delete', 'com_media')): ?>
							<li>
								<span class="separator"></span>
							</li>
							<li>
								<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
