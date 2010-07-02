<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_HYDRODYNAMIC_DATA' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiHydrodynamicDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiHydrodynamicDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_HYDRODYNAMIC_DATA');
		$tMap->setPhpName('TsunamiHydrodynamicData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_HYDRDYNMC_DT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CONDITION_SEA', 'ConditionSea', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONDITION_SOURCE', 'ConditionSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONDITION_WEATHER', 'ConditionWeather', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONDITION_WIND', 'ConditionWind', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ECONDITION', 'Econdition', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FLOW', 'Flow', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FLOW_DIRECTION', 'FlowDirection', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FLOW_SOURCE', 'FlowSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FLOW_SPEED', 'FlowSpeed', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('INUNDATION', 'Inundation', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('INUNDATION_DIST', 'InundationDist', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('INUNDATION_QUALITY', 'InundationQuality', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('INUNDATION_SOURCE', 'InundationSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP', 'Runup', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_ADJ_METHOD', 'RunupAdjMethod', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_HEIGHT', 'RunupHeight', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_PORHEIGHT', 'RunupPoRHeight', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_PORLOC', 'RunupPoRLoc', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_QUALITY', 'RunupQuality', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_SOURCE', 'RunupSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('RUNUP_TIDAL_ADJ', 'RunupTidalAdj', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TIDEGAUGE', 'Tidegauge', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TIDEGAUGE_SOURCE', 'TidegaugeSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TIDEGAUGE_TYPE', 'TidegaugeType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addColumn('WAVE', 'Wave', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_ARRIVAL_TIMES', 'WaveArrivalTimes', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_FORM', 'WaveForm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_HEIGHT', 'WaveHeight', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_NUMBER', 'WaveNumber', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_PERIOD', 'WavePeriod', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_SOURCE', 'WaveSource', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('WAVE_TIME_TO_NORM', 'WaveTimeToNorm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('CONDITION_SEA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONDITION_SEA');

		$tMap->addValidator('CONDITION_SEA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONDITION_SEA');

		$tMap->addValidator('CONDITION_SEA', 'required', 'propel.validator.RequiredValidator', '', 'CONDITION_SEA');

		$tMap->addValidator('CONDITION_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONDITION_SOURCE');

		$tMap->addValidator('CONDITION_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONDITION_SOURCE');

		$tMap->addValidator('CONDITION_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'CONDITION_SOURCE');

		$tMap->addValidator('CONDITION_WEATHER', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONDITION_WEATHER');

		$tMap->addValidator('CONDITION_WEATHER', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONDITION_WEATHER');

		$tMap->addValidator('CONDITION_WEATHER', 'required', 'propel.validator.RequiredValidator', '', 'CONDITION_WEATHER');

		$tMap->addValidator('CONDITION_WIND', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONDITION_WIND');

		$tMap->addValidator('CONDITION_WIND', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONDITION_WIND');

		$tMap->addValidator('CONDITION_WIND', 'required', 'propel.validator.RequiredValidator', '', 'CONDITION_WIND');

		$tMap->addValidator('ECONDITION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ECONDITION');

		$tMap->addValidator('ECONDITION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ECONDITION');

		$tMap->addValidator('ECONDITION', 'required', 'propel.validator.RequiredValidator', '', 'ECONDITION');

		$tMap->addValidator('FLOW', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FLOW');

		$tMap->addValidator('FLOW', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FLOW');

		$tMap->addValidator('FLOW', 'required', 'propel.validator.RequiredValidator', '', 'FLOW');

		$tMap->addValidator('FLOW_DIRECTION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FLOW_DIRECTION');

		$tMap->addValidator('FLOW_DIRECTION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FLOW_DIRECTION');

		$tMap->addValidator('FLOW_DIRECTION', 'required', 'propel.validator.RequiredValidator', '', 'FLOW_DIRECTION');

		$tMap->addValidator('FLOW_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FLOW_SOURCE');

		$tMap->addValidator('FLOW_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FLOW_SOURCE');

		$tMap->addValidator('FLOW_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'FLOW_SOURCE');

		$tMap->addValidator('FLOW_SPEED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FLOW_SPEED');

		$tMap->addValidator('FLOW_SPEED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FLOW_SPEED');

		$tMap->addValidator('FLOW_SPEED', 'required', 'propel.validator.RequiredValidator', '', 'FLOW_SPEED');

		$tMap->addValidator('INUNDATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INUNDATION');

		$tMap->addValidator('INUNDATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INUNDATION');

		$tMap->addValidator('INUNDATION', 'required', 'propel.validator.RequiredValidator', '', 'INUNDATION');

		$tMap->addValidator('INUNDATION_DIST', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INUNDATION_DIST');

		$tMap->addValidator('INUNDATION_DIST', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INUNDATION_DIST');

		$tMap->addValidator('INUNDATION_DIST', 'required', 'propel.validator.RequiredValidator', '', 'INUNDATION_DIST');

		$tMap->addValidator('INUNDATION_QUALITY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INUNDATION_QUALITY');

		$tMap->addValidator('INUNDATION_QUALITY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INUNDATION_QUALITY');

		$tMap->addValidator('INUNDATION_QUALITY', 'required', 'propel.validator.RequiredValidator', '', 'INUNDATION_QUALITY');

		$tMap->addValidator('INUNDATION_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INUNDATION_SOURCE');

		$tMap->addValidator('INUNDATION_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INUNDATION_SOURCE');

		$tMap->addValidator('INUNDATION_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'INUNDATION_SOURCE');

		$tMap->addValidator('RUNUP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP');

		$tMap->addValidator('RUNUP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP');

		$tMap->addValidator('RUNUP', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP');

		$tMap->addValidator('RUNUP_ADJ_METHOD', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_ADJ_METHOD');

		$tMap->addValidator('RUNUP_ADJ_METHOD', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_ADJ_METHOD');

		$tMap->addValidator('RUNUP_ADJ_METHOD', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_ADJ_METHOD');

		$tMap->addValidator('RUNUP_HEIGHT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_HEIGHT');

		$tMap->addValidator('RUNUP_HEIGHT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_HEIGHT');

		$tMap->addValidator('RUNUP_HEIGHT', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_HEIGHT');

		$tMap->addValidator('RUNUP_PORHEIGHT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_PORHEIGHT');

		$tMap->addValidator('RUNUP_PORHEIGHT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_PORHEIGHT');

		$tMap->addValidator('RUNUP_PORHEIGHT', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_PORHEIGHT');

		$tMap->addValidator('RUNUP_PORLOC', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_PORLOC');

		$tMap->addValidator('RUNUP_PORLOC', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_PORLOC');

		$tMap->addValidator('RUNUP_PORLOC', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_PORLOC');

		$tMap->addValidator('RUNUP_QUALITY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_QUALITY');

		$tMap->addValidator('RUNUP_QUALITY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_QUALITY');

		$tMap->addValidator('RUNUP_QUALITY', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_QUALITY');

		$tMap->addValidator('RUNUP_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_SOURCE');

		$tMap->addValidator('RUNUP_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_SOURCE');

		$tMap->addValidator('RUNUP_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_SOURCE');

		$tMap->addValidator('RUNUP_TIDAL_ADJ', 'maxValue', 'propel.validator.MaxValueValidator', '', 'RUNUP_TIDAL_ADJ');

		$tMap->addValidator('RUNUP_TIDAL_ADJ', 'notMatch', 'propel.validator.NotMatchValidator', '', 'RUNUP_TIDAL_ADJ');

		$tMap->addValidator('RUNUP_TIDAL_ADJ', 'required', 'propel.validator.RequiredValidator', '', 'RUNUP_TIDAL_ADJ');

		$tMap->addValidator('TIDEGAUGE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TIDEGAUGE');

		$tMap->addValidator('TIDEGAUGE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TIDEGAUGE');

		$tMap->addValidator('TIDEGAUGE', 'required', 'propel.validator.RequiredValidator', '', 'TIDEGAUGE');

		$tMap->addValidator('TIDEGAUGE_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TIDEGAUGE_SOURCE');

		$tMap->addValidator('TIDEGAUGE_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TIDEGAUGE_SOURCE');

		$tMap->addValidator('TIDEGAUGE_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'TIDEGAUGE_SOURCE');

		$tMap->addValidator('TIDEGAUGE_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TIDEGAUGE_TYPE');

		$tMap->addValidator('TIDEGAUGE_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TIDEGAUGE_TYPE');

		$tMap->addValidator('TIDEGAUGE_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'TIDEGAUGE_TYPE');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_HYDRODYNAMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_HYDRODYNAMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_HYDRODYNAMIC_DATA_ID');

		$tMap->addValidator('TSUNAMI_HYDRODYNAMIC_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_HYDRODYNAMIC_DATA_ID');

		$tMap->addValidator('WAVE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE');

		$tMap->addValidator('WAVE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE');

		$tMap->addValidator('WAVE', 'required', 'propel.validator.RequiredValidator', '', 'WAVE');

		$tMap->addValidator('WAVE_ARRIVAL_TIMES', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_ARRIVAL_TIMES');

		$tMap->addValidator('WAVE_ARRIVAL_TIMES', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_ARRIVAL_TIMES');

		$tMap->addValidator('WAVE_ARRIVAL_TIMES', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_ARRIVAL_TIMES');

		$tMap->addValidator('WAVE_FORM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_FORM');

		$tMap->addValidator('WAVE_FORM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_FORM');

		$tMap->addValidator('WAVE_FORM', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_FORM');

		$tMap->addValidator('WAVE_HEIGHT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_HEIGHT');

		$tMap->addValidator('WAVE_HEIGHT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_HEIGHT');

		$tMap->addValidator('WAVE_HEIGHT', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_HEIGHT');

		$tMap->addValidator('WAVE_NUMBER', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_NUMBER');

		$tMap->addValidator('WAVE_NUMBER', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_NUMBER');

		$tMap->addValidator('WAVE_NUMBER', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_NUMBER');

		$tMap->addValidator('WAVE_PERIOD', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_PERIOD');

		$tMap->addValidator('WAVE_PERIOD', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_PERIOD');

		$tMap->addValidator('WAVE_PERIOD', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_PERIOD');

		$tMap->addValidator('WAVE_SOURCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_SOURCE');

		$tMap->addValidator('WAVE_SOURCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_SOURCE');

		$tMap->addValidator('WAVE_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_SOURCE');

		$tMap->addValidator('WAVE_TIME_TO_NORM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'WAVE_TIME_TO_NORM');

		$tMap->addValidator('WAVE_TIME_TO_NORM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'WAVE_TIME_TO_NORM');

		$tMap->addValidator('WAVE_TIME_TO_NORM', 'required', 'propel.validator.RequiredValidator', '', 'WAVE_TIME_TO_NORM');

	} // doBuild()

} // TsunamiHydrodynamicDataMapBuilder
