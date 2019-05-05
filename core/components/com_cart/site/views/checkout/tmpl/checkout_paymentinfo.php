<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

?>

<div class="section">

	<h2>Payment info</h2>

	<?php

	echo '<p>' . $this->paymentInfo . '</p>';

	echo '<a href="';
	echo Route::url('index.php?option=com_cart') . 'checkout/payment?update=true';
	echo '">Change</a>';
	?>

</div>