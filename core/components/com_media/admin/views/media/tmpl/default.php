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
						<a href="<?php echo Route::url('index.php?option=com_media&controller=medialist&tmpl=component&folder=' . '/' . $folder['path']); ?>">
							<?php echo $this->escape($folder['name']); ?>
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
						<!-- <span class="icon-folder-close right-arrow-bg">root</span> -->
						<img src="<?php echo $this->img('folder.svg'); ?>" alt="<?php echo COM_MEDIA_BASEURL; ?>" />
					</a>
					<span class="icon-chevron-right dir-separator"><span>/</span></span>
					<a class="media-breadcrumbs folder has-next-button" id="path_F9pU2QqQ">(2005-) IDW</a>
					<span class="icon-chevron-right dir-separator"><span>/</span></span>
					<a class="media-breadcrumbs folder" id="path_toZCjLzJ">Alternative Continuities</a>
				</div>
				<div class="media-header-buttons">
					<a class="icon-th media-files-view thumbs-view <?php if (!$this->layout || $this->layout == 'thumbs') { echo 'active'; } ?>" data-view="thumbs" href="<?php echo Route::url('index.php?option=' . $this->option . '&layout=thumbs'); ?>" title="<?php echo Lang::txt('Thumbnail view'); ?>">
						<?php echo Lang::txt('Thumbnail view'); ?>
					</a>
					<a class="icon-align-justify media-files-view hasTip listing-view <?php if ($this->layout == 'list') { echo 'active'; } ?>" data-view="list" href="<?php echo Route::url('index.php?option=' . $this->option . '&layout=list'); ?>" title="<?php echo Lang::txt('List view'); ?>">
						<?php echo Lang::txt('List view'); ?>
					</a>

					<div class="button link-button right fm-file-upload hidden">
						<i class="small-icon file-upload"></i>
						<span>File Upload</span>
						<input type="file" title="File Upload" id="fileselect1" multiple="">
					</div>

					<div class="button link-button right fm-new-folder hidden" title="Create new folder">
						<i class="small-icon dark-grey-plus"></i>
						<span>New Folder</span>
					</div>
				</div>
			</div>
			<div class="media-view">
				<div class="media-items" id="media-items">
					<?php
					$children = Components\Media\Admin\Helpers\MediaHelper::getChildren(COM_MEDIA_BASE, '');

					$this->view('default', 'medialist')
						->set('folder', $this->folder)
						->set('children', $children)
						->set('layout', $this->layout)
						->display();

					/*$this->view('list', 'medialist')
						->set('folder', $this->folder)
						->set('children', $children)
						->set('active', ($this->layout == 'list' ? true : false))
						->display();

					$this->view('thumbs', 'medialist')
						->set('folder', $this->folder)
						->set('children', $children)
						->set('active', ($this->layout == 'thumbs' ? true : false))
						->display();*/
					?>
				</div>
			</div>

			<form action="<?php echo Route::url('index.php?option=com_media&' . Session::getFormToken() . '=1', true, true); ?>" name="adminForm" id="media-form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="token" value="<?php echo Session::getFormToken(); ?>" />
				<input type="hidden" name="folder" id="folder" value="<?php echo $this->escape($this->folder); ?>" />
				<input type="hidden" name="layout" id="layout" value="<?php echo $this->escape($this->layout); ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div><!-- / .panel-files -->
	</div><!-- / .media-panels -->
</div>
