<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
	<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=share&sharewith=' . strtolower($this->name)); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_SHARE_ON', Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->name))); ?>" class="popup" rel="external"><span class="share_<?php echo strtolower($this->name);  ?>"><span><?php echo Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->name)); ?></span></span></a>