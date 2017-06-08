<?php namespace Components\Citations\Site\Controllers;

use Hubzero\Component\SiteController;

class Curate extends SiteController
{
	const GEOCODE_TEMPLATE = 'https://maps.googleapis.com/maps/api/geocode/json?address={place}';
	const GEOCODE_API_KEY  = NULL; ///< not actually required, for now. please change to load from conf or the db if it ever is


	/**
	 * Run one of the given methods based on what is after curate/ in the URL
	 */
	public function execute() {
		$this->base = realpath(__DIR__.'/../../curate');
		static $tasks = [
			'index'          => 1,
			'cleanBibTeX'    => 1,
			'matchNames'     => 1,
			'downloadTicket' => 1,
			'download'       => 1,
			'geocode'        => 1
		];
		$rest = explode('/', isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['REDIRECT_SCRIPT_URL']);
		$task = isset($rest[3]) ? $rest[3] : 'index';
		// remove the URL prefix and the task (if present), leaving additional parameters as they arrived delimited by slashes
		$rest = array_slice($rest, 4, count($rest) - 4);
		// invalid tasks get defaulted to the index page
		if (!isset($tasks[$task])) {
			$task = 'index';
		}
		$this->$task($rest);
	}

	/**
	 * Consult the geocoding service, display its JSON response
	 */
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

	/**
	 * Hash some data, store it in the database, and respond with the hash.
	 *
	 * This is useful for two purposes, given that the full data can be
	 * retrieved by supplying the hash again:
	 *  1. Triggering a download prompt for some data that exists on the client
	 *  2. Exporting the data to a third-party service that can consume it. Only
	 *     the hash needs to be transmitted to the service, and it can call back
	 *     to convert that back into the full data. This allows the clien to
	 *     have a bookmark- and share-able link to potentially large data sets.
	 */
	public function downloadTicket() {
		header('content-type: application/json');
		static $types = [ 'bib' => 1, 'end' => 1, 'json' => 1 ];
		$post = file_get_contents('php://input');
		// verify base assumptions about the request
		if (!isset($post) || !($post = json_decode($post, TRUE)) || !isset($post['body']) || !isset($post['type']) || !isset($types[$post['type']])) {
			echo json_encode([ 'error' => 'failed to parse post body' ]);
			exit();
		}

		$body = $post['body'];
		// JSON gets inserted directly, for .bib and .end we do a bidirectional conversion using bibutils to ensure everything is formatted nicely
		// The only current practical use for this is download a .bib version of citations.
		if ($post['type'] === 'bib' || $post['type'] === 'end') {
			$body = self::execWith('/usr/bin/bib2xml', $body);
			$body = self::execWith('/usr/bin/xml2'.$post['type'], $body);
		}

		$ticket = sha1($body);
		$dbh = App::get('db');
		// There is a unique key on `ticket` and we don't care if it gets violated. This isn't security-sensitive and we do not care if someone manages an sha1 collision
		$dbh->prepare('INSERT IGNORE #__citations_download_tickets(ticket, type, data) VALUES (?, ?, ?)');
		$dbh->bind([ $ticket, $post['type'], $body ]);
		$dbh->execute();

		// Respond with both the download endpoint and the hash for interpolation into another request for convenience
		echo json_encode([ 'result' => '/citations/curate/download/'.$ticket, 'ticket' => $ticket ]);
		exit();
	}

	/**
	 * Get the data corresponding to a previous submission.
	 * See downloadTicket() for reasons why we want to do this.
	 */
	public function download($rest) {
		if (isset($_GET['hash'])) {
			$rest = [ $_GET['hash'] ];
		}
		if (!$rest) {
			return App::abort(404, 'Not found');
		}
		$dbh = App::get('db');
		$dbh->prepare('SELECT type, data FROM #__citations_download_tickets WHERE ticket = ?');
		$dbh->bind([ $rest[0] ]);
		if (!($row = $dbh->loadAssoc())) {
			return App::abort(404, 'Not found');
		}
		/// @TODO debatably correct -- client wanted more control over how the file is saved so we open it in the browser where they can save-as instead of sending a content-type that will imply it's an attachment
		header('content-type: '.($row['type'] === 'json' ? 'application/json' : 'text/plain'));
		// JSON serializations are used to make visualizations cross-domain and we're happy with that
		header('access-control-allow-origin: *');
		echo $row['data'];
		exit();
	}

	/**
	 * For the project spurring this development we happen to have a lot of the people among the citation authors
	 * that are actually users on nanoHUB. We also have the problem of needing the names to be reconciled to
	 * a canonical form for the visualization of how the citation authors network.
	 *
	 * So, one of the early ideas was to look for similar names among our users and suggest those as canonical
	 * forms.
	 *
	 * It turns out there are other much more effective things we can do aid author reconciliation, and we do
	 * so in the Vue client application, but this doesn't hurt as supplementary data.
	 */
	public function matchNames() {
		$rv = [];
		if (isset($_GET['names']) && ($search = strtolower(trim($_GET['names'])))) {
			// Split by ' and ' to get the author names. sometimes these are last, first and sometimes first last.
			// The mapping attempts to heuristically make them all first last.
			$search = array_map(function($name) {
				return implode(' ', array_reverse(preg_split('/\s*,\s*/', $name)));
			}, preg_split('/and\s+/xims', $search));

			// look for similar names in the database
			$dbh = App::get('db');
			$dbh->setQuery(
				'SELECT u.id, concat(u.givenName, \' \', case when u.middlename then concat(u.middlename, \' \') else \'\' end, u.surname) name, p.organization
				FROM #__users u
				INNER JOIN jos_xprofiles p ON p.uidNumber = u.id AND p.public)'
			);
			$seen = [];
			foreach ($dbh->loadAssocList() as $row) {
				$lcname = strtolower($row['name']);
				if (!$seen[$lcname]) {
					$score = 0;
					$pass = FALSE;
					foreach ($search as $sname) {
						$nameScore = similar_text($lcname, $sname);
						$score = max($score, $nameScore);
						// experimentally-determined decent score for similarity
						if ($nameScore > ceil(0.7 * strlen($sname))) {
							$pass = TRUE;
						}
					}
					if ($pass) {
						$rv[] = [ $score, $row['name'], $row['organization'] ];
					}
					$seen[$lcname] = true;
				}
			}
			// sort by similarity, then alphabetically
			usort($rv, function($a, $b) {
				if ($a[0] == $b[0]) {
					return strcmp($a[1], $b[1]);
				}
				return $a[0] > $b[0] ? -1 : 1;
			});
		}
		header('content-type: application/json');
		// fudging object wrapper rather than json_encode([ 'result' => json_encode($rv) ]) saves a _lot_ of memory!
		echo '{"result": '.json_encode($rv).'}';
		exit();
	}

	/**
	 * Show the index file, which doesn't do anything but load the Vue app
	 */
	public function index() {
		$dbh = App::get('db'); 
		$dbh->setQuery('SELECT name FROM #__citations_visualizations');
		$visualizations = array_map(function() { return true; }, array_flip(array_map(function($row) { return $row[0]; }, $dbh->loadRowList())));
		require $this->base.'/index.php';
	}

	/**
	 * Given some form input that purports to be BibTeX, convert it to something that is definitely BibTeX by
	 * using the bibutils package to parse it to XML and then back into BibTeX.
	 *
	 * Works with .end files as well, but that's currently unadvertised because people confuse it with .enl,
	 * for which bibutils does not provide a parser.
	 */
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

			// I'm not sure why the result of xml2bib is not already valid UTF-8 but it will fail the JSON encoding process if it has UTF codepoints and you don't do this:
			$bib = mb_convert_encoding($bib, 'UTF-8', 'UTF-8');
			echo json_encode([ 'result' => $bib ]);
		}
		catch (\Exception $ex) {
			echo json_encode([ 'error' => $ex->getMessage() ]);
		}
		exit();
	}

	/**
	 * Wrapper to pipe a document to one of the utilities we use, thus avoiding having to create and clean up tmp files
	 */
	private static function execWith($cmd, $stdin) {
		$descriptors = [
			['pipe', 'r'], // stdin
			['pipe', 'w'], // stdout
			['pipe', 'w']  // stderr
		];
		$process = proc_open($cmd, $descriptors, $pipes, '/tmp', [ 'LANG' => 'en_US.utf-8' ]);
		if (!is_resource($process)) {
			throw new \Exception('failed to execute utility');
		}
		// the order in which the pipe operations are done is sensitive
 		fwrite($pipes[0], $stdin);
		fclose($pipes[0]);

		$rv = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		if (($code = proc_close($process)) !== 0) {
			throw new \Exception('utility returned error code '.$code.', '.$cmd);
		}
		return $rv;
	}
}

