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

// Querystring option
$t = '';
$tmpl = Request::getCmd('tmpl');
if ($tmpl):
	$t .= '&tmpl=' . $tmpl;
endif;

// Get a shortened name
$name = Filesystem::name($this->currentDoc['name']);
if ($tmpl == 'component' && strlen($name) > 10):
	$name = substr($name, 0, 10) . ' ... ';
endif;
$name .= '.' . $ext;

// Download link
$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path']));

// Last modified time
$this->currentDoc['modified'] = filemtime(COM_MEDIA_BASE . $this->currentDoc['path']);
$modified = Date::of($this->currentDoc['modified']);

// Before display event
$params = new Hubzero\Config\Registry;
Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
	<tr class="media-item media-item-list">
		<td width="50%">
			<a class="doc-item" href="<?php echo COM_MEDIA_BASEURL . $this->currentDoc['path']; ?>" title="<?php echo $this->escape($this->currentDoc['name']); ?>">
				<span class="media-icon">
					<img src="<?php echo $this->img($icon); ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->currentDoc['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentDoc['size']))); ?>" />
				</span>
				<span class="media-name">
					<?php echo $this->escape($name); ?>
				</span>
			</a>
		</td>
		<td>
			<span class="media-size"><?php echo Components\Media\Admin\Helpers\MediaHelper::parseSize($this->currentDoc['size']); ?></span>
		</td>
	<?php if ($tmpl != 'component'): ?>
		<td>
			<span class="media-type"><?php echo strtoupper($ext); ?></span>
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
								<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info' . $t . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_INFO'); ?></a>
							</li>
							<li>
								<span class="separator"></span>
							</li>
							<li>
								<a download class="icon-download media-opt-download" href="<?php echo $href; ?>"><?php echo Lang::txt('COM_MEDIA_DOWNLOAD'); ?></a>
							</li>
							<li>
								<a class="icon-link media-opt-path" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=path' . $t . '&' . Session::getFormToken() . '=1&file=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('COM_MEDIA_FILE_LINK'); ?></a>
							</li>
						<?php endif; ?>
						<?php if (User::authorise('core.delete', 'com_media')): ?>
							<li>
								<span class="separator"></span>
							</li>
							<li>
								<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete' . $t . '&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentDoc['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</td>
	<?php endif; ?>
	</tr>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
