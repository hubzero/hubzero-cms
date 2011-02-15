<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SITEREPORTS_QAR_RPS' table to 'NEEScentral' DatabaseMap object.
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
class SiteReportsQARRPSMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SiteReportsQARRPSMapBuilder';

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

		$tMap = $this->dbMap->addTable('SITEREPORTS_QAR_RPS');
		$tMap->setPhpName('SiteReportsQARRPS');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SITEREPORTS_QAR_RPS_SEQ');

		$tMap->addPrimaryKey('ID', 'ID', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('QAR_ID', 'QAR_ID', 'double', CreoleTypes::NUMERIC, 'SITEREPORTS_QAR', 'ID', false, 22);

		$tMap->addColumn('PROJECT', 'PROJECT', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PROJECT_WAREHOUSE_ID', 'PROJECT_WAREHOUSE_ID', 'string', CreoleTypes::VARCHAR, false, 1024);

		$tMap->addColumn('NEESR_SHARED_USE_YEAR', 'NEESR_SHARED_USE_YEAR', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('OFFICIAL_AWARD_NUMBER', 'OFFICIAL_AWARD_NUMBER', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PROJECT_TITLE', 'PROJECT_TITLE', 'string', CreoleTypes::VARCHAR, false, 1024);

		$tMap->addColumn('PROJECT_NUMBER', 'PROJECT_NUMBER', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PI_NAME', 'PI_NAME', 'string', CreoleTypes::VARCHAR, false, 1024);

		$tMap->addColumn('INSTITUTION', 'INSTITUTION', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('PPP_FY_START_PRG', 'PPP_FY_START_PRG', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PPP_FY_END_PRG', 'PPP_FY_END_PRG', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('APP_Q1', 'APP_Q1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('APP_Q2', 'APP_Q2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('APP_Q3', 'APP_Q3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('APP_Q4', 'APP_Q4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('Q1_NAR', 'Q1_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('Q2_NAR', 'Q2_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('Q3_NAR', 'Q3_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('Q4_NAR', 'Q4_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('PROJECT_WEIGHT', 'PROJECT_WEIGHT', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WEIGHTED_PROGRESS', 'WEIGHTED_PROGRESS', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CREATED_BY', 'CREATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('CREATED_ON', 'CREATED_ON', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('UPDATED_BY', 'UPDATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('UPDATED_ON', 'UPDATED_ON', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // SiteReportsQARRPSMapBuilder
