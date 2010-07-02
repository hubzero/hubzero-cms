<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_SITE' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.tsunami.map
 */
class TsunamiSiteMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiSiteMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_SITE');
		$tMap->setPhpName('TsunamiSite');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSUNAMI_SITE_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_SITE_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('BOUNDING_POLYGON', 'BoundingPolygon', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('COUNTRY', 'Country', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SITE_LAT', 'SiteLatitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('SITE_LON', 'SiteLongitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('TSUNAMI_PROJECT_ID', 'TsunamiProjectId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_PROJECT', 'TSUNAMI_PROJECT_ID', false, 22);

		$tMap->addColumn('TYPE', 'Type', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addValidator('BOUNDING_POLYGON', 'required', 'propel.validator.RequiredValidator', '', 'BOUNDING_POLYGON');

		$tMap->addValidator('COUNTRY', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COUNTRY');

		$tMap->addValidator('COUNTRY', 'required', 'propel.validator.RequiredValidator', '', 'COUNTRY');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SITE_LAT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SITE_LAT');

		$tMap->addValidator('SITE_LAT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SITE_LAT');

		$tMap->addValidator('SITE_LAT', 'required', 'propel.validator.RequiredValidator', '', 'SITE_LAT');

		$tMap->addValidator('SITE_LON', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SITE_LON');

		$tMap->addValidator('SITE_LON', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SITE_LON');

		$tMap->addValidator('SITE_LON', 'required', 'propel.validator.RequiredValidator', '', 'SITE_LON');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'TYPE');

		$tMap->addValidator('TYPE', 'required', 'propel.validator.RequiredValidator', '', 'TYPE');

	} // doBuild()

} // TsunamiSiteMapBuilder
