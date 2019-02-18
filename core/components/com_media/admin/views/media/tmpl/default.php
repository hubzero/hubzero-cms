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

Toolbar::title(Lang::txt('COM_MEDIA'));
if (User::authorise('core.admin', 'com_media'))
{
	Toolbar::preferences($this->option);
	Toolbar::spacer();
}
if (User::authorise('core.delete', 'com_media'))
{
	Toolbar::deleteList('', 'delete');
	Toolbar::spacer();
}
Toolbar::help('media');

$base = COM_MEDIA_BASE;
if (DIRECTORY_SEPARATOR == '\\')
{
	$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_MEDIA_BASE);
}
$style = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');

Html::behavior('framework', true);

$this->js("
	var basepath = '" . $base . "';
	var viewstyle = '" . $style . "';
");
//$this->css('jquery.treeview.css', 'system');
$this->css();
$this->js('jquery.treeview.js', 'system');
$this->js();
?>
<div class="media-container">
	<div class="media-panels">
		<div class="panel panel-tree">
			<div id="media-tree_tree">
				<ul>
					<li>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&tmpl=component&folder=' . '/' . $this->folders[0]['path']); ?>">
							<?php echo $this->escape($this->folders[0]['name']); ?>
						</a>
						<?php echo $this->loadTemplate('folders'); ?>
					</li>
				</ul>
			</div>
		</div><!-- / .panel-tree -->
		<div class="panel panel-files">
			<div class="media-header">
				<div class="media-breadcrumbs-block">
					<a class="media-breadcrumbs has-next-button folder-link" id="path_root">
						<img src="<?php echo $this->img('folder.svg'); ?>" alt="<?php echo COM_MEDIA_BASEURL; ?>" />
					</a>
					<span id="media-breadcrumbs">
						<?php
						$folder = trim($this->folder, '/');
						$trail = explode('/', $folder);

						foreach ($trail as $crumb):
							// Skip the root directory
							if ($crumb == $this->folders[0]['name']):
								continue;
							endif;
							?>
							<span class="icon-chevron-right dir-separator">/</span>
							<a class="media-breadcrumbs folder has-next-button" id="path_<?php echo $crumb; ?>"><?php echo $crumb; ?></a>
							<?php
						endforeach;
						?>
					</span>
				</div>
				<div class="media-header-buttons">
					<a class="icon-th media-files-view thumbs-view <?php if (!$this->layout || $this->layout == 'thumbs') { echo 'active'; } ?>" data-view="thumbs" href="<?php echo Route::url('index.php?option=' . $this->option . '&layout=thumbs&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('Thumbnail view'); ?>">
						<?php echo Lang::txt('Thumbnail view'); ?>
					</a>
					<a class="icon-align-justify media-files-view hasTip listing-view <?php if ($this->layout == 'list') { echo 'active'; } ?>" data-view="list" href="<?php echo Route::url('index.php?option=' . $this->option . '&layout=list&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('List view'); ?>">
						<?php echo Lang::txt('List view'); ?>
					</a>
					<?php if (User::authorise('core.create', $this->option)): ?>
						<a class="icon-folder-new media-files-action media-folder-new hasTip <?php if ($this->layout == 'list') { echo 'active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new&' . Session::getFormToken() . '=1'); ?>" data-prompt="<?php echo Lang::txt('Folder name'); ?>" title="<?php echo Lang::txt('New Folder'); ?>">
							<?php echo Lang::txt('New Folder'); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="media-view">
				<div class="media-items" id="media-items" data-list="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=display&' . Session::getFormToken() . '=1'); ?>">
					<?php
					$children = Components\Media\Admin\Helpers\MediaHelper::getChildren(COM_MEDIA_BASE, '');

					$this->view('default', 'medialist')
						->set('folder', $this->folder)
						->set('children', $children)
						->set('layout', $this->layout)
						->display();
					?>
				</div>
			</div>

			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&' . Session::getFormToken() . '=1', true, true); ?>" name="adminForm" id="upload-form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="token" value="<?php echo Session::getFormToken(); ?>" />
				<input type="hidden" name="folder" id="folder" value="<?php echo $this->escape($this->folder); ?>" />
				<input type="hidden" name="layout" id="layout" value="<?php echo $this->escape($this->layout); ?>" />
				<?php echo Html::input('token'); ?>

				<?php if (User::authorise('core.create', $this->option)): ?>
					<?php
					$this->js('jquery.fileuploader.js', 'system');
					?>
					<div id="ajax-uploader"
						data-action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&task=upload&' . Session::getFormToken() . '=1'); ?>"
						data-list="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=display&' . Session::getFormToken() . '=1'); ?>"
						data-instructions="Drop files here to upload">
						<noscript>
							<div class="input-wrap">
								<label for="upload"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>:</label>
								<input type="file" name="upload" id="upload" />
							</div>
						</noscript>
					</div>
					<div class="field-wrap file-list" id="ajax-uploader-list">
						<ul></ul>
					</div>
				<?php endif; ?>
			</form>
		</div><!-- / .panel-files -->
	</div><!-- / .media-panels -->
</div>
