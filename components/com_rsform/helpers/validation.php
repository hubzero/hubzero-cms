<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.mail.helper');

class RSFormProValidations
{
	function none($value,$extra=null)
	{
		return true;
	}

	function email($email,$extra=null)
	{
		$email = trim($email);
		return JMailHelper::isEmailAddress($email);
	}
	function numeric($param,$extra=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if (strpos($extra,$param[$i]) === false && !is_numeric($param[$i]))
				return false;
				
		return true;
	}

	function alphanumeric($param,$extra = null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z0-9 ])#', $param[$i]))
				return false;
				
		return true;
	}

	function alpha($param,$extra=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
			
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z ])#', $param[$i]))
				return false;
				
		return true;
	}

	function custom($param,$extra=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false)
				return false;
				
		return true;
	}

	function password($param,$extra=null)
	{
		return true;
	}
}
?>