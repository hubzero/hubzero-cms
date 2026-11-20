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
 * @var \Hubzero\Component\View&object{as: mixed, value: mixed} $this
 */

$as = explode(',', $this->as);
$html  = '<select name="access">' . "\n";
for ($i = 0, $n = count($as); $i < $n; $i++) {
    $html .= "\t" . '<option value="' . $i . '"';
    if ($this->value == $i) {
        $html .= ' selected="selected"';
    }
    $html .= '>' . trim($as[$i]) . '</option>' . "\n";
}
$html .= '</select>' . "\n";
echo $html;
