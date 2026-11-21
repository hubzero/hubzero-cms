<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     _params: mixed,
 *     option: mixed,
 *     publication: \Components\Publications\Models\Publication,
 * } $this
 */

$this
     ->css()
     ->js();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? $this->_params->get('icons_limit') : 0;

$popup = '<ol class="sharelinks">';
$title = Lang::txt(
    'PLG_PUBLICATION_SHARE_VIEWING',
    Config::get('sitename'),
    stripslashes((string) $this->publication->title),
);
$metadata  = '<div class="share">' . "\n";
$metadata .= "\t" . Lang::txt('PLG_PUBLICATION_SHARE') . ': ';

// Available options
// Google+ (2019) and Delicious (2017) are gone; their buttons led to unrelated pages
$sharing = ['facebook', 'twitter', 'pinterest', 'linkedin', 'reddit'];

foreach ($sharing as $shared) {
    if ($this->_params->get('share_' . $shared, 1) == 1) {
        // Show activity
        $link = $this
            ->view('_item')
            ->set('option', $this->option)
            ->set('publication', $this->publication)
            ->set('name', $shared)
            ->loadTemplate();

        $metadata .= (!$limit || $i <= $limit) ? $link : '';
        $popup    .= '<li class="';
        $popup    .= ($i % 2) ? 'odd' : 'even';
        $popup    .= '">' . $link . '</li>';
        $i++;
    }
}

// Pop up more
if ($limit > 0 && $i > $limit) {
    $metadata .= '...';
}
$popup .= '</ol>';

// Show pop-up?
if ($limit > 0) {
    $metadata .= '<div class="shareinfo">' . "\n";
    $metadata .= "\t" . '<p>' . "\n";
    $metadata .= "\t\t" . Lang::txt('PLG_PUBLICATION_SHARE_RESOURCE') . "\n";
    $metadata .= "\t" . '</p>' . "\n";
    $metadata .= "\t" . '<div>' . "\n";
    $metadata .= $popup;
    $metadata .= "\t" . '</div>' . "\n";
    $metadata .= '</div>' . "\n";
}
$metadata .= '</div>' . "\n";

echo $metadata;
