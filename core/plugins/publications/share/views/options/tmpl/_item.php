<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
	<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=share&v=' . $this->publication->version_number . '&sharewith=' . strtolower($this->name)); ?>" title="<?php echo Lang::txt('PLG_PUBLICATION_SHARE_ON', ucfirst($this->name)); ?>" class="popup" rel="external"><span class="share_<?php echo strtolower($this->name);  ?>"><span><?php echo Lang::txt('PLG_PUBLICATIONS_SHARE_' . strtoupper($this->name)); ?></span></span></a>