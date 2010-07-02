<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT');
		$tMap->setPhpName('Equipment');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EQUIPMENT_SEQ');

		$tMap->addPrimaryKey('EQUIPMENT_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CALIBRATION_INFORMATION', 'CalibrationInformation', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('COMMISSION_DATE', 'CommissionDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('LAB_ASSIGNED_ID', 'LabAssignedId', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('MAJOR', 'Major', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('MODEL_ID', 'ModelId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_MODEL', 'ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('NEES_OPERATED', 'NeesOperated', 'string', CreoleTypes::VARCHAR, false, 36);

		$tMap->addColumn('NOTE', 'Note', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('ORGID', 'OrganizationId', 'double', CreoleTypes::NUMERIC, 'ORGANIZATION', 'ORGID', false, 22);

		$tMap->addColumn('OWNER', 'Owner', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addForeignKey('PARENT_ID', 'ParentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addColumn('QUANTITY', 'Quantity', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SEPARATE_SCHEDULING', 'SeparateScheduling', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SERIAL_NUMBER', 'SerialNumber', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('CALIBRATION_INFORMATION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CALIBRATION_INFORMATION');

		$tMap->addValidator('CALIBRATION_INFORMATION', 'required', 'propel.validator.RequiredValidator', '', 'CALIBRATION_INFORMATION');

		$tMap->addValidator('COMMISSION_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'COMMISSION_DATE');

		$tMap->addValidator('COMMISSION_DATE', 'required', 'propel.validator.RequiredValidator', '', 'COMMISSION_DATE');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'unique', 'propel.validator.UniqueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('LAB_ASSIGNED_ID', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'LAB_ASSIGNED_ID');

		$tMap->addValidator('LAB_ASSIGNED_ID', 'required', 'propel.validator.RequiredValidator', '', 'LAB_ASSIGNED_ID');

		$tMap->addValidator('MAJOR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAJOR');

		$tMap->addValidator('MAJOR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAJOR');

		$tMap->addValidator('MAJOR', 'required', 'propel.validator.RequiredValidator', '', 'MAJOR');

		$tMap->addValidator('MODEL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MODEL_ID');

		$tMap->addValidator('MODEL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MODEL_ID');

		$tMap->addValidator('MODEL_ID', 'required', 'propel.validator.RequiredValidator', '', 'MODEL_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('NEES_OPERATED', 'maxLength', 'propel.validator.MaxLengthValidator', '36', 'NEES_OPERATED');

		$tMap->addValidator('NEES_OPERATED', 'required', 'propel.validator.RequiredValidator', '', 'NEES_OPERATED');

		$tMap->addValidator('NOTE', 'required', 'propel.validator.RequiredValidator', '', 'NOTE');

		$tMap->addValidator('ORGID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ORGID');

		$tMap->addValidator('ORGID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ORGID');

		$tMap->addValidator('ORGID', 'required', 'propel.validator.RequiredValidator', '', 'ORGID');

		$tMap->addValidator('OWNER', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'OWNER');

		$tMap->addValidator('OWNER', 'required', 'propel.validator.RequiredValidator', '', 'OWNER');

		$tMap->addValidator('PARENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'PARENT_ID');

		$tMap->addValidator('QUANTITY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'QUANTITY');

		$tMap->addValidator('QUANTITY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'QUANTITY');

		$tMap->addValidator('QUANTITY', 'required', 'propel.validator.RequiredValidator', '', 'QUANTITY');

		$tMap->addValidator('SEPARATE_SCHEDULING', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SEPARATE_SCHEDULING');

		$tMap->addValidator('SEPARATE_SCHEDULING', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SEPARATE_SCHEDULING');

		$tMap->addValidator('SEPARATE_SCHEDULING', 'required', 'propel.validator.RequiredValidator', '', 'SEPARATE_SCHEDULING');

		$tMap->addValidator('SERIAL_NUMBER', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SERIAL_NUMBER');

		$tMap->addValidator('SERIAL_NUMBER', 'required', 'propel.validator.RequiredValidator', '', 'SERIAL_NUMBER');

	} // doBuild()

} // EquipmentMapBuilder
