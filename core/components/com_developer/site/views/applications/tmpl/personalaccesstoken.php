<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="subject full">
	<div class="container cf">
		<h3>
		<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_PERSONAL_APPLICATION_TOKEN'); ?>
		</h3>
		<div class="tokens access-tokens">
		<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_NEW_PERSONAL_APPLICATION_TOKEN'); ?>: 
		<b><?php echo $this->accesstoken ?></b></br>
		<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_MAKE_SURE_PERSONAL_APPLICATION_TOKEN'); ?>
		</div>
	</div>
</div>