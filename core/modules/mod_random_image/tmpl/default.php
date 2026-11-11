<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;

// no direct access
defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $image
 * @var mixed $link
 * @var mixed $moduleclass_sfx
 */
?>
<div class="random-image<?php echo $moduleclass_sfx; ?>">
    <?php if ($link) : ?>
        <a href="<?php echo $link; ?>">
    <?php endif; ?>
            <?php
            echo Html::asset(
                'image',
                $image->folder . '/' . $image->name,
                $image->name,
                ['width' => $image->width, 'height' => $image->height],
            );
            ?>
    <?php if ($link) : ?>
        </a>
    <?php endif; ?>
</div>
