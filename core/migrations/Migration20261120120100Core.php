<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Name Purdue University in the copyright extensions record of themselves
 *
 * UC San Diego transferred its copyrights back to Purdue, and every header in
 * core names Purdue University now, with 2026 as the final year. Each
 * extension's #__extensions.manifest_cache keeps the copyright its manifest
 * had when it was installed or last refreshed, so a hub would go on showing
 * The Regents of the University of California (or HUBzero Foundation, LLC)
 * until then. Only the "copyright" value is rewritten, as the headers were:
 * the first year is kept and the range ends in 2026.
 *
 * There is no down(): the old holders no longer hold the copyright.
 **/
class Migration20261120120100Core extends Base
{
    /**
     * Holders that became Purdue University
     *
     * @var  string
     */
    private const HOLDERS = 'The Regents of the University of California|The Regents of Purdue University'
        . '|HUBzero Foundation, LLC|Purdue University';

    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__extensions')) {
            return;
        }

        $query = $this->db->getQuery()
            ->select('extension_id')
            ->select('manifest_cache')
            ->from('#__extensions')
            ->whereLike('manifest_cache', 'Regents of', 1)
            ->orWhereLike('manifest_cache', 'HUBzero Foundation', 1)
            ->resetDepth();
        $this->db->setQuery($query->toString());
        $rows = $this->db->loadObjectList() ?: [];

        foreach ($rows as $row) {
            $cache = $this->purdue((string) $row->manifest_cache);
            if ($cache === $row->manifest_cache) {
                continue;
            }

            $update = $this->db->getQuery()
                ->update('#__extensions')
                ->set(['manifest_cache' => $cache])
                ->whereEquals('extension_id', (int) $row->extension_id);
            $this->db->setQuery($update->toString());
            $this->db->query();
        }
    }

    /**
     * Rewrite the "copyright" value of a manifest cache (JSON)
     *
     * @param   string  $cache
     * @return  string
     */
    private function purdue($cache)
    {
        return (string) preg_replace_callback(
            '/"copyright"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/',
            function ($m) {
                $value = json_decode('"' . $m[1] . '"');
                if (!is_string($value)) {
                    return $m[0];
                }

                $value = (string) preg_replace_callback(
                    '/Copyright (?:(?:\(c\)|\(C\)|©|&copy;) ?)?(\d{4})(?:\s*-\s*(\d{4}))? (?:' . self::HOLDERS . ')'
                        . '\.?(?: All [Rr]ights [Rr]eserved\.)?/u',
                    function ($c) {
                        $years = $c[1] === '2026' ? '2026' : $c[1] . '-2026';

                        return 'Copyright © ' . $years . ' Purdue University. All Rights Reserved.';
                    },
                    $value,
                );

                return '"copyright":' . json_encode($value, JSON_UNESCAPED_SLASHES);
            },
            $cache,
        );
    }
}
