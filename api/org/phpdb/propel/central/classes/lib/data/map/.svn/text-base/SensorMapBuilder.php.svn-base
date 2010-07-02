<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SENSOR' table to 'NEEScentral' DatabaseMap object.
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
class SensorMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SensorMapBuilder';

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

		$tMap = $this->dbMap->addTable('SENSOR');
		$tMap->setPhpName('Sensor');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SENSOR_SEQ');

		$tMap->addPrimaryKey('SENSOR_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMMISSION_DATE', 'CommissionDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('DECOMMISSION_DATE', 'DecommissionDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LOCAL_ID', 'LocalId', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addForeignKey('SENSOR_MODEL_ID', 'SensorModelId', 'double', CreoleTypes::NUMERIC, 'SENSOR_MODEL', 'SENSOR_MODEL_ID', false, 22);

		$tMap->addColumn('SERIAL_NUMBER', 'SerialNumber', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('SUPPLIER', 'Supplier', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addValidator('COMMISSION_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'COMMISSION_DATE');

		$tMap->addValidator('COMMISSION_DATE', 'required', 'propel.validator.RequiredValidator', '', 'COMMISSION_DATE');

		$tMap->addValidator('DECOMMISSION_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DECOMMISSION_DATE');

		$tMap->addValidator('DECOMMISSION_DATE', 'required', 'propel.validator.RequiredValidator', '', 'DECOMMISSION_DATE');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('LOCAL_ID', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'LOCAL_ID');

		$tMap->addValidator('LOCAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'LOCAL_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SENSOR_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'unique', 'propel.validator.UniqueValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SENSOR_MODEL_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_MODEL_ID');

		$tMap->addValidator('SERIAL_NUMBER', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'SERIAL_NUMBER');

		$tMap->addValidator('SERIAL_NUMBER', 'required', 'propel.validator.RequiredValidator', '', 'SERIAL_NUMBER');

		$tMap->addValidator('SUPPLIER', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'SUPPLIER');

		$tMap->addValidator('SUPPLIER', 'required', 'propel.validator.RequiredValidator', '', 'SUPPLIER');

	} // doBuild()

} // SensorMapBuilder
