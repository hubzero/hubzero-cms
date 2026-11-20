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
 * @var \Hubzero\Component\View $this
 */
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_DEVELOPER_API_STATUS'); ?></h2>
</header>

<section class="section api docs">
    <div class="section-inner">
        <?php echo Lang::txt('TODO: API Status'); ?>
    </div>
</section>