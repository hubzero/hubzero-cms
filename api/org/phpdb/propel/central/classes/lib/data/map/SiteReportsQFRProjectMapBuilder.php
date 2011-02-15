<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SITEREPORTS_QFR_PROJECT' table to 'NEEScentral' DatabaseMap object.
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
class SiteReportsQFRProjectMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SiteReportsQFRProjectMapBuilder';

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

		$tMap = $this->dbMap->addTable('SITEREPORTS_QFR_PROJECT');
		$tMap->setPhpName('SiteReportsQFRProject');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SITEREPORTS_QFR_PROJECT_SEQ');

		$tMap->addPrimaryKey('ID', 'ID', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('QFR_ID', 'QFR_ID', 'double', CreoleTypes::NUMERIC, 'SITEREPORTS_QFR', 'ID', false, 22);

		$tMap->addColumn('PI', 'PI', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PROJECT_ID', 'PROJECT_ID', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PROJECT_NUMBER', 'PROJECT_NUMBER', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('P_COST', 'P_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('E_COST', 'E_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PSC_COST', 'PSC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ODC_COST', 'ODC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IC_COST', 'IC_COST', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CREATED_BY', 'CREATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('CREATED_ON', 'CREATED_ON', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('UPDATED_BY', 'UPDATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('UPDATED_ON', 'UPDATED_ON', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // SiteReportsQFRProjectMapBuilder
