<?php
defined('_HZEXEC_') or die();

require_once 'db.php';

class HubgraphConnectionError extends \Exception
{
}

class HubgraphConfiguration implements \ArrayAccess, \Iterator
{
	private static $inst;
	private static $defaultSettings = array(
		'host' => 'unix:///var/run/hubgraph/hubgraph-server.sock',
		'port' => NULL,
		'showTagCloud' => TRUE,
		'enabledOptions' => ''
	);
	private $settings, $idx;

	private function __construct()
	{
		if (!$this->settings)
		{
			$this->settings = self::$defaultSettings;
		}
	}

	public static function instance()
	{
		if (!self::$inst)
		{
			$query = 'SELECT params FROM jos_extensions WHERE `type`=\'component\' AND `element` = \'com_hubgraph\'';

			$conf = Db::scalarQuery($query);
			if ($conf)
			{
				if (isset($conf[0]) && $conf[0] != '{')
				{
					self::$inst = unserialize($conf);
				}
				else
				{
					self::$inst = new HubgraphConfiguration;
					self::$inst->settings = json_decode($conf, TRUE);
				}
			}
			if (!self::$inst instanceof HubgraphConfiguration)
			{
				self::$inst = new HubgraphConfiguration;
				self::$inst->save();
			}
			self::$inst->validate();
		}
		return self::$inst;
	}

	public function save()
	{
		$params = json_encode($this->settings);

		$updateQuery = 'UPDATE jos_extensions SET params = ? WHERE `type`=\'component\' AND `element` = \'com_hubgraph\'';
		$insertQuery = 'INSERT INTO jos_extensions(name, `type`, `element`, params) VALUES (\'HubGraph\', \'component\', \'com_hubgraph\', ?)';

		if (!Db::update($updateQuery, array($params))) {
			Db::execute($insertQuery, array($params));
		}
	}

	private function validate()
	{
		foreach (self::$defaultSettings as $k=>$v)
		{
			if (!array_key_exists($k, $this->settings))
			{
				$this->settings[$k] = $v;
			}
		}
	}

	public function bind($form)
	{
		foreach (array_keys($this->settings) as $k)
		{
			if (array_key_exists($k, $form))
			{
				$this->settings[$k] = $form[$k] == '' ? NULL : $form[$k];
			}
		}
		return $this;
	}

	public function isOptionEnabled($opt)
	{
		return in_array($opt, explode(',', $this->settings['enabledOptions']));
	}

	public static function niceKey($k)
	{
		return ucfirst(preg_replace_callback('/([A-Z])+/', function($ma) { return ' '.strtolower($ma[1]); }, $k));
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset) || !isset($this->settings[$offset]))
		{
			throw new \Exception('not supported');
		}
		$this->settings[$offset] = $value;
	}
	public function offsetExists($offset)
	{
		return isset($this->settings[$offset]);
	}
	public function offsetUnset($_offset)
	{
		throw new \Exception('not supported');
	}
	public function offsetGet($offset)
	{
		if (!$this->offsetExists($offset))
		{
			return NULL;
		}
		return $this->settings[$offset];
	}
	public function rewind()
	{
		return reset($this->settings);
	}
	public function current()
	{
		return current($this->settings);
	}
	public function key()
	{
		return key($this->settings);
	}
	public function next()
	{
		return next($this->settings);
	}
	public function valid()
	{
		return array_key_exists($this->key(), $this->settings);
	}
}

class HubgraphClient
{
	const CHUNK_LEN = 1024;

	private static function http($method, $url, $entity = NULL)
	{
		$conf = HubgraphConfiguration::instance();
		if (!($sock = @fsockopen($conf['host'], $conf['port'], $_errno, $errstr, 1)))
		{
			throw new HubGraphConnectionError('unable to establish HubGraph connection using '.$conf['host'].': '.$errstr);
		}

		fwrite($sock, "$method $url HTTP/1.1\r\n");
		fwrite($sock, "Host: localhost\r\n");
		fwrite($sock, "X-HubGraph-Request: ".sha1(uniqid())."\r\n");
		if ($entity)
		{
			fwrite($sock, "Content-Length: ".strlen($entity)."\r\n");
		}
		fwrite($sock, "Connection: close\r\n\r\n");
		if ($entity)
		{
			fwrite($sock, $entity);
		}

		$first = true;
		$inHeaders = true;
		$status = NULL;
		$body = '';
		while (($chunk = fgets($sock, self::CHUNK_LEN))) {
			if ($first && !preg_match('/^HTTP\/1\.1\ (\d{3})/', $chunk, $code)) {
				throw new \Exception('Unable to determine response status');
			}
			elseif ($first) {
				if (($status = intval($code[1])) === 204) {
					break;
				}
				$first = false;
			}
			elseif ($inHeaders && preg_match('/^[\r\n]+$/', $chunk)) {
				$inHeaders = false;
			}
			elseif (!$inHeaders) {
				$body .= $chunk;
			}
		}
		fclose($sock);
		return array($status, $body);
	}

	public static function execView($key, $args = NULL)
	{
		static $count = 0;
		++$count;
		$path = '/views/'.$key;
		$query = '';
		if ($args) {
			foreach ($args as $k=>$v) {
				if (is_array($v)) {
					$query .= ($query == '' ? '' : '&').$k.'='.implode(',', array_map('urlencode', $v));
				}
				else {
					$query .= ($query == '' ? '' : '&').$k.'='.(is_bool($v) ? ($v ? 'true' : 'false') : urlencode($v));
				}
			}
		}
		$query .= '&count='.$count;
		list($code, $entity) = self::http('GET', $path.'?'.$query);
		return $entity;
	}
}
