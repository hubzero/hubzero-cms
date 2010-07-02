<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_SEISMIC_DATA' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiSeismicDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiSeismicDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_SEISMIC_DATA');
		$tMap->setPhpName('TsunamiSeismicData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSUNAMI_SEISMIC_DATA_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_SEISMIC_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('LOCAL', 'Local', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LOCAL_DATA_SOURCES', 'LocalDataSources', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LOCAL_TYPE', 'LocalType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MEASURES', 'Measures', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MEASURES_SITE_CONFIG', 'MeasuresSiteConfig', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MEASURES_TYPES', 'MeasuresTypes', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MIA', 'Mia', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MIA_SOURCE', 'MiaSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('LOCAL', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LOCAL');

		$tMap->addValidator('LOCAL', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LOCAL');

		$tMap->addValidator('LOCAL', 'required', 'propel.validator.RequiredValidator', '', 'LOCAL');

		$tMap->addValidator('LOCAL_DATA_SOURCES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LOCAL_DATA_SOURCES');

		$tMap->addValidator('LOCAL_DATA_SOURCES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LOCAL_DATA_SOURCES');

		$tMap->addValidator('LOCAL_DATA_SOURCES', 'required', 'propel.validator.RequiredValidator', '', 'LOCAL_DATA_SOURCES');

		$tMap->addValidator('LOCAL_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LOCAL_TYPE');

		$tMap->addValidator('LOCAL_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LOCAL_TYPE');

		$tMap->addValidator('LOCAL_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'LOCAL_TYPE');

		$tMap->addValidator('MEASURES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MEASURES');

		$tMap->addValidator('MEASURES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MEASURES');

		$tMap->addValidator('MEASURES', 'required', 'propel.validator.RequiredValidator', '', 'MEASURES');

		$tMap->addValidator('MEASURES_SITE_CONFIG', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MEASURES_SITE_CONFIG');

		$tMap->addValidator('MEASURES_SITE_CONFIG', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MEASURES_SITE_CONFIG');

		$tMap->addValidator('MEASURES_SITE_CONFIG', 'required', 'propel.validator.RequiredValidator', '', 'MEASURES_SITE_CONFIG');

		$tMap->addValidator('MEASURES_TYPES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MEASURES_TYPES');

		$tMap->addValidator('MEASURES_TYPES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MEASURES_TYPES');

		$tMap->addValidator('MEASURES_TYPES', 'required', 'propel.validator.RequiredValidator', '', 'MEASURES_TYPES');

		$tMap->addValidator('MIA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIA');

		$tMap->addValidator('MIA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIA');

		$tMap->addValidator('MIA', 'required', 'propel.validator.RequiredValidator', '', 'MIA');

		$tMap->addValidator('MIA_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIA_SOURCE');

		$tMap->addValidator('MIA_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIA_SOURCE');

		$tMap->addValidator('MIA_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'MIA_SOURCE');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_SEISMIC_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_SEISMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_SEISMIC_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_SEISMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_SEISMIC_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_SEISMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_SEISMIC_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_SEISMIC_DATA_ID');

	} // doBuild()

} // TsunamiSeismicDataMapBuilder
