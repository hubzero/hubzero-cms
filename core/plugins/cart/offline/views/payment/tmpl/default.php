<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<form action="<?php echo $this->get('url'); ?>" method="post">
	<input type="hidden" name="paymentProvider" value="offline">
	<input type="submit" class="btn" name="paymentSelect" value="Use offline payment" />
</form>