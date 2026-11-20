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
 * @var \Hubzero\Component\View&object{publication: \Components\Publications\Models\Publication, title: mixed} $this
 */

// The version's own text, else its license's (a publication may have none)
$license = $this->publication->license();
$text = $this->publication->version->get('license_text')
    ? $this->publication->version->get('license_text')
    : ($license ? $license->text : '');
$text = preg_replace("/\r\n/", "\r", trim($text));

?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>
    <div class="license-wrap">
        <?php if ($this->getError()) {
            echo '<p class="error">' . $this->getError() . '</p>';
        } else {
            echo '<pre>' . $this->escape($text) . '</pre>';
        } ?>
    </div>
</header>