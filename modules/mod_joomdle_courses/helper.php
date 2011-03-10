<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoomdleCoursesHelper 
{
	function filter_by_value ($array, $index, $value)
	{
		$newarray = array ();
		if(is_array($array) && count($array)>0)
		{
			foreach(array_keys($array) as $key)
			{
				$temp[$key] = $array[$key][$index];
				//if ($temp[$key] == $value){
				if (in_array ($temp[$key] ,$value)){
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	}


}
