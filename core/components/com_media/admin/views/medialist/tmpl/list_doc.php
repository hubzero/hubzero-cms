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

$icon = 'txt.svg';

$path = Component::path('com_media') . '/admin/assets/img/';
$ext = Filesystem::extension($this->currentDoc['name']);
if (file_exists($path . $ext . '.svg'))
{
	$icon = $ext . '.svg';
}

$params = new Hubzero\Config\Registry;

$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path']));

$this->currentDoc['modified'] = filemtime(COM_MEDIA_BASE . $this->currentDoc['path']);
$modified = Date::of($this->currentDoc['modified']);

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
	<tr class="media-item media-item-list doc-item">
		<td width="50%">
			<a href="<?php echo $href; ?>" title="<?php echo $this->escape($this->currentDoc['name']); ?>">
				<span class="media-icon">
					<img src="<?php echo $this->img($icon); ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->currentDoc['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentDoc['size']))); ?>" />
				</span>
				<span class="media-name">
					<?php echo $this->escape($this->currentDoc['name']); ?>
				</span>
			</a>
		</td>
		<td>
			<span class="media-size"><?php echo Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentDoc['size']); ?></span>
		</td>
		<td>
			<span class="media-type"><?php echo strtoupper($ext); ?></span>
		</td>
		<td>
			<time class="media-modified" datetime="<?php echo $modified->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo $modified->toSql(); ?></time>
		</td>
		<td>
			<div class="media-preview-inner">
				<span class="media-options-btn"></span>
				<div class="media-options">
					<ul>
						<li>
							<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('Info'); ?></a>
						</li>
						<li>
							<span class="separator"></span>
						</li>
						<li>
							<a class="icon-download media-opt-download" href="<?php echo $href; ?>"><?php echo Lang::txt('Download'); ?></a>
						</li>
						<li>
							<a class="icon-link media-opt-path" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=path&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('Get link'); ?></a>
						</li>
						<?php if (User::authorise('core.delete', 'com_media')): ?>
							<li>
								<span class="separator"></span>
							</li>
							<li>
								<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&' . Session::getFormToken() . '=1&rm[]=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</td>
	</tr>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
