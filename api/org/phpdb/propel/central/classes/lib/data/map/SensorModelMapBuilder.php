<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SENSOR_MODEL' table to 'NEEScentral' DatabaseMap object.
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
class SensorModelMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SensorModelMapBuilder';

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

		$tMap = $this->dbMap->addTable('SENSOR_MODEL');
		$tMap->setPhpName('SensorModel');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SENSOR_MODEL_SEQ');

		$tMap->addPrimaryKey('SENSOR_MODEL_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('MANUFACTURER', 'Manufacturer', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('MAX_MEASURED_VALUE', 'MaxMeasuredValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('MAX_OP_TEMP', 'MaxOpTemp', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('MEASURED_VALUE_UNITS_ID', 'MeasuredValueUnitsId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('MIN_MEASURED_VALUE', 'MinMeasuredValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('MIN_OP_TEMP', 'MinOpTemp', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MODEL', 'Model', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('NOTE', 'Note', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SENSITIVITY', 'Sensitivity', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('SENSITIVITY_UNITS_ID', 'SensitivityUnitsId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addForeignKey('SENSOR_TYPE_ID', 'SensorTypeId', 'double', CreoleTypes::NUMERIC, 'SENSOR_TYPE', 'ID', false, 22);

		$tMap->addColumn('SIGNAL_TYPE', 'SignalType', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addForeignKey('TEMP_UNITS_ID', 'TempUnitsId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('MANUFACTURER', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'MANUFACTURER');

		$tMap->addValidator('MANUFACTURER', 'required', 'propel.validator.RequiredValidator', '', 'MANUFACTURER');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MAX_OP_TEMP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAX_OP_TEMP');

		$tMap->addValidator('MAX_OP_TEMP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAX_OP_TEMP');

		$tMap->addValidator('MAX_OP_TEMP', 'required', 'propel.validator.RequiredValidator', '', 'MAX_OP_TEMP');

		$tMap->addValidator('MEASURED_VALUE_UNITS_ID', 'required', 'propel.validator.RequiredValidator', '', 'MEASURED_VALUE_UNITS_ID');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('MIN_OP_TEMP', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIN_OP_TEMP');

		$tMap->addValidator('MIN_OP_TEMP', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIN_OP_TEMP');

		$tMap->addValidator('MIN_OP_TEMP', 'required', 'propel.validator.RequiredValidator', '', 'MIN_OP_TEMP');

		$tMap->addValidator('MODEL', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'MODEL');

		$tMap->addValidator('MODEL', 'required', 'propel.validator.RequiredValidator', '', 'MODEL');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('NOTE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NOTE');

		$tMap->addValidator('NOTE', 'required', 'propel.validator.RequiredValidator', '', 'NOTE');

		$tMap->addValidator('SENSITIVITY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY', 'required', 'propel.validator.RequiredValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY_UNITS_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSITIVITY_UNITS_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'unique', 'propel.validator.UniqueValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_TYPE_ID');

		$tMap->addValidator('SENSOR_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_TYPE_ID');

		$tMap->addValidator('SENSOR_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_TYPE_ID');

		$tMap->addValidator('SIGNAL_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'SIGNAL_TYPE');

		$tMap->addValidator('SIGNAL_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'SIGNAL_TYPE');

		$tMap->addValidator('TEMP_UNITS_ID', 'required', 'propel.validator.RequiredValidator', '', 'TEMP_UNITS_ID');

	} // doBuild()

} // SensorModelMapBuilder
