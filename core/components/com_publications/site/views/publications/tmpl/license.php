<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = $this->publication->version->get('license_text') ? $this->publication->version->get('license_text') : $this->publication->license()->text;
$text = preg_replace("/\r\n/", "\r", trim($text));

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div class="license-wrap">
		<?php if ($this->getError()) { echo '<p class="error">' . $this->getError() . '</p>';
} else { echo '<pre>' . $text . '</pre>'; } ?>
	</div>
</header>