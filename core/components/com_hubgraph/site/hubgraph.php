<?php
defined('_HZEXEC_') or die();

require 'Inflect.php';

/*
[!] - (zooley) Added NotFoundError class because PHP was throwing 
	fatal error that the class was not found (irony?).
*/
if (!class_exists('NotFoundError'))
{
	class NotFoundError extends InvalidArgumentException
	{
	}
}

foreach (array('SCRIPT_URL', 'URL', 'REDIRECT_SCRIPT_URL', 'REDIRECT_URL') as $k)
{
	if (isset($_SERVER[$k]))
	{
		$base = $_SERVER[$k];
		break;
	}
}
$basePath = preg_replace('#^'.preg_quote(PATH_ROOT).'#', '', __DIR__);


\Pathway::append('Search', $base);

function hgView($view, $args = array())
{
	header('Content-type: text/json');
	echo HubgraphClient::execView($view, array_merge($_GET, $args));
	exit();
}

function p($num, $label)
{
	return Inflect::pluralizeIf($num, $label);
}

function h($str)
{
	return htmlentities($str);
}

function a($str)
{
	return str_replace('"', '&quot;', $str);
}

function assertSuperAdmin()
{
	if (\User::get('usertype') != 'Super Administrator')
	{
		\App::abort(405, 'Forbidden');
	}
}

function createNonce()
{
//	set_include_path(get_include_path() . PATH_SEPARATOR . JPATH_BASE.'/libraries/openid');
//	require_once 'Auth/OpenID/Nonce.php';
	$now = time();
	$_SESSION['hg_nonce'] = sha1($now); //Auth_OpenID_mkNonce($now);
	Db::execute('INSERT INTO jos_oauthp_nonces(created, nonce, stamp) VALUES (CURRENT_TIMESTAMP, ?, 0)', array($_SESSION['hg_nonce']));
	return $_SESSION['hg_nonce'];
}

function consumeNonce($form)
{
	$now = time();
	if (!isset($form['nonce']) || $form['nonce'] != $_SESSION['hg_nonce']
		|| !preg_match('/^\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\dZ/', $form['nonce'], $ma)
		|| !($timestamp = strtotime($ma[0]))
		|| $timestamp > $now
		|| $timestamp < $now - 60 * 60
		|| Db::scalarQuery('SELECT stamp FROM jos_oauthp_nonces WHERE nonce = ?', array($form['nonce'])))
	{
		//throw new Exception('Bad token', 405);
	}
	Db::execute('UPDATE jos_oauthp_nonces SET stamp = 1 WHERE nonce = ?', array($form['nonce']));
	unset($_SESSION['hg_nonce']);
}

require_once 'client.php';
require_once 'request.php';
$req = new HubgraphRequest($_GET);
$conf = HubgraphConfiguration::instance();
$perPage = 40;

try
{
	switch ($task = !defined('HG_INLINE') && isset($_REQUEST['task']) ? $_REQUEST['task'] : 'index')
	{
		case 'complete':
			hgView('complete', array('limit' => 20, 'threshold' => 3, 'tagLimit' => 100));
		case 'getRelated':
			hgView('related', $req->getTransportCriteria(array('limit' => 5, 'domain' => $_GET['domain'], 'id' => $_GET['id'])));
		case 'index': case 'page':
			$results = $req->anyCriteria()
				? json_decode(HubgraphClient::execView('search', $req->getTransportCriteria(array('limit' => $perPage))), TRUE)
				: NULL;
			$tags         = $req->getTags();
			$users        = $req->getContributors();
			$groups       = $req->getGroup();
			$domainMap    = $req->getDomainMap();
			$loggedIn     = (bool) User::get('id');
			ksort($domainMap);

			if ($task == 'page')
			{
				define('HG_AJAX', 1);
				require 'views/page.html.php';
				exit();
			}
			else
			{
				require 'views/index-update.html.php';
			}
		break;
		case 'update':
			$results = $req->anyCriteria()
				? json_decode(HubgraphClient::execView('search', $req->getTransportCriteria(array('limit' => $perPage))), TRUE)
				: NULL;
			define('HG_AJAX', 1);
			require 'views/index-update.html.php';
			exit();
		break;
		case 'settings':
			assertSuperAdmin();
			require 'views/settings.html.php';
		break;
		case 'updateSettings':
			assertSuperAdmin();
			consumeNonce($_POST);
			$conf->bind($_POST)->save();
			header('Location: /hubgraph?task=settings');
			exit();
		break;
		default:
			throw new NotFoundError('no such task');
	}
}
catch (Exception $ex)
{
	echo $ex->getMessage();
	exit();
	error_log($ex->getMessage());
	if (!defined('HG_INLINE'))
	{
		header('Location: '.Route::url('index.php?option=com_search' . (isset($_GET['terms']) ? '&terms='.$_GET['terms'] : '')));
		exit();
	}
}
