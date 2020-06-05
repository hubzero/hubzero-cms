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
	<h2>Payment</h2>
</header>

<section class="main section">
	<div class="section-inner">
		<p>Click the 'Pay' button to finalize the order.</p>

		<form action="" id="frm" method="post">

		<?php
			foreach ($_POST as $k => $v)
			{
				echo '<input type="hidden" name="' . $k . '" value="' . $v . '"></input>';
			}
		?>

		<input type="hidden" name="dummypay" value="1"></input>

		<input type="submit" value="Pay">
		</form>
	</div>
</section>