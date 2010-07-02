<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SENSOR_GROUPED_ATTRIBUTE' table to 'NEEScentral' DatabaseMap object.
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
class SensorGroupedAttributeMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SensorGroupedAttributeMapBuilder';

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

		$tMap = $this->dbMap->addTable('SENSOR_GROUPED_ATTRIBUTE');
		$tMap->setPhpName('SensorGroupedAttribute');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SNSR_GRPD_TTRBT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('ATTRIBUTE_ID', 'AttributeId', 'double', CreoleTypes::NUMERIC, 'ATTRIBUTE', 'ATTRIBUTE_ID', false, 22);

		$tMap->addColumn('DATE_VALUE', 'DateValue', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('G_ATTRIBUTE_ID', 'GroupAttributeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('INT_VALUE', 'IntValue', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NOTE', 'Note', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('NUM_VALUE', 'NumValue', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('PAGE_COUNT', 'PageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STRING_VALUE', 'StringValue', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('UNIT_ID', 'UnitId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('DATE_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DATE_VALUE');

		$tMap->addValidator('DATE_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'DATE_VALUE');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INT_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INT_VALUE');

		$tMap->addValidator('INT_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INT_VALUE');

		$tMap->addValidator('INT_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'INT_VALUE');

		$tMap->addValidator('NOTE', 'required', 'propel.validator.RequiredValidator', '', 'NOTE');

		$tMap->addValidator('NUM_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'NUM_VALUE');

		$tMap->addValidator('NUM_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'NUM_VALUE');

		$tMap->addValidator('NUM_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'NUM_VALUE');

		$tMap->addValidator('PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('STRING_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'STRING_VALUE');

		$tMap->addValidator('UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_ID');

	} // doBuild()

} // SensorGroupedAttributeMapBuilder
