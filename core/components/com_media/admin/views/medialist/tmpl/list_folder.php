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

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl):
	$t .= '&tmpl=' . $tmpl;
endif;

// Get a shortened name
$name = $this->currentFolder['name'];
if ($tmpl == 'component' && strlen($name) > 10):
	$name = substr($name, 0, 10) . ' ... ';
endif;

// Last modified time
$this->currentFolder['modified'] = filemtime(COM_MEDIA_BASE . $this->currentFolder['path']);
$modified = Date::of($this->currentFolder['modified']);
?>
	<tr class="media-item media-item-list">
		<td width="<?php echo ($tmpl == 'component' && !User::authorise('core.delete', 'com_media')) ? '70' : '60'; ?>%">
			<a class="folder-item" data-folder="<?php echo $this->escape('/' . $this->currentFolder['path']); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist' . $t . '&' . Session::getFormToken() . '=1&folder=/' . $this->currentFolder['path']); ?>">
				<span class="media-icon">
					<img src="<?php echo $this->img('folder.svg'); ?>" alt="<?php echo $this->escape($this->currentFolder['name']); ?>" />
				</span>
				<span class="media-name">
					<?php echo $this->escape($name); ?>
				</span>
			</a>
		</td>
		<td>
			<!-- Nothing here -->
		</td>
	<?php if ($tmpl != 'component'): ?>
		<td>
			<span class="media-type"><?php echo Lang::txt('Folder'); ?></span>
		</td>
		<td>
			<time class="media-modified" datetime="<?php echo $modified->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo $modified->toSql(); ?></time>
		</td>
	<?php endif; ?>
	<?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')): ?>
		<td>
			<div class="media-preview-inner">
				<span class="media-options-btn"></span>
				<div class="media-options">
					<ul>
						<?php if ($tmpl != 'component'): ?>
							<li>
								<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info' . $t . '&' . Session::getFormToken() . '=1&folder=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('Info'); ?></a>
							</li>
						<?php endif; ?>
						<?php if (User::authorise('core.delete', 'com_media')): ?>
							<li>
								<span class="separator"></span>
							</li>
							<li>
								<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete' . $t . '&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</td>
	<?php endif; ?>
	</tr>