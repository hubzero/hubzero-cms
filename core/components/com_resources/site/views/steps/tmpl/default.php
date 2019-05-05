<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="warning"><?php echo $this->getError(); ?></p>
	<?php } ?>
</section><!-- / .main section -->
