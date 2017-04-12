<?php

namespace Components\Citations\Site\Controllers;

use Hubzero\Component\SiteController;

class Curate extends SiteController
{
	const GEOCODE_TEMPLATE = 'https://maps.googleapis.com/maps/api/geocode/json?address={place}';
	const GEOCODE_API_KEY  = NULL;

	public function execute() {
		$this->base = realpath(__DIR__.'/../../curate');
		static $tasks = [ 'index' => 1, 'cleanBibTeX' => 1, 'matchNames' => 1, 'downloadTicket' => 1, 'download' => 1, 'geocode' => 1 ];
		$rest = explode('/', isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL']);
		$task = isset($rest[3]) ? $rest[3] : 'index';
		$rest = array_slice($rest, 4, count($rest) - 4);
		if (!isset($tasks[$task])) {
			$task = 'index';
		}	
		$this->$task($rest);
	}

	public function geocode() {
		header('content-type: application/json');
		if (!isset($_GET['place'])) {
			echo json_encode([ 'error' => 'failed to parse post body' ]);
			exit();
		}

		$ch = curl_init();
		$copts = [
			\CURLOPT_URL            => str_replace('{place}', urlencode($_GET['place']), self::GEOCODE_TEMPLATE).(self::GEOCODE_API_KEY ? '&key='.self::GEOCODE_API_KEY : ''),
			\CURLOPT_RETURNTRANSFER => true
		];
		curl_setopt_array($ch, $copts);
		$res = curl_exec($ch);
		echo $res;
		exit();
	}


	public function download($rest) {
		if (isset($_GET['hash'])) {
			$rest = [ $_GET['hash'] ];
		}
		if (!$rest) {
			return App::abort(404, 'Not found');
		}
		$dbh = App::get('db');
		$dbh->setQuery('select type, data from #__citations_download_tickets where ticket = '.$dbh->quote($rest[0]));
		if (!($row = $dbh->loadAssoc())) {
			return App::abort(404, 'Not found');
		}
		header('content-type: '.($row['type'] === 'json' ? 'application/json' : 'text/plain'));
		header('access-control-allow-origin: *');
		echo $row['data'];
		exit();
	}

	public function downloadTicket() {
		header('content-type: application/json');
		static $types = [ 'bib' => 1, 'end' => 1, 'json' => 1 ];
		$post = file_get_contents('php://input');
		if (!isset($post) || !($post = json_decode($post, TRUE)) || !isset($post['body']) || !isset($post['type']) || !isset($types[$post['type']])) {
			echo json_encode([ 'error' => 'failed to parse post body' ]);
			exit();
		}
	
		$body = $post['body'];	
		if ($post['type'] === 'bib' || $post['type'] === 'end') {
			$body = self::execWith('/usr/bin/bib2xml', $body);
			$body = self::execWith('/usr/bin/xml2'.$post['type'], $body);
		}
		else if ($post['type'] === 'csv') {
			ob_start();
			$stdout = fopen('php://output', 'w');
			fputcsv($stdout, [ 'journal', 'title' ]);
			foreach (json_decode($body, TRUE) as $row) {
				$row = array_map(function($col) {
					return str_replace('\\&', '', $col);
				}, $row);
				fputcsv($stdout, $row);
			}
			$body = ob_get_clean();
		}
		$ticket = sha1($body);
		$dbh = App::get('db'); 
		$dbh->setQuery('insert ignore into #__citations_download_tickets(ticket, type, data) values ('.$dbh->quote($ticket).', '.$dbh->quote($post['type']).', '.$dbh->quote($body).')');
		$dbh->execute();
		echo json_encode([ 'result' => '/citations/curate/download/'.$ticket, 'ticket' => $ticket ]);
		exit();
	}

	public function matchNames() {
		$rv = [];
		if (isset($_GET['names']) && ($search = strtolower(trim($_GET['names'])))) {
			$search = array_map(function($name) {
				return implode(' ', array_reverse(preg_split('/\s*,\s*/', $name)));
			}, preg_split('/and\s+/xims', $search));
			$dbh = App::get('db');
			$dbh->setQuery('select u.id, concat(u.givenName, \' \', case when u.middlename then concat(u.middlename, \' \') else \'\' end, u.surname) name, p.organization from #__users u inner join jos_xprofiles p on p.uidNumber = u.id and (p.public or u.givenName = \'Dheeraj\')'); ///@TODO rm dheeraj after demo
			$seen = [];
			foreach ($dbh->loadAssocList() as $row) {
				$lcname = strtolower($row['name']);
				if (!$seen[$lcname]) {
					$score = 0;
					$pass = FALSE;
					foreach ($search as $sname) {
						$nameScore = similar_text($lcname, $sname);
						$score = max($score, $nameScore);
						if ($nameScore > ceil(0.7*strlen($sname))) {
							$pass = TRUE;
						}
					}
					if ($pass) {
						$rv[] = [ $score, $row['name'], $row['organization'] ];
					}
					$seen[$lcname] = true;
				}
			}
			usort($rv, function($a, $b) {
				if ($a[0] == $b[0]) {
					return strcmp($a[1], $b[1]);
				}
				return $a[0] > $b[0] ? -1 : 1;
			});
		}
		header('content-type: application/json');
		// fudging object wrapper rather than json_encode([ 'result' => json_encode($rv) ]) seems to save a lot of memory
		echo '{"result": '.json_encode($rv).'}';
		exit();
	}

	public function index() {
		require $this->base.'/index.php';
	}

	public function cleanBibTeX() {
		header('content-type: application/json');
		$types = ['bib' => 1, 'end' => 1];
		$post = file_get_contents('php://input');
		if (!isset($post) || !($post = json_decode($post, TRUE)) || !isset($post['body']) || !isset($post['type']) || !isset($types[$post['type']])) {
			echo json_encode([ 'error' => 'failed to parse post body' ]);
			exit();
		}
		try {
			$xml = self::execWith('/usr/bin/'.$post['type'].'2xml', $post['body']);
			$bib = self::execWith('/usr/bin/xml2bib-nl', $xml);
			$bib = mb_convert_encoding($bib, 'UTF-8', 'UTF-8');
			echo json_encode([ 'result' => $bib ]);
		}
		catch (\Exception $ex) {
			echo json_encode([ 'error' => $ex->getMessage() ]);
		}
		exit();
	}

	private static function execWith($cmd, $stdin) {
		$descriptorspec = [
			['pipe', 'r'], // stdin
			['pipe', 'w'], // stdout
			['pipe', 'w']  // stderr
		];
		$process = proc_open($cmd, $descriptorspec, $pipes, '/tmp', [ 'LANG' => 'en_US.utf-8' ]);
		if (!is_resource($process)) {
			throw new \Exception('failed to execute utility');
		}
 		fwrite($pipes[0], $stdin);
		fclose($pipes[0]);

		$rv = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		if (($code = proc_close($process)) !== 0) {
			throw new \Exception('utlity returned error code '.$code.', '.$cmd);
		}
		return $rv;
	}
}

