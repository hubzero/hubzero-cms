<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to let zone locations hold IPv6 ranges
 *
 * zone_locations.ipFROM and ipTO were IPv4 addresses as unsigned integers,
 * so a zone could not be given an IPv6 range, and an IPv6 client never fell
 * in one. They become char(32) ascii_bin: the 32 lower-case hex digits of an
 * address's 16 bytes, an IPv4 address as IPv4-mapped IPv6 (::ffff:a.b.c.d).
 * Strings of one length sort as the numbers they are, so the lookup is still
 * ipFROM <= address <= ipTO. See Components\Tools\Helpers\Iprange.
 *
 * 0, which meant no range (a location by country or continent only), becomes
 * ''. A range that started at 0 with a real end keeps it, as 0.0.0.0.
 *
 * 0.0.0.0 to 255.255.255.255, a 0.0.0.0/0 catch-all, becomes every address,
 * IPv6 too, as /0 is when entered now.
 *
 * A single address entered with "IP to" left blank was stored with ipTO 0,
 * so it never matched. It becomes a range of that one address, which is
 * what was meant, and what saving such a location does now.
 *
 * The table is in the middleware database. Its failure to connect marks the
 * migration skipped, so it is tried again on the next run.
 **/
class Migration20261104170100ComTools extends Base
{
    /**
     * The new column definition
     *
     * @var  string
     **/
    const COLUMN = "char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT ''";

    /**
     * Up
     **/
    public function up()
    {
        try {
            $mwdb = $this->getMWDBO();
        } catch (\Throwable $e) {
            $mwdb = false;
        }

        if (!$mwdb) {
            $this->setError('Unable to connect to the middleware database, will try again on the next run.', 'skipped');
            return;
        }

        if (!$mwdb->tableExists('zone_locations')) {
            return;
        }

        $fields = $mwdb->getTableColumns('zone_locations', false);

        // Already converted
        if (!isset($fields['ipFROM'], $fields['ipTO']) || !preg_match('/int/i', $fields['ipFROM']->Type)) {
            return;
        }

        // New columns beside the old; a run that stopped after this is resumed
        $add = [];
        foreach (['ipFROM_hex', 'ipTO_hex'] as $column) {
            if (!isset($fields[$column])) {
                $add[] = 'ADD `' . $column . '` ' . self::COLUMN;
            }
        }
        if ($add) {
            $mwdb->setQuery('ALTER TABLE `zone_locations` ' . implode(', ', $add));
            $mwdb->query();
        }

        $mapped = "CONCAT('00000000000000000000ffff', LOWER(LPAD(HEX(%s), 8, '0')))";

        $everyone = "`ipFROM` = 0 AND `ipTO` = 4294967295";

        $mwdb->setQuery(
            "UPDATE `zone_locations` SET
				`ipFROM_hex` = CASE
					WHEN " . $everyone . " THEN REPEAT('0', 32)
					WHEN `ipFROM` = 0 AND `ipTO` = 0 THEN ''
					ELSE " . sprintf($mapped, '`ipFROM`') . " END,
				`ipTO_hex` = CASE
					WHEN " . $everyone . " THEN REPEAT('f', 32)
					WHEN `ipTO` = 0 THEN IF(`ipFROM` = 0, '', " . sprintf($mapped, '`ipFROM`') . ")
					ELSE " . sprintf($mapped, '`ipTO`') . " END",
        );
        $mwdb->query();

        // One statement, so the old columns go only as the new take their place
        $mwdb->setQuery(
            "ALTER TABLE `zone_locations`
				DROP `ipFROM`,
				DROP `ipTO`,
				CHANGE `ipFROM_hex` `ipFROM` " . self::COLUMN . " AFTER `zone_id`,
				CHANGE `ipTO_hex` `ipTO` " . self::COLUMN . " AFTER `ipFROM`",
        );
        $mwdb->query();

        $this->log('Converted zone_locations.ipFROM and ipTO to hex, for IPv6 ranges');
    }

    /**
     * Down
     *
     * IPv6 ranges have no integer form, so leave the columns as they are
     **/
    public function down()
    {
    }
}
