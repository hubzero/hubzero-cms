<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PROJECT_GRANT' table to 'NEEScentral' DatabaseMap object.
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
class ProjectGrantMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ProjectGrantMapBuilder';

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

		$tMap = $this->dbMap->addTable('PROJECT_GRANT');
		$tMap->setPhpName('ProjectGrant');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('PROJECT_GRANT_SEQ');

		$tMap->addForeignKey('PROJID', 'ProjectId', 'double', CreoleTypes::NUMERIC, 'PROJECT', 'PROJID', true, 22);

		$tMap->addColumn('FUND_ORG', 'FundingOrg', 'string', CreoleTypes::VARCHAR, true, 128);

		$tMap->addColumn('AWARD_NUM', 'AwardNumber', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('AWARD_URL', 'AwardUrl', 'string', CreoleTypes::VARCHAR, true, 512);

		$tMap->addForeignKey('NEES_AWARD_TYPE_ID', 'NeesAwardTypeId', 'double', CreoleTypes::NUMERIC, 'NEES_AWARD_TYPE', 'ID', true, 22);

	} // doBuild()

} // ProjectGrantMapBuilder
