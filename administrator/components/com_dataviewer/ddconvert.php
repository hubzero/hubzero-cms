#!/usr/bin/php
<?php
/**
 * @package		Dataviewer :: Data Definition Converter
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2011 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

isset($argc) or die('Restricted access');

$opt = getopt("i:o:v::");

$show_help = false;

if ($argc > 1 && (!isset($opt['i']) || !isset($opt['o']))) {
	print "\n\nPlease enter a valid input file name and an output filename.\n\n";
	$show_help = true;
}

if ($argc == 1 || $show_help) {
	print "Usage: ./ddconvert.php [OPTIONS]...\n\n";
	print "OPTIONS:\n";
	print "\t-i \tInput (.php) file name.\n";
	print "\t-o \tOutput (.json) file name.\n";
	print "\t-v \tVerbose Output.\n";
	exit(0);
}

$ip = trim($opt['i']);
$op = trim($opt['o']);

if (pathinfo($ip, PATHINFO_EXTENSION) == 'php' && pathinfo($op, PATHINFO_EXTENSION) == 'json') {
	define('_JEXEC', 'true');
	
	require_once($ip);
	$func = 'get_' . pathinfo($ip, PATHINFO_FILENAME);
	$dd_arr = $func();
	
	file_put_contents($op, json_format(json_encode($dd_arr)));

	if (isset($opt['v'])) {
		print "$op : \n";
		print json_format(json_encode($dd_arr)) . "\n";
		print "\nWriting $op done.\n";
	}
}


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
