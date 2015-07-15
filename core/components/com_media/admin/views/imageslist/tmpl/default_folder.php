<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div class="item">
	<a class="folder-item" href="<?php echo Route::url('index.php?option=com_media&view=imagesList&tmpl=component&folder=' . $this->_tmp_folder->path_relative . '&asset=' . Request::getCmd('asset') . '&author=' . Request::getCmd('author')); ?>">
		<span><?php echo $this->_tmp_folder->name; ?></span>
	</a>
</div>
