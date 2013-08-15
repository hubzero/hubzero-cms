<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// http://www.php.net/manual/en/function.json-encode.php#80339
function json_format($json) 
{
	$tab = "\t";
	$new_json = "";
	$indent_level = 0;
	$in_string = false;

	$json_obj = json_decode($json);

	if($json_obj === false)
		return false;

	$json = json_encode($json_obj);
	$len = strlen($json);

	for($c = 0; $c < $len; $c++) {
		$char = $json[$c];
		switch($char) {
			case '{':
			case '[':
				if(!$in_string) {
					$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
					$indent_level++;
				} else {
					$new_json .= $char;
				}
				break;
			case '}':
			case ']':
				if(!$in_string) {
					$indent_level--;
					$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
				} else {
					$new_json .= $char;
				}
				break;
			case ',':
				if(!$in_string) {
					$new_json .= ",\n" . str_repeat($tab, $indent_level);
				} else {
					$new_json .= $char;
				}
				break;
			case ':':
				if(!$in_string) {
					$new_json .= ": ";
				} else {
					$new_json .= $char;
				}
				break;
			case '"':
				if($c > 0 && $json[$c-1] != '\\') {
					$in_string = !$in_string;
				}
			default:
				$new_json .= $char;
				break;				   
		}
	}

	return $new_json;
}
?>
