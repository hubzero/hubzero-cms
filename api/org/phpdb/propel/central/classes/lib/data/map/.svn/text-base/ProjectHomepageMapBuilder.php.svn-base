<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PROJECT_HOMEPAGE' table to 'NEEScentral' DatabaseMap object.
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
class ProjectHomepageMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ProjectHomepageMapBuilder';

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

		$tMap = $this->dbMap->addTable('PROJECT_HOMEPAGE');
		$tMap->setPhpName('ProjectHomepage');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('PROJECT_HOMEPAGE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('PROJECT_ID', 'ProjectId', 'double', CreoleTypes::NUMERIC, 'PROJECT', 'PROJID', true, 22);

		$tMap->addForeignKey('DATA_FILE_ID', 'DataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', true, 22);

		$tMap->addColumn('CAPTION', 'Caption', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1023);

		$tMap->addColumn('URL', 'Url', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('PROJECT_HOMEPAGE_TYPE_ID', 'ProjectHomepageTypeId', 'double', CreoleTypes::NUMERIC, true, 22);

	} // doBuild()

} // ProjectHomepageMapBuilder
