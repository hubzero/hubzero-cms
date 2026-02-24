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
        if (!$dbName || !$version) {
            return false;
        }

        mb_internal_encoding('UTF-8');

        // com_dataviewer is a set of namespaced classes; the autoloader finds them
        \Components\Dataviewer\Site\DvConfig::init();

        $dd = \Components\Dataviewer\Site\Modes\ModeDsl::getDd(null, $dbName, $version);

        $dd['serverside'] = false;

        $sql    = \Components\Dataviewer\Site\Lib\Db::queryGen($dd);
        $result = \Components\Dataviewer\Site\Lib\Db::getResults($sql, $dd);

        ob_start();
        \Components\Dataviewer\Site\Filter\Csv::filter($result, $dd, true);
        $csv = ob_get_contents();
        ob_end_clean();

        if ($csv && $tmpFile) {
            $handle = fopen($tmpFile, 'w');
            if ($handle === false) {
                return false;
            }
            fwrite($handle, $csv);
            fclose($handle);

            return true;
        }

        return $csv;
    }
}
