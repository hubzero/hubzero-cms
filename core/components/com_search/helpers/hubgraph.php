<?php
defined('JPATH_BASE') or die();

if (!function_exists('p'))
{
	function p($num, $label)
	{
		return Inflect::pluralizeIf($num, $label);
	}
}

if (!function_exists('h'))
{
	function h($str)
	{
		return htmlentities($str, ENT_COMPAT, 'UTF-8');
	}
}

if (!function_exists('a'))
{
	function a($str)
	{
		return str_replace('"', '&quot;', $str);
	}
}

if (!function_exists('createNonce'))
{
	function createNonce()
	{
		//set_include_path(get_include_path() . PATH_SEPARATOR . JPATH_BASE.'/libraries/openid');
		//require_once 'Auth/OpenID/Nonce.php';
		$now = time();
		$_SESSION['hg_nonce'] = sha1($now); //Auth_OpenID_mkNonce($now);
		Db::execute('INSERT INTO `#__oauthp_nonces` (created, nonce, stamp) VALUES (CURRENT_TIMESTAMP, ?, 0)', array($_SESSION['hg_nonce']));
		return $_SESSION['hg_nonce'];
	}
}
