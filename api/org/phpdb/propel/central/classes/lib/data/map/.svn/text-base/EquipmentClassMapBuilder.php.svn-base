<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT_CLASS' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentClassMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentClassMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT_CLASS');
		$tMap->setPhpName('EquipmentClass');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EQUIPMENT_CLASS_SEQ');

		$tMap->addPrimaryKey('EQUIPMENT_CLASS_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CATEGORY', 'Category', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addColumn('CLASS_NAME', 'ClassName', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('DEPRECATED', 'Deprecated', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('MAJOR', 'Major', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SPEC_AVAILABLE', 'SpecAvailable', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('CATEGORY', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'required', 'propel.validator.RequiredValidator', '', 'CATEGORY');

		$tMap->addValidator('CLASS_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'CLASS_NAME');

		$tMap->addValidator('CLASS_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CLASS_NAME');

		$tMap->addValidator('DEPRECATED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DEPRECATED');

		$tMap->addValidator('DEPRECATED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DEPRECATED');

		$tMap->addValidator('DEPRECATED', 'required', 'propel.validator.RequiredValidator', '', 'DEPRECATED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'unique', 'propel.validator.UniqueValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('MAJOR', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MAJOR');

		$tMap->addValidator('MAJOR', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MAJOR');

		$tMap->addValidator('MAJOR', 'required', 'propel.validator.RequiredValidator', '', 'MAJOR');

		$tMap->addValidator('SPEC_AVAILABLE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SPEC_AVAILABLE');

		$tMap->addValidator('SPEC_AVAILABLE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SPEC_AVAILABLE');

		$tMap->addValidator('SPEC_AVAILABLE', 'required', 'propel.validator.RequiredValidator', '', 'SPEC_AVAILABLE');

	} // doBuild()

} // EquipmentClassMapBuilder
