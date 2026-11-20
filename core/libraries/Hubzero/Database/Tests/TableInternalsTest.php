<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * A driver that answers only for the columns, without connecting
     *
     * @return  \Hubzero\Database\Drivers\Sqlite\SqliteDriver
     */
    private function driver()
    {
        return new class extends \Hubzero\Database\Drivers\Sqlite\SqliteDriver {
            /**
             * Nothing to connect to
             */
            public function __construct() {}

            /**
             * @param   string  $table
             * @param   bool  $typeOnly
             * @return  array<mixed>
             */
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
    public function testPropertiesAreTheColumnsAndAddedData(): void
    {
        $table = new ThingTable($this->driver());

        $this->assertSame(['id', 'title'], array_keys($table->getProperties()));

        $table->extra = 'added';

        $this->assertSame(['id', 'title', 'extra'], array_keys($table->getProperties()));
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
            'tableName'  => '#__elsewhere',
            'tableKey'   => 'title',
            'db'         => null,
            'fieldCache' => [],
        ]);

        $this->assertSame(5, $table->id);
        $this->assertSame('A thing', $table->title);
        $this->assertSame('#__things', $table->getTableName());
        $this->assertSame('id', $table->getKeyName());
        $this->assertSame($db, $table->getDbo());
        $this->assertSame(['id', 'title'], array_keys($table->getFields()));
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

    public function testAnInsertGivesRequiredTextWithNoDefaultAnEmptyValue(): void
    {
        $db = new class extends \Hubzero\Database\Drivers\Sqlite\SqliteDriver {
            public function __construct() {}

            public function getTableColumns($table, $typeOnly = true)
            {
                $column = static fn ($name, $type, $null, $default, $key = '') => (object) [
                    'Field'   => $name,
                    'Type'    => $type,
                    'Null'    => $null,
                    'Default' => $default,
                    'Key'     => $key,
                    'Extra'   => '',
                ];

                return [
                    'id'          => $column('id', 'int(11)', 'NO', null, 'PRI'),
                    'description' => $column('description', 'longtext', 'NO', null),
                    'alias'       => $column('alias', 'varchar(250)', 'NO', null),
                    'title'       => $column('title', 'varchar(255)', 'NO', ''),
                    'notes'       => $column('notes', 'text', 'YES', null),
                    'ordering'    => $column('ordering', 'int(11)', 'NO', null),
                ];
            }
        };
        $table = new class ($db) extends ThingTable {
            public function fill(): void
            {
                $this->fillRequiredText();
            }
        };
        $table->alias = 'given';

        $table->fill();

        $this->assertSame('', $table->description);
        $this->assertSame('given', $table->alias);
        $this->assertNull($table->title, 'a column with a default is left to it');
        $this->assertNull($table->notes, 'a nullable column stays null');
        $this->assertNull($table->ordering, 'only text columns are filled');
        $this->assertNull($table->id);
    }
}
