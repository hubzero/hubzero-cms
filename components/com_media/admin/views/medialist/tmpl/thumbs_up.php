<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->state->parent); ?>" target="folderframe">
						<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/folderup_32.png" alt=".." height="32" width"32" />
					</a>
				</div>
			</div>
			<div class="controls">
				<span>&#160;</span>
			</div>
			<div class="imginfoBorder">
				<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->state->parent); ?>" target="folderframe">..</a>
			</div>
		</div>
