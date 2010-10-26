<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DATA_FILE_ARCHIVE_LIST' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.map
 */
class DataFileArchiveListMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DataFileArchiveListMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('NEEScentral');

		$tMap = $this->dbMap->addTable('DATA_FILE_ARCHIVE_LIST');
		$tMap->setPhpName('DataFileArchiveList');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DATA_FILE_ARCHIVE_LIST_SEQ');

		$tMap->addColumn('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('PROCESSED', 'Processed', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('PROCESSED_DATE', 'ProcessedDate', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // DataFileArchiveListMapBuilder
