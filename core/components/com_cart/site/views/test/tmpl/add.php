<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

setlocale(LC_MONETARY, 'en_US.UTF-8');

?>

<header id="content-header">
	<h2>Express add</h2>
</header>

<section class="main section">
	<div class="section-inner">
		<p>Add straight to the cart</p>

		<form action="<?php echo Route::url('index.php?option=com_cart'); ?>" id="frm" method="post">

			<input type="hidden" name="updateCart" value="true">
			<input type="hidden" name="skus" value="11">
			<input type="hidden" name="expressCheckout" value="true">

			<input type="submit" value="Pay">
		</form>
	</div>
</section>