<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');
?>
<header id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<p class="warning"><?php echo Lang::txt('COM_CART_NOT_LOGGEDIN'); ?></p>
	<?php
	\Hubzero\Module\Helper::displayModules('force_mod');
	?>
</section><!-- / .main section -->
