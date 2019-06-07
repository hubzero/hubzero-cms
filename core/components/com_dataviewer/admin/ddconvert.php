#!/usr/bin/php
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

isset($argc) or die();

$opt = getopt("i:o:v::");

$show_help = false;

if ($argc > 1 && (!isset($opt['i']) || !isset($opt['o'])))
{
	print "\n\nPlease enter a valid input file name and an output filename.\n\n";
	$show_help = true;
}

if ($argc == 1 || $show_help)
{
	print "Usage: ./ddconvert.php [OPTIONS]...\n\n";
	print "OPTIONS:\n";
	print "\t-i \tInput (.php) file name.\n";
	print "\t-o \tOutput (.json) file name.\n";
	print "\t-v \tVerbose Output.\n";
	exit(0);
}

$ip = trim($opt['i']);
$op = trim($opt['o']);

if (pathinfo($ip, PATHINFO_EXTENSION) == 'php' && pathinfo($op, PATHINFO_EXTENSION) == 'json')
{
	define('_HZEXEC_', 'true');

	require_once $ip;
	$func = 'get_' . pathinfo($ip, PATHINFO_FILENAME);
	$dd_arr = $func();

	file_put_contents($op, json_format(json_encode($dd_arr)));

	if (isset($opt['v']))
	{
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

	if ($json_obj === false)
	{
		return false;
	}

	$json = json_encode($json_obj);
	$len = strlen($json);

	for ($c = 0; $c < $len; $c++) {
		$char = $json[$c];
		switch ($char) {
			case '{':
			case '[':
				if (!$in_string)
				{
					$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
					$indent_level++;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '}':
			case ']':
				if (!$in_string)
				{
					$indent_level--;
					$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ',':
				if (!$in_string)
				{
					$new_json .= ",\n" . str_repeat($tab, $indent_level);
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case ':':
				if (!$in_string)
				{
					$new_json .= ": ";
				}
				else
				{
					$new_json .= $char;
				}
				break;
			case '"':
				if ($c > 0 && $json[$c-1] != '\\')
				{
					$in_string = !$in_string;
				}
			default:
				$new_json .= $char;
				break;
		}
	}

	return $new_json;
}
