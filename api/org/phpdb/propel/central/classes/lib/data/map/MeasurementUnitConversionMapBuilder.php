<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MEASUREMENT_UNIT_CONVERSION' table to 'NEEScentral' DatabaseMap object.
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
class MeasurementUnitConversionMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MeasurementUnitConversionMapBuilder';

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

		$tMap = $this->dbMap->addTable('MEASUREMENT_UNIT_CONVERSION');
		$tMap->setPhpName('MeasurementUnitConversion');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MSRMNT_NT_CNVRSN_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('FROM_ID', 'FromId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('K0', 'K0', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('K1', 'K1', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('TO_ID', 'ToId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addValidator('FROM_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FROM_ID');

		$tMap->addValidator('FROM_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FROM_ID');

		$tMap->addValidator('FROM_ID', 'required', 'propel.validator.RequiredValidator', '', 'FROM_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('K0', 'maxValue', 'propel.validator.MaxValueValidator', '', 'K0');

		$tMap->addValidator('K0', 'notMatch', 'propel.validator.NotMatchValidator', '', 'K0');

		$tMap->addValidator('K0', 'required', 'propel.validator.RequiredValidator', '', 'K0');

		$tMap->addValidator('K1', 'maxValue', 'propel.validator.MaxValueValidator', '', 'K1');

		$tMap->addValidator('K1', 'notMatch', 'propel.validator.NotMatchValidator', '', 'K1');

		$tMap->addValidator('K1', 'required', 'propel.validator.RequiredValidator', '', 'K1');

		$tMap->addValidator('TO_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TO_ID');

		$tMap->addValidator('TO_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TO_ID');

		$tMap->addValidator('TO_ID', 'required', 'propel.validator.RequiredValidator', '', 'TO_ID');

	} // doBuild()

} // MeasurementUnitConversionMapBuilder
