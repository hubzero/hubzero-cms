<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SPONSOR' table to 'NEEScentral' DatabaseMap object.
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
class SponsorMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SponsorMapBuilder';

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

		$tMap = $this->dbMap->addTable('SPONSOR');
		$tMap->setPhpName('Sponsor');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SPONSOR_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DISPLAY_NAME', 'DisplayName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('SYSTEM_NAME', 'SystemName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('ADDRESS', 'Address', 'string', CreoleTypes::VARCHAR, false, 500);

		$tMap->addColumn('PHONE', 'Phone', 'string', CreoleTypes::VARCHAR, false, 14);

		$tMap->addColumn('CONTACT_PERSON', 'ContactPerson', 'string', CreoleTypes::VARCHAR, false, 50);

		$tMap->addColumn('URL', 'Url', 'string', CreoleTypes::VARCHAR, false, 500);

	} // doBuild()

} // SponsorMapBuilder
