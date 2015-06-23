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
<ul <?php echo $this->folders_id; ?>>
	<?php foreach ($this->folders['children'] as $folder) : ?>
		<li id="<?php echo $folder['data']->relative; ?>"><a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $folder['data']->relative); ?>" target="folderframe"><?php echo $folder['data']->name; ?></a><?php echo $this->getFolderLevel($folder); ?></li>
	<?php endforeach; ?>
</ul>
