<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT_ATTRIBUTE_VALUE' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentAttributeValueMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentAttributeValueMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT_ATTRIBUTE_VALUE');
		$tMap->setPhpName('EquipmentAttributeValue');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('QPMNT_TTRBT_VL_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('EQUIPMENT_ATTRIBUTE_CLASS_ID', 'EquipmentAttributeClassId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_ATTRIBUTE_CLASS', 'ID', false, 22);

		$tMap->addForeignKey('EQUIPMENT_ATTRIBUTE_ID', 'EquipmentAttributeId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_ATTRIBUTE', 'ID', false, 22);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addColumn('NOTE', 'Note', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('UNIT_ID', 'UnitId', 'double', CreoleTypes::NUMERIC, 'UNIT', 'UNIT_ID', false, 22);

		$tMap->addColumn('VALUE', 'Value', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_CLASS_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ATTRIBUTE_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_CLASS_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ATTRIBUTE_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_CLASS_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ATTRIBUTE_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ATTRIBUTE_ID');

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ATTRIBUTE_ID');

		$tMap->addValidator('EQUIPMENT_ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ATTRIBUTE_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NOTE', 'required', 'propel.validator.RequiredValidator', '', 'NOTE');

		$tMap->addValidator('UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_ID');

		$tMap->addValidator('VALUE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'VALUE');

		$tMap->addValidator('VALUE', 'required', 'propel.validator.RequiredValidator', '', 'VALUE');

	} // doBuild()

} // EquipmentAttributeValueMapBuilder
