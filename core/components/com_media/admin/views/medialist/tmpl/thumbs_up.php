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
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a class="up-item" href="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->parent); ?>" target="folderframe">
						..
					</a>
				</div>
			</div>
			<div class="imginfoBorder">
				<a href="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->parent); ?>" target="folderframe">..</a>
			</div>
		</div>
