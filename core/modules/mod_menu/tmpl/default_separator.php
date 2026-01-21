<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

// Note. It is important to remove spaces between elements.
$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';
if ($item->menu_image) {
    $imgTag = '<img src="' . $item->menu_image
        . '" alt="' . $item->title . '" />';
    $item->params->get('menu_text', 1) ?
        $linktype = $imgTag
            . '<span class="image-title">'
            . $item->title . '</span> ' :
        $linktype = $imgTag;
} else {
    $linktype = $item->title;
}
?><span class="separator"><?php echo $title; ?><?php echo $linktype; ?></span>
