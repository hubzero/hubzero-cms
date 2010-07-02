<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'REPETITION' table to 'NEEScentral' DatabaseMap object.
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
class RepetitionMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.RepetitionMapBuilder';

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

		$tMap = $this->dbMap->addTable('REPETITION');
		$tMap->setPhpName('Repetition');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('REPETITION_SEQ');

		$tMap->addPrimaryKey('REPID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CURATION_STATUS', 'CurationStatus', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('END_DATE', 'EndDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('START_DATE', 'StartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('TRIALID', 'TrialId', 'double', CreoleTypes::NUMERIC, 'TRIAL', 'TRIALID', false, 22);

		$tMap->addValidator('CURATION_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CURATION_STATUS');

		$tMap->addValidator('CURATION_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'CURATION_STATUS');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('END_DATE', 'required', 'propel.validator.RequiredValidator', '', 'END_DATE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('REPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REPID');

		$tMap->addValidator('REPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REPID');

		$tMap->addValidator('REPID', 'required', 'propel.validator.RequiredValidator', '', 'REPID');

		$tMap->addValidator('REPID', 'unique', 'propel.validator.UniqueValidator', '', 'REPID');

		$tMap->addValidator('START_DATE', 'required', 'propel.validator.RequiredValidator', '', 'START_DATE');

		$tMap->addValidator('STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'STATUS');

		$tMap->addValidator('STATUS', 'required', 'propel.validator.RequiredValidator', '', 'STATUS');

		$tMap->addValidator('TRIALID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'required', 'propel.validator.RequiredValidator', '', 'TRIALID');

	} // doBuild()

} // RepetitionMapBuilder
