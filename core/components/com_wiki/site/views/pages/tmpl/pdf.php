<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<header id="content-header">
	<h2><?php echo $this->page->get('title'); ?></h2>
</header><!-- /#content-header -->
<article class="wikipage">
	<?php echo $this->revision->get('pagehtml'); ?>
</article>