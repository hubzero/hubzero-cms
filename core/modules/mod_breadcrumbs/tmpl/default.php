<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

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
 * @var mixed $count
 * @var array<int, \stdClass> $list
 * @var mixed $moduleclass_sfx
 * @var mixed $params
 * @var mixed $separator
 */
?>
<nav class="breadcrumbs<?= $moduleclass_sfx ?> pathway<?= $moduleclass_sfx ?>"
    aria-label="<?= Lang::txt('MOD_BREADCRUMBS') ?>">
    <?php
    if ($params->get('showHere', 1)) {
        echo '<span class="showHere">' . Lang::txt('MOD_BREADCRUMBS_HERE') . '</span>';
    }

    // Get rid of duplicated entries on trail including home page when using multilanguage
    for ($i = 0; $i < $count; $i++) {
        if (
            $i == 1 && !empty($list[$i]->link)
            && !empty($list[$i - 1]->link)
            && $list[$i]->link == $list[$i - 1]->link
        ) {
            unset($list[$i]);
        }
    }

    // Find last and penultimate items in breadcrumbs list
    end($list);
    $last_item_key = key($list);
    prev($list);
    $penult_item_key = key($list);

    // Names come escaped from some callers and raw (titles people typed) from
    // others: decode, then escape, so both print as text
    $crumbText = function ($name) {
        return htmlspecialchars(html_entity_decode((string) $name), ENT_COMPAT, 'UTF-8');
    };

    // Generate the trail
    foreach ($list as $key => $item) :
        // Make a link if not the last item in the breadcrumbs
        $show_last = $params->get('showLast', 1);
        if ($key != $last_item_key) {
            // Render all but last item - along with separator
            if (!empty($item->link)) {
                echo '<a href="' . $item->link . '" class="pathway" aria-label="'
                    . $crumbText($item->name) . ' (breadcrumb)">'
                    . $crumbText($item->name) . '</a>';
            } else {
                echo '<span>' . $crumbText($item->name) . '</span>';
            }

            if (($key != $penult_item_key) || $show_last) {
                echo ' <span class="sep">' . $separator . '</span> ';
            }
        } elseif ($show_last) {
            // Render last item if reqd.
            echo '<span aria-current="page">' . $crumbText($item->name) . '</span>';
        }
    endforeach;
    ?>
</nav>
