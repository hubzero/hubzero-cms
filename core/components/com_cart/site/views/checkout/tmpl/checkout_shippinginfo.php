<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

?>

<div class="section">

	<h2>Shipping info</h2>

	<?php
	if (!empty($this->transactionInfo))
	{
		echo '<p>';
		echo $this->transactionInfo->tiShippingToFirst;
		echo ' ';
		echo $this->transactionInfo->tiShippingToLast;
		echo '<br>';
		echo $this->transactionInfo->tiShippingAddress;
		echo '<br>';
		echo $this->transactionInfo->tiShippingCity;
		echo ', ';
		echo $this->transactionInfo->tiShippingState;
		echo ' ';
		echo $this->transactionInfo->tiShippingZip;
		echo '</p>';
	}

	echo '<a href="';
	echo Route::url('index.php?option=com_cart') . 'checkout/shipping?update=true';
	echo '">Change</a>';
	?>

</div>