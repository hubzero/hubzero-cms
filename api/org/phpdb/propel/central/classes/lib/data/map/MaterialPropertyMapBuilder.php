<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MATERIAL_PROPERTY' table to 'NEEScentral' DatabaseMap object.
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
class MaterialPropertyMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MaterialPropertyMapBuilder';

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

		$tMap = $this->dbMap->addTable('MATERIAL_PROPERTY');
		$tMap->setPhpName('MaterialProperty');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MATERIAL_PROPERTY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('MATERIAL_ID', 'MaterialId', 'double', CreoleTypes::NUMERIC, 'MATERIAL', 'ID', false, 22);

		$tMap->addForeignKey('MATERIAL_TYPE_PROPERTY_ID', 'MaterialTypePropertyId', 'double', CreoleTypes::NUMERIC, 'MATERIAL_TYPE_PROPERTY', 'ID', false, 22);

		$tMap->addForeignKey('MEASUREMENT_UNIT_ID', 'MeasurementUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('VALUE', 'Value', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MATERIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MATERIAL_ID');

		$tMap->addValidator('MATERIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MATERIAL_ID');

		$tMap->addValidator('MATERIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'MATERIAL_ID');

		$tMap->addValidator('MATERIAL_TYPE_PROPERTY_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MATERIAL_TYPE_PROPERTY_ID');

		$tMap->addValidator('MATERIAL_TYPE_PROPERTY_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MATERIAL_TYPE_PROPERTY_ID');

		$tMap->addValidator('MATERIAL_TYPE_PROPERTY_ID', 'required', 'propel.validator.RequiredValidator', '', 'MATERIAL_TYPE_PROPERTY_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MEASUREMENT_UNIT_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MEASUREMENT_UNIT_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'MEASUREMENT_UNIT_ID');

		$tMap->addValidator('VALUE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'VALUE');

		$tMap->addValidator('VALUE', 'required', 'propel.validator.RequiredValidator', '', 'VALUE');

	} // doBuild()

} // MaterialPropertyMapBuilder
