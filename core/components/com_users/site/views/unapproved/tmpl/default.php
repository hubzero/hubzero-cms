<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

<header id="content-header">
	<h2>Pending Approval</h2>
</header>

<section class="main section">
	<p>
		Your account is currently pending approval from a site administrator.
		If you feel you are receiving this message in error, please
		<a href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">contact the site administrator</a>.
	</p>
</section>