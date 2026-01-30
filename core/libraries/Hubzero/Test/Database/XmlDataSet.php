<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Database;

/**
 * Loads test data from standard XML format
 *
 * Standard XML format uses:
 *   <dataset>
 *     <table name="tablename">
 *       <column>col1</column>
 *       <column>col2</column>
 *       <row>
 *         <value>val1</value>
 *         <value>val2</value>
 *       </row>
 *     </table>
 *   </dataset>
 */
class XmlDataSet extends DataSet
{
    /**
     * Load a dataset from an XML file
     *
     * @param  string  $xmlFile  Path to the XML file
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function load(string $xmlFile): static
    {
        if (!file_exists($xmlFile)) {
            throw new \InvalidArgumentException("XML file not found: {$xmlFile}");
        }

        $xml = simplexml_load_file($xmlFile);

        if ($xml === false) {
            throw new \InvalidArgumentException("Failed to parse XML file: {$xmlFile}");
        }

        $dataset = new static();

        foreach ($xml->table as $tableElement) {
            $tableName = (string) $tableElement['name'];
            $columns = [];
            $rows = [];

            // Extract column names
            foreach ($tableElement->column as $column) {
                $columns[] = (string) $column;
            }

            // Extract row data
            foreach ($tableElement->row as $rowElement) {
                $row = [];
                $valueIndex = 0;

                foreach ($rowElement->value as $value) {
                    if (isset($columns[$valueIndex])) {
                        $row[$columns[$valueIndex]] = (string) $value;
                    }
                    $valueIndex++;
                }

                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $table = new Table($tableName, $columns, $rows);
            $dataset->addTable($table);
        }

        return $dataset;
    }
}
