<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Database;

/**
 * Loads test data from flat XML format
 *
 * Flat XML format uses element names as table names and attributes as columns:
 *   <dataset>
 *     <tablename col1="val1" col2="val2" />
 *     <tablename col1="val3" col2="val4" />
 *   </dataset>
 */
class FlatXmlDataSet extends DataSet
{
    /**
     * Load a dataset from a flat XML file
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
        $tables = [];

        // Each child element of dataset is a row, element name is the table name
        foreach ($xml->children() as $element) {
            $tableName = $element->getName();

            // Initialize table if we haven't seen it
            if (!isset($tables[$tableName])) {
                $tables[$tableName] = [
                    'columns' => [],
                    'rows' => []
                ];
            }

            // Extract attributes as column values
            $row = [];
            foreach ($element->attributes() as $name => $value) {
                $columnName = (string) $name;
                $row[$columnName] = (string) $value;

                // Track columns
                if (!in_array($columnName, $tables[$tableName]['columns'])) {
                    $tables[$tableName]['columns'][] = $columnName;
                }
            }

            if (!empty($row)) {
                $tables[$tableName]['rows'][] = $row;
            }
        }

        // Create Table objects
        foreach ($tables as $tableName => $data) {
            $table = new Table($tableName, $data['columns'], $data['rows']);
            $dataset->addTable($table);
        }

        return $dataset;
    }
}
