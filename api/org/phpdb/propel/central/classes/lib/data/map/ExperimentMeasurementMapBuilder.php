<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EXPERIMENT_MEASUREMENT' table to 'NEEScentral' DatabaseMap object.
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
class ExperimentMeasurementMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ExperimentMeasurementMapBuilder';

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

		$tMap = $this->dbMap->addTable('EXPERIMENT_MEASUREMENT');
		$tMap->setPhpName('ExperimentMeasurement');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('XPRMNT_MSRMNT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('CATEGORY', 'CategoryId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT_CATEGORY', 'ID', false, 22);

		$tMap->addForeignKey('DEFAULT_UNIT', 'DefaultUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addValidator('CATEGORY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'required', 'propel.validator.RequiredValidator', '', 'CATEGORY');

		$tMap->addValidator('DEFAULT_UNIT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DEFAULT_UNIT');

		$tMap->addValidator('DEFAULT_UNIT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DEFAULT_UNIT');

		$tMap->addValidator('DEFAULT_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'DEFAULT_UNIT');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

	} // doBuild()

} // ExperimentMeasurementMapBuilder
