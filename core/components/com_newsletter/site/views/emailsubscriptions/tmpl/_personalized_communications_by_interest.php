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
 * @var \Hubzero\Component\View&object{userId: mixed} $this
 */
$userId = $this->userId;
$profileLink = "/members/$userId/profile";
$hubname = Config::get('sitename');
?>

<ul>
  <li>Personalized updates based on your usage and impact on <?php echo $hubname; ?></li>
  <li>Updates about resources you previously used</li>
  <li>Specific information based on your field and interests (please review your
<a href="<?php echo $profileLink; ?>">profile</a>)</li>
</ul>
