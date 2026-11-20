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

<div class="group-page group-page-notice notice-warning">
    <h4><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL'); ?></h4>
    <p><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL_DESC_NO_OTHER_VERSION'); ?></p>
</div>