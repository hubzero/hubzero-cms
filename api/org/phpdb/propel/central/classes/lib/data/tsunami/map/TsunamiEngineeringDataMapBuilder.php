<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_ENGINEERING_DATA' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiEngineeringDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiEngineeringDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_ENGINEERING_DATA');
		$tMap->setPhpName('TsunamiEngineeringData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_NGNRNG_DT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_ENGINEERING_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('EVENT', 'Event', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EVENT_SENSOR_DATA', 'EventSensorData', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EVENT_VIDEO', 'EventVideo', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GEOTECH', 'Geotech', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GEOTECH_DAMAGE_DESCR', 'GeotechDamageDescr', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GEOTECH_SITE_CHAR', 'GeotechSiteChar', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GEOTECH_SOIL_CHAR', 'GeotechSoilChar', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GEOTECH_VUL_ASSESSMENT', 'GeotechVulAssessment', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM', 'Hm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM_EVAC_PLAN_MAPS', 'HmEvacPlanMaps', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM_FAULT_MAPS', 'HmFaultMaps', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM_HAZARD_ASSESSMENT', 'HmHazardAssessment', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM_HAZARD_MAPS', 'HmHazardMaps', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HM_SHELTER_LOCATIONS', 'HmShelterLocations', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE', 'Lifeline', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_DAMAGE_DESCR', 'LifelineDamageDescription', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_DESIGN', 'LifelineDesign', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_SEISMIC_DESIGN', 'LifelineSeismicDesign', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_TYPE', 'LifelineType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_VUL_ASSESSMENT', 'LifelineVulAssessment', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LIFELINE_YEAR', 'LifelineYear', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE', 'Structure', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_DAMAGE_DESCR', 'StructureDamageDescription', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_DESIGN', 'StructureDesign', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_SEISMIC_DESIGN', 'StructureSeismicDesign', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_TYPE', 'StructureType', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_VUL_ASSESSMENT', 'StructureVulAssessment', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRUCTURE_YEAR', 'StructureYear', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('EVENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EVENT');

		$tMap->addValidator('EVENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EVENT');

		$tMap->addValidator('EVENT', 'required', 'propel.validator.RequiredValidator', '', 'EVENT');

		$tMap->addValidator('EVENT_SENSOR_DATA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EVENT_SENSOR_DATA');

		$tMap->addValidator('EVENT_SENSOR_DATA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EVENT_SENSOR_DATA');

		$tMap->addValidator('EVENT_SENSOR_DATA', 'required', 'propel.validator.RequiredValidator', '', 'EVENT_SENSOR_DATA');

		$tMap->addValidator('EVENT_VIDEO', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EVENT_VIDEO');

		$tMap->addValidator('EVENT_VIDEO', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EVENT_VIDEO');

		$tMap->addValidator('EVENT_VIDEO', 'required', 'propel.validator.RequiredValidator', '', 'EVENT_VIDEO');

		$tMap->addValidator('GEOTECH', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GEOTECH');

		$tMap->addValidator('GEOTECH', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GEOTECH');

		$tMap->addValidator('GEOTECH', 'required', 'propel.validator.RequiredValidator', '', 'GEOTECH');

		$tMap->addValidator('GEOTECH_DAMAGE_DESCR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GEOTECH_DAMAGE_DESCR');

		$tMap->addValidator('GEOTECH_DAMAGE_DESCR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GEOTECH_DAMAGE_DESCR');

		$tMap->addValidator('GEOTECH_DAMAGE_DESCR', 'required', 'propel.validator.RequiredValidator', '', 'GEOTECH_DAMAGE_DESCR');

		$tMap->addValidator('GEOTECH_SITE_CHAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GEOTECH_SITE_CHAR');

		$tMap->addValidator('GEOTECH_SITE_CHAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GEOTECH_SITE_CHAR');

		$tMap->addValidator('GEOTECH_SITE_CHAR', 'required', 'propel.validator.RequiredValidator', '', 'GEOTECH_SITE_CHAR');

		$tMap->addValidator('GEOTECH_SOIL_CHAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GEOTECH_SOIL_CHAR');

		$tMap->addValidator('GEOTECH_SOIL_CHAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GEOTECH_SOIL_CHAR');

		$tMap->addValidator('GEOTECH_SOIL_CHAR', 'required', 'propel.validator.RequiredValidator', '', 'GEOTECH_SOIL_CHAR');

		$tMap->addValidator('GEOTECH_VUL_ASSESSMENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GEOTECH_VUL_ASSESSMENT');

		$tMap->addValidator('GEOTECH_VUL_ASSESSMENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GEOTECH_VUL_ASSESSMENT');

		$tMap->addValidator('GEOTECH_VUL_ASSESSMENT', 'required', 'propel.validator.RequiredValidator', '', 'GEOTECH_VUL_ASSESSMENT');

		$tMap->addValidator('HM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM');

		$tMap->addValidator('HM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM');

		$tMap->addValidator('HM', 'required', 'propel.validator.RequiredValidator', '', 'HM');

		$tMap->addValidator('HM_EVAC_PLAN_MAPS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM_EVAC_PLAN_MAPS');

		$tMap->addValidator('HM_EVAC_PLAN_MAPS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM_EVAC_PLAN_MAPS');

		$tMap->addValidator('HM_EVAC_PLAN_MAPS', 'required', 'propel.validator.RequiredValidator', '', 'HM_EVAC_PLAN_MAPS');

		$tMap->addValidator('HM_FAULT_MAPS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM_FAULT_MAPS');

		$tMap->addValidator('HM_FAULT_MAPS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM_FAULT_MAPS');

		$tMap->addValidator('HM_FAULT_MAPS', 'required', 'propel.validator.RequiredValidator', '', 'HM_FAULT_MAPS');

		$tMap->addValidator('HM_HAZARD_ASSESSMENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM_HAZARD_ASSESSMENT');

		$tMap->addValidator('HM_HAZARD_ASSESSMENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM_HAZARD_ASSESSMENT');

		$tMap->addValidator('HM_HAZARD_ASSESSMENT', 'required', 'propel.validator.RequiredValidator', '', 'HM_HAZARD_ASSESSMENT');

		$tMap->addValidator('HM_HAZARD_MAPS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM_HAZARD_MAPS');

		$tMap->addValidator('HM_HAZARD_MAPS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM_HAZARD_MAPS');

		$tMap->addValidator('HM_HAZARD_MAPS', 'required', 'propel.validator.RequiredValidator', '', 'HM_HAZARD_MAPS');

		$tMap->addValidator('HM_SHELTER_LOCATIONS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'HM_SHELTER_LOCATIONS');

		$tMap->addValidator('HM_SHELTER_LOCATIONS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'HM_SHELTER_LOCATIONS');

		$tMap->addValidator('HM_SHELTER_LOCATIONS', 'required', 'propel.validator.RequiredValidator', '', 'HM_SHELTER_LOCATIONS');

		$tMap->addValidator('LIFELINE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE');

		$tMap->addValidator('LIFELINE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE');

		$tMap->addValidator('LIFELINE', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE');

		$tMap->addValidator('LIFELINE_DAMAGE_DESCR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_DAMAGE_DESCR');

		$tMap->addValidator('LIFELINE_DAMAGE_DESCR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_DAMAGE_DESCR');

		$tMap->addValidator('LIFELINE_DAMAGE_DESCR', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_DAMAGE_DESCR');

		$tMap->addValidator('LIFELINE_DESIGN', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_DESIGN');

		$tMap->addValidator('LIFELINE_DESIGN', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_DESIGN');

		$tMap->addValidator('LIFELINE_DESIGN', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_DESIGN');

		$tMap->addValidator('LIFELINE_SEISMIC_DESIGN', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_SEISMIC_DESIGN');

		$tMap->addValidator('LIFELINE_SEISMIC_DESIGN', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_SEISMIC_DESIGN');

		$tMap->addValidator('LIFELINE_SEISMIC_DESIGN', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_SEISMIC_DESIGN');

		$tMap->addValidator('LIFELINE_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_TYPE');

		$tMap->addValidator('LIFELINE_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_TYPE');

		$tMap->addValidator('LIFELINE_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_TYPE');

		$tMap->addValidator('LIFELINE_VUL_ASSESSMENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_VUL_ASSESSMENT');

		$tMap->addValidator('LIFELINE_VUL_ASSESSMENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_VUL_ASSESSMENT');

		$tMap->addValidator('LIFELINE_VUL_ASSESSMENT', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_VUL_ASSESSMENT');

		$tMap->addValidator('LIFELINE_YEAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LIFELINE_YEAR');

		$tMap->addValidator('LIFELINE_YEAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LIFELINE_YEAR');

		$tMap->addValidator('LIFELINE_YEAR', 'required', 'propel.validator.RequiredValidator', '', 'LIFELINE_YEAR');

		$tMap->addValidator('STRUCTURE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE');

		$tMap->addValidator('STRUCTURE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE');

		$tMap->addValidator('STRUCTURE', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE');

		$tMap->addValidator('STRUCTURE_DAMAGE_DESCR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_DAMAGE_DESCR');

		$tMap->addValidator('STRUCTURE_DAMAGE_DESCR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_DAMAGE_DESCR');

		$tMap->addValidator('STRUCTURE_DAMAGE_DESCR', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_DAMAGE_DESCR');

		$tMap->addValidator('STRUCTURE_DESIGN', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_DESIGN');

		$tMap->addValidator('STRUCTURE_DESIGN', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_DESIGN');

		$tMap->addValidator('STRUCTURE_DESIGN', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_DESIGN');

		$tMap->addValidator('STRUCTURE_SEISMIC_DESIGN', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_SEISMIC_DESIGN');

		$tMap->addValidator('STRUCTURE_SEISMIC_DESIGN', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_SEISMIC_DESIGN');

		$tMap->addValidator('STRUCTURE_SEISMIC_DESIGN', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_SEISMIC_DESIGN');

		$tMap->addValidator('STRUCTURE_TYPE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_TYPE');

		$tMap->addValidator('STRUCTURE_TYPE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_TYPE');

		$tMap->addValidator('STRUCTURE_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_TYPE');

		$tMap->addValidator('STRUCTURE_VUL_ASSESSMENT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_VUL_ASSESSMENT');

		$tMap->addValidator('STRUCTURE_VUL_ASSESSMENT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_VUL_ASSESSMENT');

		$tMap->addValidator('STRUCTURE_VUL_ASSESSMENT', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_VUL_ASSESSMENT');

		$tMap->addValidator('STRUCTURE_YEAR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STRUCTURE_YEAR');

		$tMap->addValidator('STRUCTURE_YEAR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STRUCTURE_YEAR');

		$tMap->addValidator('STRUCTURE_YEAR', 'required', 'propel.validator.RequiredValidator', '', 'STRUCTURE_YEAR');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_ENGINEERING_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_ENGINEERING_DATA_ID');

		$tMap->addValidator('TSUNAMI_ENGINEERING_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_ENGINEERING_DATA_ID');

		$tMap->addValidator('TSUNAMI_ENGINEERING_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_ENGINEERING_DATA_ID');

		$tMap->addValidator('TSUNAMI_ENGINEERING_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_ENGINEERING_DATA_ID');

	} // doBuild()

} // TsunamiEngineeringDataMapBuilder
