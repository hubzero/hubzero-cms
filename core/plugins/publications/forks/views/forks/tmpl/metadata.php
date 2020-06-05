<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p class="forks">
	<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $this->publication->id . '&active=forks'); ?>">
		<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_N', $this->total); ?>
	</a>
</p>