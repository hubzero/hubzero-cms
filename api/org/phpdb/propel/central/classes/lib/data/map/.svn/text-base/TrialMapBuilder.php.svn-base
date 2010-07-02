<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TRIAL' table to 'NEEScentral' DatabaseMap object.
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
class TrialMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.TrialMapBuilder';

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

		$tMap = $this->dbMap->addTable('TRIAL');
		$tMap->setPhpName('Trial');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TRIAL_SEQ');

		$tMap->addPrimaryKey('TRIALID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ACCELERATION', 'Acceleration', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('BASE_ACCELERATION', 'BaseAcceleration', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('BASE_ACCELERATION_UNIT_ID', 'BaseAccelerationUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('COMPONENT', 'Component', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CURATION_STATUS', 'CurationStatus', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('END_DATE', 'EndDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addForeignKey('MOTION_FILE_ID', 'MotionFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('MOTION_NAME', 'MotionName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('OBJECTIVE', 'Objective', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('START_DATE', 'StartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('STATION', 'Station', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, true, 1020);

		$tMap->addColumn('TRIAL_TYPE_ID', 'TrialTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('ACCELERATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ACCELERATION');

		$tMap->addValidator('ACCELERATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ACCELERATION');

		$tMap->addValidator('ACCELERATION', 'required', 'propel.validator.RequiredValidator', '', 'ACCELERATION');

		$tMap->addValidator('BASE_ACCELERATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BASE_ACCELERATION');

		$tMap->addValidator('BASE_ACCELERATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BASE_ACCELERATION');

		$tMap->addValidator('BASE_ACCELERATION', 'required', 'propel.validator.RequiredValidator', '', 'BASE_ACCELERATION');

		$tMap->addValidator('BASE_ACCELERATION_UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('BASE_ACCELERATION_UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('BASE_ACCELERATION_UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('COMPONENT', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COMPONENT');

		$tMap->addValidator('COMPONENT', 'required', 'propel.validator.RequiredValidator', '', 'COMPONENT');

		$tMap->addValidator('CURATION_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CURATION_STATUS');

		$tMap->addValidator('CURATION_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'CURATION_STATUS');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('END_DATE', 'required', 'propel.validator.RequiredValidator', '', 'END_DATE');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('MOTION_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MOTION_FILE_ID');

		$tMap->addValidator('MOTION_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MOTION_FILE_ID');

		$tMap->addValidator('MOTION_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'MOTION_FILE_ID');

		$tMap->addValidator('MOTION_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'MOTION_NAME');

		$tMap->addValidator('MOTION_NAME', 'required', 'propel.validator.RequiredValidator', '', 'MOTION_NAME');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('OBJECTIVE', 'required', 'propel.validator.RequiredValidator', '', 'OBJECTIVE');

		$tMap->addValidator('START_DATE', 'required', 'propel.validator.RequiredValidator', '', 'START_DATE');

		$tMap->addValidator('STATION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'STATION');

		$tMap->addValidator('STATION', 'required', 'propel.validator.RequiredValidator', '', 'STATION');

		$tMap->addValidator('STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'STATUS');

		$tMap->addValidator('STATUS', 'required', 'propel.validator.RequiredValidator', '', 'STATUS');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

		$tMap->addValidator('TRIALID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'required', 'propel.validator.RequiredValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'unique', 'propel.validator.UniqueValidator', '', 'TRIALID');

		$tMap->addValidator('TRIAL_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIAL_TYPE_ID');

		$tMap->addValidator('TRIAL_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIAL_TYPE_ID');

		$tMap->addValidator('TRIAL_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRIAL_TYPE_ID');

	} // doBuild()

} // TrialMapBuilder
