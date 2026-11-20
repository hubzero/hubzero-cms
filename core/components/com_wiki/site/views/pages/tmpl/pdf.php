<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{page: \Components\Wiki\Models\Page, revision: mixed} $this
 */
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->page->get('title')); ?></h2>
</header><!-- /#content-header -->
<article class="wikipage">
    <?php echo $this->revision->get('pagehtml'); ?>
</article>