<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();
//ddie($this->folderTree);
?>
<ul <?php echo $this->folders_id; ?>>
	<?php foreach ($this->folderTree as $folder) { ?>
		<li id="<?php echo $folder['name']; ?>">
			<a href="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $folder['name']); ?>" target="folderframe"><?php echo $folder['name']; ?></a><?php //echo $this->getFolderLevel($folder); ?>
		</li>
	<?php } ?>
</ul>
