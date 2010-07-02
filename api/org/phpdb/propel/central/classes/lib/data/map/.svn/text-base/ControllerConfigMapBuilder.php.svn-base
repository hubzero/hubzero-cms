<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CONTROLLER_CONFIG' table to 'NEEScentral' DatabaseMap object.
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
class ControllerConfigMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ControllerConfigMapBuilder';

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

		$tMap = $this->dbMap->addTable('CONTROLLER_CONFIG');
		$tMap->setPhpName('ControllerConfig');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CONTROLLER_CONFIG_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('CONFIG_DATA_FILE_ID', 'ConfigDataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addForeignKey('INPUT_DATA_FILE_ID', 'InputDataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('PEAK_BASE_ACCELERATION', 'PeakBaseAcceleration', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('PEAK_BASE_ACCELERATION_UNIT_ID', 'PeakBaseAccelerationUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addForeignKey('TRIAL_ID', 'TrialId', 'double', CreoleTypes::NUMERIC, 'TRIAL', 'TRIALID', false, 22);

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INPUT_DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INPUT_DATA_FILE_ID');

		$tMap->addValidator('INPUT_DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INPUT_DATA_FILE_ID');

		$tMap->addValidator('INPUT_DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'INPUT_DATA_FILE_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PEAK_BASE_ACCELERATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PEAK_BASE_ACCELERATION');

		$tMap->addValidator('PEAK_BASE_ACCELERATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PEAK_BASE_ACCELERATION');

		$tMap->addValidator('PEAK_BASE_ACCELERATION', 'required', 'propel.validator.RequiredValidator', '', 'PEAK_BASE_ACCELERATION');

		$tMap->addValidator('PEAK_BASE_ACCELERATION_UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PEAK_BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('PEAK_BASE_ACCELERATION_UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PEAK_BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('PEAK_BASE_ACCELERATION_UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'PEAK_BASE_ACCELERATION_UNIT_ID');

		$tMap->addValidator('TRIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRIAL_ID');

	} // doBuild()

} // ControllerConfigMapBuilder
