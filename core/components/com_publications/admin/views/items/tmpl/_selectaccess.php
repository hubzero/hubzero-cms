<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$as = explode(',', $this->as);
$html  = '<select name="access">'."\n";
for ($i=0, $n=count( $as ); $i < $n; $i++)
{
	$html .= "\t" . '<option value="' . $i . '"';
	if ($this->value == $i)
	{
		$html .= ' selected="selected"';
	}
	$html .= '>' . trim($as[$i]) . '</option>' . "\n";
}
$html .= '</select>' . "\n";
echo $html;
