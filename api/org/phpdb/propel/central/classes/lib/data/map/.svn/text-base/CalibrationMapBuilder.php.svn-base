<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CALIBRATION' table to 'NEEScentral' DatabaseMap object.
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
class CalibrationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.CalibrationMapBuilder';

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

		$tMap = $this->dbMap->addTable('CALIBRATION');
		$tMap->setPhpName('Calibration');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CALIBRATION_SEQ');

		$tMap->addPrimaryKey('CALIB_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ADJUSTMENTS', 'Adjustments', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('CALIB_DATE', 'CalibDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('CALIB_FACTOR', 'CalibFactor', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('CALIB_FACTOR_UNITS', 'CalibFactorUnits', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('CALIBRATOR', 'Calibrator', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('MAX_MEASURED_VALUE', 'MaxMeasuredValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('MEASURED_VALUE_UNITS', 'MeasuredValueUnits', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('MIN_MEASURED_VALUE', 'MinMeasuredValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('REFERENCE', 'Reference', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('REFERENCE_UNITS', 'ReferenceUnits', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('SENSITIVITY', 'Sensitivity', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('SENSITIVITY_UNITS', 'SensitivityUnits', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addForeignKey('SENSOR_ID', 'SensorId', 'double', CreoleTypes::NUMERIC, 'SENSOR', 'SENSOR_ID', false, 22);

		$tMap->addValidator('ADJUSTMENTS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADJUSTMENTS');

		$tMap->addValidator('ADJUSTMENTS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADJUSTMENTS');

		$tMap->addValidator('ADJUSTMENTS', 'required', 'propel.validator.RequiredValidator', '', 'ADJUSTMENTS');

		$tMap->addValidator('CALIBRATOR', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'CALIBRATOR');

		$tMap->addValidator('CALIBRATOR', 'required', 'propel.validator.RequiredValidator', '', 'CALIBRATOR');

		$tMap->addValidator('CALIB_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CALIB_DATE');

		$tMap->addValidator('CALIB_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CALIB_DATE');

		$tMap->addValidator('CALIB_FACTOR', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'CALIB_FACTOR');

		$tMap->addValidator('CALIB_FACTOR', 'required', 'propel.validator.RequiredValidator', '', 'CALIB_FACTOR');

		$tMap->addValidator('CALIB_FACTOR_UNITS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CALIB_FACTOR_UNITS');

		$tMap->addValidator('CALIB_FACTOR_UNITS', 'required', 'propel.validator.RequiredValidator', '', 'CALIB_FACTOR_UNITS');

		$tMap->addValidator('CALIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CALIB_ID');

		$tMap->addValidator('CALIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CALIB_ID');

		$tMap->addValidator('CALIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'CALIB_ID');

		$tMap->addValidator('CALIB_ID', 'unique', 'propel.validator.UniqueValidator', '', 'CALIB_ID');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MAX_MEASURED_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MAX_MEASURED_VALUE');

		$tMap->addValidator('MEASURED_VALUE_UNITS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'MEASURED_VALUE_UNITS');

		$tMap->addValidator('MEASURED_VALUE_UNITS', 'required', 'propel.validator.RequiredValidator', '', 'MEASURED_VALUE_UNITS');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('MIN_MEASURED_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MIN_MEASURED_VALUE');

		$tMap->addValidator('REFERENCE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REFERENCE');

		$tMap->addValidator('REFERENCE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REFERENCE');

		$tMap->addValidator('REFERENCE', 'required', 'propel.validator.RequiredValidator', '', 'REFERENCE');

		$tMap->addValidator('REFERENCE_UNITS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'REFERENCE_UNITS');

		$tMap->addValidator('REFERENCE_UNITS', 'required', 'propel.validator.RequiredValidator', '', 'REFERENCE_UNITS');

		$tMap->addValidator('SENSITIVITY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY', 'required', 'propel.validator.RequiredValidator', '', 'SENSITIVITY');

		$tMap->addValidator('SENSITIVITY_UNITS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'SENSITIVITY_UNITS');

		$tMap->addValidator('SENSITIVITY_UNITS', 'required', 'propel.validator.RequiredValidator', '', 'SENSITIVITY_UNITS');

		$tMap->addValidator('SENSOR_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_ID');

	} // doBuild()

} // CalibrationMapBuilder
