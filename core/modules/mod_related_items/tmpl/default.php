<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;

// no direct access
defined('_HZEXEC_') or die;
?>
<ul class="relateditems<?php echo $moduleclass_sfx; ?>">
    <?php foreach ($list as $item) : ?>
        <li>
            <a href="<?php echo $item->route; ?>">
                <?php
                if ($showDate) :
                    $formattedDate = Date::of($item->created)
                        ->toLocal(Lang::txt('DATE_FORMAT_LC4'));
                    echo '<time datetime="' . $item->created . '">'
                        . $formattedDate . '</time> - ';
                endif;
                ?>
                <?php echo $item->title; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
