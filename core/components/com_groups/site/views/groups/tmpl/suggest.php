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
 * @var \Hubzero\Component\View&object{notifications: mixed, title: mixed} $this
 */
?>

<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>
</header>

<?php
foreach ($this->notifications as $notification) {
    echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
}
