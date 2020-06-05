<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php echo '<form method="post" action="' . rtrim(Request::root(), '/') . '/cart/test/pay">' ?>

	<?php

	$buttonVars['custom'] = $this->transaction->token . '-' . $this->transaction->info->tId;

	foreach ($buttonVars as $k => $v)
	{
		echo '<input type="hidden" value="' . $v . '" name="' . $k . '">';
	}

	?>

	<input type="hidden" name="provider" value="offline">
	<input type="submit" value="Pay with offline" />
</form>