<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'COORDINATE_SYSTEM' table to 'NEEScentral' DatabaseMap object.
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
class CoordinateSystemMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.CoordinateSystemMapBuilder';

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

		$tMap = $this->dbMap->addTable('COORDINATE_SYSTEM');
		$tMap->setPhpName('CoordinateSystem');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('COORDINATE_SYSTEM_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('DIM1', 'Dimension1', 'double', CreoleTypes::NUMERIC, 'COORDINATE_DIMENSION', 'ID', false, 22);

		$tMap->addForeignKey('DIM2', 'Dimension2', 'double', CreoleTypes::NUMERIC, 'COORDINATE_DIMENSION', 'ID', false, 22);

		$tMap->addForeignKey('DIM3', 'Dimension3', 'double', CreoleTypes::NUMERIC, 'COORDINATE_DIMENSION', 'ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addValidator('DIM1', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DIM1');

		$tMap->addValidator('DIM1', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DIM1');

		$tMap->addValidator('DIM1', 'required', 'propel.validator.RequiredValidator', '', 'DIM1');

		$tMap->addValidator('DIM2', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DIM2');

		$tMap->addValidator('DIM2', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DIM2');

		$tMap->addValidator('DIM2', 'required', 'propel.validator.RequiredValidator', '', 'DIM2');

		$tMap->addValidator('DIM3', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DIM3');

		$tMap->addValidator('DIM3', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DIM3');

		$tMap->addValidator('DIM3', 'required', 'propel.validator.RequiredValidator', '', 'DIM3');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

	} // doBuild()

} // CoordinateSystemMapBuilder
