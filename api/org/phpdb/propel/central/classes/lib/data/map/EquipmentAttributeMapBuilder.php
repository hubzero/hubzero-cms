<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT_ATTRIBUTE' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentAttributeMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentAttributeMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT_ATTRIBUTE');
		$tMap->setPhpName('EquipmentAttribute');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EQUIPMENT_ATTRIBUTE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DATA_TYPE', 'DataType', 'string', CreoleTypes::VARCHAR, false, 28);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('LABEL', 'Label', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('MAX_VALUE', 'MaxValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('MIN_VALUE', 'MinValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addForeignKey('PARENT_ID', 'ParentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_ATTRIBUTE', 'ID', false, 22);

		$tMap->addForeignKey('UNIT_ID', 'UnitId', 'double', CreoleTypes::NUMERIC, 'UNIT', 'UNIT_ID', false, 22);

		$tMap->addValidator('DATA_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '28', 'DATA_TYPE');

		$tMap->addValidator('DATA_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'DATA_TYPE');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('LABEL', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'LABEL');

		$tMap->addValidator('LABEL', 'required', 'propel.validator.RequiredValidator', '', 'LABEL');

		$tMap->addValidator('MAX_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAX_VALUE');

		$tMap->addValidator('MAX_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAX_VALUE');

		$tMap->addValidator('MAX_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MAX_VALUE');

		$tMap->addValidator('MIN_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MIN_VALUE');

		$tMap->addValidator('MIN_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MIN_VALUE');

		$tMap->addValidator('MIN_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'MIN_VALUE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PARENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'PARENT_ID');

		$tMap->addValidator('UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_ID');

	} // doBuild()

} // EquipmentAttributeMapBuilder
