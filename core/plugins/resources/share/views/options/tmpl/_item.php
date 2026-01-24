<?php

// phpcs:disable Generic.Files.LineLength

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
    <a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=share&sharewith=' . strtolower($this->_name)); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_SHARE_ON', Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->_name))); ?>" class="popup" rel="external"><span class="share_<?php echo strtolower($this->_name);  ?>"><span><?php echo Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->_name)); ?></span></span></a>