<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'UNIT' table to 'NEEScentral' DatabaseMap object.
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
class UnitMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.UnitMapBuilder';

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

		$tMap = $this->dbMap->addTable('UNIT');
		$tMap->setPhpName('Unit');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('UNIT_SEQ');

		$tMap->addPrimaryKey('UNIT_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('BASE_ID', 'BaseId', 'double', CreoleTypes::NUMERIC, 'UNIT', 'UNIT_ID', false, 22);

		$tMap->addColumn('CONVERSION', 'Conversion', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('SYMBOL', 'Symbol', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('UNICODE', 'Unicode', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('BASE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BASE_ID');

		$tMap->addValidator('BASE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BASE_ID');

		$tMap->addValidator('BASE_ID', 'required', 'propel.validator.RequiredValidator', '', 'BASE_ID');

		$tMap->addValidator('CONVERSION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONVERSION');

		$tMap->addValidator('CONVERSION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONVERSION');

		$tMap->addValidator('CONVERSION', 'required', 'propel.validator.RequiredValidator', '', 'CONVERSION');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SYMBOL', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'SYMBOL');

		$tMap->addValidator('SYMBOL', 'required', 'propel.validator.RequiredValidator', '', 'SYMBOL');

		$tMap->addValidator('UNICODE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'UNICODE');

		$tMap->addValidator('UNICODE', 'required', 'propel.validator.RequiredValidator', '', 'UNICODE');

		$tMap->addValidator('UNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_ID');

		$tMap->addValidator('UNIT_ID', 'unique', 'propel.validator.UniqueValidator', '', 'UNIT_ID');

	} // doBuild()

} // UnitMapBuilder
