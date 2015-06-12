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
		<tr>
			<td class="imgTotal">
				<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->state->parent); ?>" target="folderframe">
					<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/folderup_16.png" alt=".." height="16" width"16" />
				</a>
			</td>
			<td class="description">
				<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->state->parent); ?>" target="folderframe">..</a>
			</td>
			<td>&#160;</td>
			<td>&#160;</td>
		<?php if (User::authorise('core.delete', 'com_media')):?>
			<td>&#160;</td>
		<?php endif;?>
		</tr>
