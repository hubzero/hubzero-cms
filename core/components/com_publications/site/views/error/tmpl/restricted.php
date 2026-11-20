<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{error: mixed, title: mixed} $this
 */

$this
     ->css()
     ->js();
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>
</header>

<section class="main section">
    <p class="error"><?php echo $this->error; ?></p>
</section><!-- / .section -->

