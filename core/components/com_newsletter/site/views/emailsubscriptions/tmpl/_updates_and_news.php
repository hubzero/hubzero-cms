<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View $this
 */
$hubname = Config::get('sitename');
?>

<ul>
  <li>news and announcements (e.g new courses, apps, and other opportunities)</li>
  <li><?php echo $hubname; ?> newsletter</li>
</ul>
