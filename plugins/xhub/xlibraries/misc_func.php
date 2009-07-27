<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

function altrow($row) {
	if($row % 2 == 0) {
		return('class="row1"');
	} else {
		return('class="row2"');
	}
}


function quote($str) {
	$ret = '"' . str_replace('"', '\\"', $str) . '"';
	return($ret);
}


function thisurl($cutgetvars = 0) {
	if(!empty($_SERVER['REDIRECT_URL'])) {
		$thisurl = $_SERVER['REDIRECT_URL'];
	}
	else {
		$thisurl = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], "/") + 1);
	}
	if($cutgetvars) {
		$pvar = strpos($thisurl, "?");
		if($pvar) {
			$thisurl = substr($thisurl, 0, $pvar);
		}
	}
	return($thisurl);
}


function arraykeyedcmp($a, $b) {
	return(strnatcasecmp($a['sortkey'], $b['sortkey']));
}


function arraykeyeddesccmp($a, $b) {
	return(strnatcasecmp($b['sortkey'], $a['sortkey']));
}


function arraykeyedsort(&$arr, $keys) {
	$max = array();
	for($k = 0; $k < count($keys); $k++) {
		$max[$k] = 0;
		for($i = 0; $i < count($arr); $i++) {
			$len = strlen($arr[$i][$keys[$k]]);
			if($len > $max[$k]) {
				//$max[$k] = $arr[$i][$key];
				$max[$k] = $len;
			}
		}
	}
	for($i = 0; $i < count($arr); $i++) {
		$arr[$i]['sortkey'] = '';
		for($k = 0; $k < count($keys); $k++) {
			$arr[$i]['sortkey'] .= str_pad($arr[$i][$keys[$k]], $max[$k], chr(0));
		}
	}
	return(usort($arr, "arraykeyedcmp"));
}


function caturl($a, $b, $vars = '') {
	$caturl = $a;
	if(strrpos($a, '/') === (strlen($a) - 1) && strpos($b, '/') === 0) {
		$caturl .= substr($b, 1);
	}
	else {
		$caturl .= $b;
	}
	if($vars) {
		if(strpos($caturl, '?')) {
			$caturl .= '&';
		}
		else {
			$caturl .= '?';
		}
		$caturl .= $vars;
	}
	return($caturl);
}

function stat_dbopen(&$db) {
	$xhub = &XFactory::getHub();
	$usagestats_dbhost = $xhub->getCfg('statsDBHost');
	$usagestats_username = $xhub->getCfg('statsDBUsername');
	$usagestats_password = $xhub->getCfg('statsDBPassword');
	$usagestats_database = $xhub->getCfg('statsDBDatabase');
	$db = mysql_connect($usagestats_dbhost, $usagestats_username, $usagestats_password);
	if($db) { mysql_select_db($usagestats_database, $db); }
}

function stat_dbclose() {
	mysql_close();
}

function net_dbopen(&$db) {
	$xhub = &XFactory::getHub();
	$dbhost = $xhub->getCfg('ipDBHost');
	$username = $xhub->getCfg('ipDBUsername');
	$password = $xhub->getCfg('ipDBPassword');
	$database = $xhub->getCfg('ipDBDatabase');
//var_dump($dbhost); var_dump($username); var_dump($password);
	$db = mysql_connect($dbhost, $username, $password);
	if($db) { mysql_select_db($database, $db); }
	if (!$db) die('database error');
}

function net_dbclose() {
	mysql_close();
}


function getcountries() {
	$countries = array();
	net_dbopen($db);
	if($db) {
	$sql = "SELECT LOWER(code), name FROM countries ORDER BY name";
	$result = mysql_query($sql, $db);
	if($result) {
		if(mysql_num_rows($result) > 0) {
			$row = mysql_fetch_row($result);
			while($row) {
				if($row[0] <> "-" && $row[1] <> "-") {
					array_push($countries, array('code' => $row[0], 'id' => $row[0], 'name' => $row[1]));
				}
				$row = mysql_fetch_row($result);
			}
		}
		mysql_free_result($result);
	}
	net_dbclose();
	}
	return($countries);
}


function getcountry($code = '') {
	$name = '';
	if($code) {
		net_dbopen($db);
		if($db) {
		$sql = "SELECT name FROM countries WHERE code = '" . mysql_escape_string($code) . "'";
		$result = mysql_query($sql, $db);
		if($result) {
			if(mysql_num_rows($result) > 0) {
				if($row = mysql_fetch_row($result)) {
					$name = $row[0];
				}
			}
			mysql_free_result($result);
		}
		net_dbclose();
		}
	}
	return($name);
}


function ipcountry($ip) {
	$country = '';
	if($ip) {
		net_dbopen($db);
		if($db) {
		$sql = "SELECT LOWER(countrySHORT) FROM ipcountry WHERE ipFROM <= INET_ATON('" . mysql_escape_string($ip) . "') AND ipTO >= INET_ATON('" . mysql_escape_string($ip) . "')";
		$result = mysql_query($sql, $db);
		if($result) {
			if(mysql_num_rows($result) > 0) {
				if($row = mysql_fetch_row($result)) {
					$country = $row[0];
				}
			}
			mysql_free_result($result);
		}
		net_dbclose();
		}
	}
	return($country);
}


function is_d1nation($country) {
	$d1nation = false;
	if($country) {
		net_dbopen($db);
		if($db) {
			$sql = "SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . mysql_escape_string($country) . "') AND countrygroup = 'D1'";
			$result = mysql_query($sql, $db);
			if($result) {
				if(mysql_num_rows($result) > 0) {
					if($row = mysql_fetch_row($result)) {
						if($row[0] > 0) {
							$d1nation = true;
						}
					}
				}
				mysql_free_result($result);
			}
			net_dbclose();
		}
	}
	return($d1nation);
}


function is_iplocation($ip, $location) {
	$iplocation = false;
	if($ip && $location) {
		net_dbopen($db);
		if($db) {
			$sql = "SELECT COUNT(*) FROM iplocation WHERE ipfrom <= INET_ATON('" . mysql_escape_string($ip) . "') AND ipto >= INET_ATON('" . mysql_escape_string($ip) . "') AND LOWER(location) = LOWER('" . mysql_escape_string($location) . "')";
			$result = mysql_query($sql, $db);
			if($result) {
				if(mysql_num_rows($result) > 0) {
					if($row = mysql_fetch_row($result)) {
						if($row[0] > 0) {
							$iplocation = true;
						}
					}
				}
				mysql_free_result($result);
			}
			net_dbclose();
		}
	}
	return($iplocation);
}


//------------------------------------------------------------------//
//  Format Value as Unaltered(0), Numeric(1), or Day/Time-Range(2)  //
//------------------------------------------------------------------//
function valformat($value, $format) {
	if($format == 1) {
		return(number_format($value));
	}
	elseif($format == 2 || $format == 3) {
		if($format == 2) {
			$min = round($value / 60);
		}
		else {
			$min = floor($value / 60);
			$sec = $value - ($min * 60);
		}
		$hr = floor($min / 60);
		$min -= ($hr * 60);
		$day = floor($hr / 24);
		$hr -= ($day * 24);
		if($day == 1) {
			$day = "1 day, ";
		}
		elseif($day > 1) {
			$day = number_format($day) . " days, ";
		}
		else {
			$day = "";
		}
		if($format == 2) {
			return(sprintf("%s%d:%02d", $day, $hr, $min));
		}
		else {
			return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
		}
	}
	else {
		return($value);
	}
}


function is_positiveint($x) {
	if(is_numeric($x) && intval($x) == $x && $x >= 0) {
		return(true);
	}
	else {
		return(false);
	}
}


function propercase($str) {
	$size = 0;
	$dont_case = array('a', 'an', 'of', 'the', 'are', 'at', 'in');
	$str = trim($str);
	$str = strtoupper($str[0]) . strtolower(substr($str, 1));
	for($i = 1; $i < strlen($str) - 1; ++$i) {
		if($str[$i] == ' ') {
			for($j = $i + 1; $j < strlen($str) && $str[$j] != ' '; ++$j);
			$size = $j - $i - 1;
			$short_word = false;
			if($size <= 3) {
				$word = substr($str, $i + 1, $size);
				for($j = 0; $j < count($dont_case) && !$short_word; ++$j) {
					if($word == $dont_case[$j]) {
						$short_word = true;
					}
				}
			}
			if(!$short_word) {
				$str = substr($str, 0, $i + 1) . strtoupper($str[$i + 1]) . substr($str, $i + 2);
			}
		}   
		$i += $size;
	}
	return($str);
}


function date2epoch($datestr){
	if (empty($datestr))
		return null;

	list ($date, $time) = explode(' ', $datestr);
	list ($y, $m, $d) = explode('-', $date);
	list ($h, $i, $s) = explode(':', $time);
	return(mktime($h, $i, $s, $m, $d, $y));
}

?>
