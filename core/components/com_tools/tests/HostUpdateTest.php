<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tests;

use Components\Tools\Tables\Host;
use Components\Tools\Tables\Hosttype;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/tables/host.php';
require_once dirname(__DIR__) . '/tables/hosttype.php';
require_once __DIR__ . '/RecordingDriver.php';

/**
 * Host and host type tables write their own UPDATE statements; those name
 * the table's columns and nothing the table keeps for itself.
 */
class HostUpdateTest extends TestCase
{
    /**
     * @return  void
     */
    public function testHostTypeUpdateNamesOnlyColumns(): void
    {
        $db = new RecordingDriver(['name', 'value', 'description']);
        $type = new Hosttype($db);
        $type->name = 'pbs';
        $type->value = 4;
        $type->description = 'Batch';

        $type->update();

        $this->assertSame(
            "UPDATE `hosttype` SET `name`='pbs',`value`='4',`description`='Batch' WHERE `name`='pbs'",
            end($db->queries),
        );
    }

    /**
     * @return  void
     */
    public function testHostStoreNamesOnlyColumns(): void
    {
        $db = new RecordingDriver(['hostname', 'provisions', 'status']);
        $host = new Host($db);
        $host->hostname = 'exec1';
        $host->provisions = 3;
        $host->status = 'up';

        $host->store();

        $this->assertSame(
            "UPDATE host SET `hostname`='exec1',`provisions`='3',`status`='up' WHERE hostname='exec1'",
            end($db->queries),
        );
    }
}
