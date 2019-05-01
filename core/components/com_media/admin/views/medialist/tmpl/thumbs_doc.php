<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->currentDoc['path'] = ltrim($this->currentDoc['path'], '/');

// File type icon
$icon = 'file.svg';
$path = Component::path('com_media') . '/admin/assets/img/';
$ext  = Filesystem::extension($this->currentDoc['name']);
if (file_exists($path . $ext . '.svg')):
	$icon = $ext . '.svg';
endif;

// Get a shortened name
$name = Filesystem::name($this->currentDoc['name']);
if (strlen($name) > 10):
	$name = substr($name, 0, 10) . ' ... ';
endif;
$name .= '.' . $ext;

// Querystring option
$t = '';
if ($tmpl = Request::getCmd('tmpl')):
	$t .= '&tmpl=' . $tmpl;
endif;

// Download link
$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path']));

// Before display event
$params = new Hubzero\Config\Registry;
Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
		<div class="media-item media-item-thumb">
			<div class="media-preview">
				<div class="media-preview-inner">
					<a href="<?php echo COM_MEDIA_BASEURL . $this->currentDoc['path']; ?>" class="media-thumb doc-item <?php echo Filesystem::extension($this->currentDoc['name']); ?>" title="<?php echo $this->escape($this->currentDoc['name']); ?>" >
						<span class="media-preview-shim"></span><!--
						--><img src="<?php echo $this->img($icon); ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->currentDoc['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentDoc['size']))); ?>" width="80" />
					</a>
					<span class="media-options-btn"></span>
				</div>
			</div>
			<div class="media-info">
				<div class="media-name">
					<?php echo $this->escape($name); ?>
				</div>
				<?php if ($tmpl != 'component' || User::authorise('core.delete', 'com_media')): ?>
					<div class="media-options">
						<ul>
							<?php if ($tmpl != 'component'): ?>
								<li>
									<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_INFO'); ?></a>
								</li>
								<li>
									<span class="separator"></span>
								</li>
								<li>
									<a download class="icon-download media-opt-download" href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEDIA_DOWNLOAD'); ?></a>
								</li>
								<li>
									<a class="icon-link media-opt-path" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=path&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_LINK'); ?></a>
								</li>
							<?php endif; ?>
							<?php if (User::authorise('core.delete', 'com_media')): ?>
								<li>
									<span class="separator"></span>
								</li>
								<li>
									<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
