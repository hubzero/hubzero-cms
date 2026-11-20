<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{no_html: mixed, output: mixed} $this
 */

if (!$this->no_html) {
    ?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_CRON'); ?></h2>
</header>

<section class="main section">

    <p>tock...</p>
    <pre><?php echo json_encode($this->output); ?></pre>

</section><!-- / .main section -->
    <?php
} else {
    session_write_close();
    ob_clean();
    header('Content-type: application/json');
    echo json_encode($this->output);
    exit();
}
