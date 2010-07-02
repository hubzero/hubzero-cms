<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'LOCATION_PLAN' table to 'NEEScentral' DatabaseMap object.
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
class LocationPlanMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.LocationPlanMapBuilder';

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

		$tMap = $this->dbMap->addTable('LOCATION_PLAN');
		$tMap->setPhpName('LocationPlan');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('LOCATION_PLAN_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addForeignKey('TRIAL_ID', 'TrialId', 'double', CreoleTypes::NUMERIC, 'TRIAL', 'TRIALID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('PLAN_TYPE_ID', 'PlanTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('TRIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRIAL_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PLAN_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PLAN_TYPE_ID');

		$tMap->addValidator('PLAN_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PLAN_TYPE_ID');

		$tMap->addValidator('PLAN_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'PLAN_TYPE_ID');

	} // doBuild()

} // LocationPlanMapBuilder
