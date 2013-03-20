<? defined('JPATH_BASE') or die(); 

class HubgraphConnectionError extends Exception
{
}

class HubgraphClient {
	const DEFAULT_HOST = 'unix:///tmp/hubgraph-server.sock';
	const DEFAULT_PORT = NULL;

	const CHUNK_LEN = 1024;

	private static function getConfiguration() {
		static $conf;
		if (!$conf) {
			$conf = Db::scalarQuery('SELECT params FROM jos_components WHERE `option` = \'com_hubgraph\'');
			if ($conf) {
				$conf = unserialize($conf);
			}
			else {
				Db::execute('INSERT INTO jos_components(name, `option`, params) VALUES (\'HubGraph\', \'com_hubgraph\', ?)', array(serialize(array('host' => self::DEFAULT_HOST, 'port' => self::DEFAULT_PORT))));
				return self::getConfiguration();
			}
		}
		return $conf;
	}

	private static function http($method, $url, $entity = NULL) {
		$conf = self::getConfiguration();
		if (!($sock = @fsockopen($conf['host'], $conf['port'], $_errno, $errstr, 1))) {
			throw new HubGraphConnectionError('unable to establish HubGraph connection: '.$errstr);
		}

		fwrite($sock, "$method $url HTTP/1.1\r\n");
		fwrite($sock, "Host: localhost\r\n");
		fwrite($sock, "X-HubGraph-Request: ".sha1(uniqid())."\r\n");
		if ($entity) {
			fwrite($sock, "Content-Length: ".strlen($entity)."\r\n");
		}
		fwrite($sock, "Connection: close\r\n\r\n");
		if ($entity) {
			fwrite($sock, $entity);
		}

		$first = true;
		$inHeaders = true;
		$status = NULL;
		$body = '';
		while (($chunk = fgets($sock, self::CHUNK_LEN))) {
			if ($first && !preg_match('/^HTTP\/1\.1\ (\d{3})/', $chunk, $code)) {
				throw new Exception('Unable to determine response status');
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

	public static function execView($key, $args = NULL) {
		static $count = 0;
		++$count;
		$path = '/views/'.$key;
	//$query = $_SERVER['QUERY_STRING'];
		$query = '';
		if ($args) {
			foreach ($args as $k=>$v) {
				$query .= ($query == '' ? '' : '&').$k.'='.(is_bool($v) ? ($v ? 'true' : 'false') : urlencode($v));
			}
		}
		$query .= '&count='.$count;
		list($code, $entity) = self::http('GET', $path.'?'.$query);
		return $entity;
	}
}
