<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ATTRIBUTE' table to 'NEEScentral' DatabaseMap object.
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
class AttributeMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.AttributeMapBuilder';

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

		$tMap = $this->dbMap->addTable('ATTRIBUTE');
		$tMap->setPhpName('Attribute');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ATTRIBUTE_SEQ');

		$tMap->addPrimaryKey('ATTRIBUTE_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DATA_TYPE', 'DataType', 'string', CreoleTypes::VARCHAR, false, 28);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('EQUIPMENT_CLASS_ID', 'EquipmentClassId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_CLASS', 'EQUIPMENT_CLASS_ID', false, 22);

		$tMap->addColumn('LABEL', 'Label', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('MAX_VALUE', 'MaxValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('MIN_VALUE', 'MinValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addForeignKey('UNIT_ID', 'UnitId', 'double', CreoleTypes::NUMERIC, 'UNIT', 'UNIT_ID', false, 22);

		$tMap->addValidator('ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'unique', 'propel.validator.UniqueValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('DATA_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '28', 'DATA_TYPE');

		$tMap->addValidator('DATA_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'DATA_TYPE');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_CLASS_ID');

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

		$tMap->addValidator('UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_ID');

	} // doBuild()

} // AttributeMapBuilder
