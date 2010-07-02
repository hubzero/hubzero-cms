<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MATERIAL_TYPE_PROPERTY' table to 'NEEScentral' DatabaseMap object.
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
class MaterialTypePropertyMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MaterialTypePropertyMapBuilder';

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

		$tMap = $this->dbMap->addTable('MATERIAL_TYPE_PROPERTY');
		$tMap->setPhpName('MaterialTypeProperty');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MTRL_TYP_PRPRTY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DATATYPE', 'DataType', 'string', CreoleTypes::VARCHAR, false, 24);

		$tMap->addColumn('DISPLAY_NAME', 'DisplayName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('MATERIAL_TYPE_ID', 'MaterialTypeId', 'double', CreoleTypes::NUMERIC, 'MATERIAL_TYPE', 'ID', false, 22);

		$tMap->addForeignKey('MEASUREMENT_UNIT_CATEGORY_ID', 'MeasurementUnitCategoryId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT_CATEGORY', 'ID', false, 22);

		$tMap->addColumn('OPTIONS', 'Options', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('REQUIRED', 'Required', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('STATUS', 'Status', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('UNITS', 'Units', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addValidator('DATATYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '24', 'DATATYPE');

		$tMap->addValidator('DATATYPE', 'required', 'propel.validator.RequiredValidator', '', 'DATATYPE');

		$tMap->addValidator('DISPLAY_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'DISPLAY_NAME');

		$tMap->addValidator('DISPLAY_NAME', 'required', 'propel.validator.RequiredValidator', '', 'DISPLAY_NAME');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_CATEGORY_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MEASUREMENT_UNIT_CATEGORY_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_CATEGORY_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MEASUREMENT_UNIT_CATEGORY_ID');

		$tMap->addValidator('MEASUREMENT_UNIT_CATEGORY_ID', 'required', 'propel.validator.RequiredValidator', '', 'MEASUREMENT_UNIT_CATEGORY_ID');

		$tMap->addValidator('OPTIONS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'OPTIONS');

		$tMap->addValidator('OPTIONS', 'required', 'propel.validator.RequiredValidator', '', 'OPTIONS');

		$tMap->addValidator('REQUIRED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REQUIRED');

		$tMap->addValidator('REQUIRED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REQUIRED');

		$tMap->addValidator('REQUIRED', 'required', 'propel.validator.RequiredValidator', '', 'REQUIRED');

		$tMap->addValidator('STATUS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'STATUS');

		$tMap->addValidator('STATUS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'STATUS');

		$tMap->addValidator('STATUS', 'required', 'propel.validator.RequiredValidator', '', 'STATUS');

		$tMap->addValidator('UNITS', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'UNITS');

		$tMap->addValidator('UNITS', 'required', 'propel.validator.RequiredValidator', '', 'UNITS');

	} // doBuild()

} // MaterialTypePropertyMapBuilder
