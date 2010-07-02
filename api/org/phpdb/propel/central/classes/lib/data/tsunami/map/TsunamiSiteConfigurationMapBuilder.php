<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_SITE_CONFIGURATION' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiSiteConfigurationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiSiteConfigurationMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_SITE_CONFIGURATION');
		$tMap->setPhpName('TsunamiSiteConfiguration');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_ST_CNFGRTN_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_SITE_CONFIGURATION_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CONFIG_BATHYMETRY', 'ConfigBathymetry', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONFIG_DESCRIPTION', 'ConfigDescription', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONFIG_TOPOGRAPHY', 'ConfigTopography', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONFIG_VISUALS', 'ConfigVisuals', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('CONFIG_BATHYMETRY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_BATHYMETRY');

		$tMap->addValidator('CONFIG_BATHYMETRY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_BATHYMETRY');

		$tMap->addValidator('CONFIG_BATHYMETRY', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_BATHYMETRY');

		$tMap->addValidator('CONFIG_DESCRIPTION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_DESCRIPTION');

		$tMap->addValidator('CONFIG_DESCRIPTION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_DESCRIPTION');

		$tMap->addValidator('CONFIG_DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_DESCRIPTION');

		$tMap->addValidator('CONFIG_TOPOGRAPHY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_TOPOGRAPHY');

		$tMap->addValidator('CONFIG_TOPOGRAPHY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_TOPOGRAPHY');

		$tMap->addValidator('CONFIG_TOPOGRAPHY', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_TOPOGRAPHY');

		$tMap->addValidator('CONFIG_VISUALS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_VISUALS');

		$tMap->addValidator('CONFIG_VISUALS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_VISUALS');

		$tMap->addValidator('CONFIG_VISUALS', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_VISUALS');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_SITE_CONFIGURATION_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_SITE_CONFIGURATION_ID');

		$tMap->addValidator('TSUNAMI_SITE_CONFIGURATION_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_SITE_CONFIGURATION_ID');

		$tMap->addValidator('TSUNAMI_SITE_CONFIGURATION_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_SITE_CONFIGURATION_ID');

		$tMap->addValidator('TSUNAMI_SITE_CONFIGURATION_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_SITE_CONFIGURATION_ID');

	} // doBuild()

} // TsunamiSiteConfigurationMapBuilder
