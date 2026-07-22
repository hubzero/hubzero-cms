<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Datastore Lite helpers — shared between the in-request packager
 * (attachments/data.php) and the off-request bundle builder (BundleBuilder),
 * so the CSV export of a stored database is produced one way only.
 */
class Datastore
{
	/**
	 * Generate a CSV export of a Datastore Lite database at a given revision,
	 * via com_dataviewer. Writes the CSV to $tmpFile when given (returns true on
	 * success), otherwise returns the CSV string. Pure data work — no web/URL
	 * context — so it runs in a detached CLI worker as well as in a request
	 * (verified headless: byte-identical to the in-request output).
	 *
	 * @param   string  $dbName   datastore object name (publication_attachments.object_name)
	 * @param   string  $version  datastore revision    (publication_attachments.object_revision)
	 * @param   string  $tmpFile  destination path, or '' to return the CSV string
	 * @return  mixed   true|false when $tmpFile is given, else the CSV string|false
	 */
	public static function generateCsv($dbName = '', $version = '', $tmpFile = '')
	{
		if (!$dbName || !$version)
		{
			return false;
		}

		mb_internal_encoding('UTF-8');

		// com_dataviewer site library (procedural)
		$dv = \Component::path('com_dataviewer') . DS . 'site';

		require_once $dv . DS . 'dv_config.php';
		require_once $dv . DS . 'lib' . DS . 'db.php';
		require_once $dv . DS . 'modes' . DS . 'mode_dsl.php';
		require_once $dv . DS . 'filter' . DS . 'csv.php';

		$dv_conf = get_conf(null);
		$dd      = get_dd(null, $dbName, $version);

		$dd['serverside'] = false;

		$sql    = query_gen($dd);
		$result = get_results($sql, $dd);

		ob_start();
		filter($result, $dd, true);
		$csv = ob_get_contents();
		ob_end_clean();

		if ($csv && $tmpFile)
		{
			$handle = fopen($tmpFile, 'w');
			if ($handle === false)
			{
				return false;
			}
			fwrite($handle, $csv);
			fclose($handle);

			return true;
		}

		return $csv;
	}
}
