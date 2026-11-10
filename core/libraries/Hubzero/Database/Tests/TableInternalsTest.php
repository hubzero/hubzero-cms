<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;

/**
 * A table's columns are its data; the table name, key, connection and the
 * rest of its own state are not, and nothing bound to it may replace them.
 */
class TableInternalsTest extends TestCase
{
    /**
     * @return  object
     */
    private function driver()
    {
        return new class {
            public function getTableColumns($table, $typeOnly = true)
            {
                return [
                    'id'    => (object) ['Field' => 'id', 'Default' => null],
                    'title' => (object) ['Field' => 'title', 'Default' => ''],
                ];
            }
        };
    }

    /**
     * @return  void
     */
    public function testBindingCannotReplaceTheTablesOwnState(): void
    {
        $db    = $this->driver();
        $table = new ThingTable($db);

        $table->bind([
            'id'       => 5,
            'title'    => 'A thing',
            '_tbl'     => '#__elsewhere',
            '_tbl_key' => 'title',
            '_db'      => null,
        ]);

        $this->assertSame(5, $table->id);
        $this->assertSame('A thing', $table->title);
        $this->assertSame('#__things', $table->getTableName());
        $this->assertSame('id', $table->getKeyName());
        $this->assertSame($db, $table->getDbo());
    }

    /**
     * @return  void
     */
    public function testXmlHoldsOnlyTheData(): void
    {
        $table = new ThingTable($this->driver());
        $table->bind(['id' => 5, 'title' => 'A thing']);

        $this->assertSame(
            "<record table=\"#__things\">\n\t<id><![CDATA[5]]></id>\n\t<title><![CDATA[A thing]]></title>\n</record>",
            $table->toXML(),
        );
    }
}
