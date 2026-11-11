<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Filesystem;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{file: mixed} $this
 */

$file = $this->file;
?>
<li>
    <span class="item-icon">
        <?php $val = $this->escape(strtolower(Filesystem::extension($file['name']))); ?>
        <span class="item-extension _<?php echo $val; ?>"></span>
    </span>
    <span class="item-title"><?php echo $this->escape($file['name']); ?></span>
    <span class="item-details"><?php echo trim(Hubzero\Utility\Number::formatBytes($file['size'])); ?></span>
</li>
