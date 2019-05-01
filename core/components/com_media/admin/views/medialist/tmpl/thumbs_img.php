<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->currentImg['path'] = ltrim($this->currentImg['path'], '/');

// Get a shortened name
$ext  = Filesystem::extension($this->currentImg['name']);
$name = Filesystem::name($this->currentImg['name']);
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
$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentImg['path']));

// Before display event
$params = new Hubzero\Config\Registry;
Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>
		<div class="media-item media-item-thumb">
			<div class="media-preview">
				<div class="media-preview-inner">
					<a href="<?php echo COM_MEDIA_BASEURL . $this->currentImg['path']; ?>" class="media-thumb doc-item img-preview <?php echo Filesystem::extension($this->currentImg['name']); ?>" title="<?php echo $this->escape($this->currentImg['name']); ?>">
						<span class="media-preview-shim"></span><!--
						--><img src="<?php echo COM_MEDIA_BASEURL . $this->currentImg['path']; ?>" alt="<?php echo Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->currentImg['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentImg['size'])); ?>" width="160" />
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
									<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info' . $t . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentImg['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_INFO'); ?></a>
								</li>
								<li>
									<span class="separator"></span>
								</li>
								<li>
									<a download class="icon-download media-opt-download" href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEDIA_DOWNLOAD'); ?></a>
								</li>
								<li>
									<a class="icon-link media-opt-path" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=path' . $t . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentImg['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_LINK'); ?></a>
								</li>
							<?php endif; ?>
							<?php if (User::authorise('core.delete', 'com_media')): ?>
								<li>
									<span class="separator"></span>
								</li>
								<li>
									<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete' . $t . '&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentImg['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
