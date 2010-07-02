<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'NAWI_FACILITY' table to 'NEEScentral' DatabaseMap object.
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
class NAWIFacilityMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.NAWIFacilityMapBuilder';

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

		$tMap = $this->dbMap->addTable('NAWI_FACILITY');
		$tMap->setPhpName('NAWIFacility');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('NAWI_FACILITY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('FACILITYID', 'FacilityId', 'double', CreoleTypes::NUMERIC, 'ORGANIZATION', 'ORGID', false, 22);

		$tMap->addForeignKey('NAWIID', 'NawiId', 'double', CreoleTypes::NUMERIC, 'NAWI', 'NAWIID', false, 22);

		$tMap->addValidator('FACILITYID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FACILITYID');

		$tMap->addValidator('FACILITYID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FACILITYID');

		$tMap->addValidator('FACILITYID', 'required', 'propel.validator.RequiredValidator', '', 'FACILITYID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAWIID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'NAWIID');

		$tMap->addValidator('NAWIID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'NAWIID');

		$tMap->addValidator('NAWIID', 'required', 'propel.validator.RequiredValidator', '', 'NAWIID');

	} // doBuild()

} // NAWIFacilityMapBuilder
