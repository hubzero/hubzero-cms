<? defined('JPATH_BASE') or die(); 


class Wiki
{
private static $parser = NULL;

	public static function parse($str) {
		if (is_null(self::$parser)) {
			ximport('Hubzero_Wiki_Parser');
			self::$parser = new Hubzero_Wiki_Parser();
		}

		/// @TODO: could stand to be less hacky and awful
		$wikiconfig = array(
			'option'   => 'com_components/com_forms',
			'scope'    => 'images',
			'pagename' => 'form_images',
			'pageid'   => 0,
			'filepath' => '/site/forms',
			'domain'   => ''
		);
		$wikiConfig = array();
		return preg_replace('#<a rel="lightbox".*?>(.*?)</a>#', '$1', preg_replace('/Image:/', '', self::$parser->parse($str, $wikiconfig)));
	}
}

class Db
{
	private static $dbh, $log, $timer;

	public static function getHandle() {
		if (!self::$dbh) {
			$cfg = new JConfig;
			self::$dbh = new PDO('mysql:host=localhost;dbname='.$cfg->db, $cfg->user, $cfg->password, array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		}
		return self::$dbh;
	}

	public static function getStatementHandle($sql, $params = array(), &$success = NULL) {
		$sth = self::getHandle()->prepare($sql);
		$success = $sth->execute($params);
		return $sth;
	}

	public static function query($sql, $params = array()) {
		return self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function scalarQuery($sql, $params = array()) {
		$rv = self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_NUM);
		return $rv && array_key_exists(0, $rv[0]) ? $rv[0][0] : NULL;
	}

	public static function insert($sql, $params = array()) {
		self::getStatementHandle($sql, $params, $success);
		return $success ? self::$dbh->lastInsertId() : NULL;
	}
	
	public static function update($sql, $params = array()) {
		$sth = self::getStatementHandle($sql, $params, $success);
		return $success ? $sth->rowCount() : FALSE;
	}

	public static function execute($sql, $params = array()) {
		return self::update($sql, $params);
	}

	public static function startTransaction() {
		self::getHandle()->query('START TRANSACTION');
	}

	public static function commit() {
		self::getHandle()->query('COMMIT');
	}
	
	public static function rollback() {
		self::getHandle()->query('ROLLBACK');
	}
}

$base = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL']; 
$basePath = preg_replace('#^'.preg_quote(JPATH_BASE).'#', '', dirname(__FILE__));

$doc = JFactory::getDocument();
//$doc->addScript('/jquery.js');
$doc->addScript($basePath.'/resources/hubgraph.js');
$doc->addStyleSheet($basePath.'/resources/hubgraph.css');

$path = JFactory::getApplication()->getPathway();
$path->addItem('Search', $base);

function hgView($view, $args = array()) {
	header('Content-type: text/json');
	echo HubgraphClient::execView($view, array_merge($_GET, $args));
	exit();
}

function h($str) {
	return htmlentities($str);
}

function a($str) {
	return str_replace('"', '&quot;', $str);
}

function assertSuperAdmin() {
	if (JFactory::getUser()->usertype != 'Super Administrator') {
		JError::raiseError(405, 'Forbidden');
	}
}

function createNonce() {
	set_include_path(get_include_path() . PATH_SEPARATOR . JPATH_BASE.'/libraries/openid');			
	require_once 'Auth/OpenID/Nonce.php';
	$now = time();
	$_SESSION['hg_nonce'] = Auth_OpenID_mkNonce($now);
	Db::execute('INSERT INTO jos_oauthp_nonces(created, nonce, stamp) VALUES (CURRENT_TIMESTAMP, ?, 0)', array($_SESSION['hg_nonce']));
	return $_SESSION['hg_nonce'];
}

function consumeNonce($form) {
	$now = time();
	if (!isset($form['nonce']) || $form['nonce'] != $_SESSION['hg_nonce']
		|| !preg_match('/^\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\dZ/', $form['nonce'], $ma)
		|| !($timestamp = strtotime($ma[0]))
		|| $timestamp > $now
		|| $timestamp < $now - 60 * 60
		|| Db::scalarQuery('SELECT stamp FROM jos_oauthp_nonces WHERE nonce = ?', array($form['nonce']))) {
		JError::raiseError(405, 'Bad token');
	}
	Db::execute('UPDATE jos_oauthp_nonces SET stamp = 1 WHERE nonce = ?', array($form['nonce']));
	unset($_SESSION['hg_nonce']);
}

require 'client.php';
require 'request.php';
$req = new HubgraphRequest($_GET);
$conf = HubgraphConfiguration::instance();
$perPage = 40;

try {
	switch (!defined('HG_INLINE') && isset($_REQUEST['task']) ? $_REQUEST['task'] : 'index') {
		case 'complete':
			hgView('complete', array('limit' => 20, 'threshold' => 3, 'tagLimit' => 100));
		case 'getRelated':
			hgView('related', array('limit' => 5, 'domain' => $_GET['domain'], 'id' => $_GET['id']));
		case 'index':
			$results = $req->anyCriteria() 
				? json_decode(HubgraphClient::execView('search', $req->getTransportCriteria(array('limit' => $perPage))), TRUE) 
				: NULL;
			require 'views/index.html.php';
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
catch (Exception $ex) {
	error_log($ex->getMessage());
	if (!defined('HG_INLINE')) {
		header('Location: /ysearch'.(isset($_GET['terms']) ? '?terms='.$_GET['terms'] : ''));
		exit();
	}
}
