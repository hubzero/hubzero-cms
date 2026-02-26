<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Filesystem;

// No direct access
defined('_HZEXEC_') or die();

$directory = $this->directory;
?>
<li>
    <span class="item-icon">
        <?php $val = Filesystem::extension($directory['name']) == 'zip' ? 'zip' : 'dir'; ?>
        <span class="item-extension _<?php echo $val; ?>"></span>
    </span>
    <span class="item-title"><?php echo $this->escape($directory['name']); ?></span>
    <?php if (isset($directory['contents']) && $directory['contents']) : ?>
        <ul>
            <?php
            $this->view('_bundle_contents')
                ->set('contents', $directory['contents'])
                ->display();
            ?>
        </ul>
    <?php endif; ?>
</li>
