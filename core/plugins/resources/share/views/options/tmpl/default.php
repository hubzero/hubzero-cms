<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{_params: mixed, option: mixed, resource: mixed} $this
 */

$this
     ->css()
     ->js();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? intval($this->_params->get('icons_limit')) : 8;

$popup = '<ol class="sharelinks">';

$metadata  = '<div class="share">' . "\n";
$metadata .= "\t" . Lang::txt('PLG_RESOURCES_SHARE') . ': ';

// Available options
// Google+ (2019) and Delicious (2017) are gone; their buttons led to unrelated pages
$sharing = ['facebook', 'twitter', 'pinterest', 'linkedin', 'reddit'];

foreach ($sharing as $shared) {
    if ($this->_params->get('share_' . $shared, 1)) {
        // Show activity
        $link = $this
             ->view('_item')
             ->set('option', $this->option)
             ->set('resource', $this->resource)
             ->set('name', $shared)
             ->loadTemplate();

        $metadata .= ($i <= $limit) ? $link : '';

        $popup    .= '<li class="';
        $popup    .= ($i % 2) ? 'odd' : 'even';
        $popup    .= '">' . $link . '</li>';

        $i++;
    }
}

// Pop up more
if (($i + 2) > $limit) {
    $metadata .= '...';
}

$popup .= '</ol>';

$metadata .= '<dl class="shareinfo">' . "\n";
$metadata .= "\t" . '<dt>' . Lang::txt('PLG_RESOURCES_SHARE') . '</dt>' . "\n";
$metadata .= "\t" . '<dd>' . "\n";
$metadata .= "\t\t" . '<p>' . "\n";
$metadata .= "\t\t\t" . Lang::txt('PLG_RESOURCES_SHARE_RESOURCE') . "\n";
$metadata .= "\t\t" . '</p>' . "\n";
$metadata .= "\t\t" . '<div>' . "\n";
$metadata .= $popup;
$metadata .= "\t\t" . '</div>' . "\n";
$metadata .= "\t" . '</dd>' . "\n";
$metadata .= '</dl>' . "\n";
$metadata .= '</div>' . "\n";

echo $metadata;
