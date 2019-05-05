<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Get a shortened name
$name = $this->currentFolder['name'];
if (strlen($name) > 10):
	$name = substr($name, 0, 10) . ' ... ';
endif;

// Querystring option
$t = '';
if ($tmpl = Request::getCmd('tmpl')):
	$t .= '&tmpl=' . $tmpl;
endif;

// Folder link
$href = Route::url('index.php?option=' . $this->option . '&task=download&' . Session::getFormToken() . '=1&folder=' . urlencode('/' . $this->currentFolder['path']));
?>
		<div class="media-item media-item-thumb">
			<div class="media-preview">
				<div class="media-preview-inner">
					<a class="media-thumb folder-item" data-folder="<?php echo $this->escape('/' . $this->currentFolder['path']); ?>" href="<?php echo Route::url('index.php?option=com_media&controller=medialist&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&folder=/' . $this->currentFolder['path']); ?>">
						<span class="media-preview-shim"></span><!--
						--><img src="<?php echo $this->img('folder.svg'); ?>" alt="<?php echo $this->escape($this->currentFolder['name']); ?>" width="80" />
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
									<a class="icon-info media-opt-info" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&task=info&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&folder=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('Info'); ?></a>
								</li>
							<?php endif; ?>
							<?php if (User::authorise('core.delete', 'com_media')): ?>
								<li>
									<span class="separator"></span>
								</li>
								<li>
									<a class="icon-trash media-opt-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&rm=' . urlencode($this->currentFolder['path'])); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
