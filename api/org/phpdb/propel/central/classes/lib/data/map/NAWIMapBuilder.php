<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'NAWI' table to 'NEEScentral' DatabaseMap object.
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
class NAWIMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.NAWIMapBuilder';

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

		$tMap = $this->dbMap->addTable('NAWI');
		$tMap->setPhpName('NAWI');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('NAWI_SEQ');

		$tMap->addPrimaryKey('NAWIID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ACTIVE', 'Active', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CONTACT_EMAIL', 'ContactEmail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CONTACT_NAME', 'ContactName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('EXP_DESCRIPT', 'ExperimentDescription', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('EXP_NAME', 'ExperimentName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('EXP_PHASE', 'ExperimentPhase', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('MOVIE_URL', 'MovieUrl', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TEST_DT', 'TestDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('TEST_END', 'TestEndDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('TEST_START', 'TestStartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('TEST_TZ', 'TestTimeZone', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addValidator('ACTIVE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ACTIVE');

		$tMap->addValidator('ACTIVE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ACTIVE');

		$tMap->addValidator('ACTIVE', 'required', 'propel.validator.RequiredValidator', '', 'ACTIVE');

		$tMap->addValidator('CONTACT_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_EMAIL');

		$tMap->addValidator('CONTACT_EMAIL', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_EMAIL');

		$tMap->addValidator('CONTACT_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_NAME');

		$tMap->addValidator('CONTACT_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_NAME');

		$tMap->addValidator('EXP_DESCRIPT', 'required', 'propel.validator.RequiredValidator', '', 'EXP_DESCRIPT');

		$tMap->addValidator('EXP_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'EXP_NAME');

		$tMap->addValidator('EXP_NAME', 'required', 'propel.validator.RequiredValidator', '', 'EXP_NAME');

		$tMap->addValidator('EXP_PHASE', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'EXP_PHASE');

		$tMap->addValidator('EXP_PHASE', 'required', 'propel.validator.RequiredValidator', '', 'EXP_PHASE');

		$tMap->addValidator('MOVIE_URL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'MOVIE_URL');

		$tMap->addValidator('MOVIE_URL', 'required', 'propel.validator.RequiredValidator', '', 'MOVIE_URL');

		$tMap->addValidator('NAWIID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'NAWIID');

		$tMap->addValidator('NAWIID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'NAWIID');

		$tMap->addValidator('NAWIID', 'required', 'propel.validator.RequiredValidator', '', 'NAWIID');

		$tMap->addValidator('NAWIID', 'unique', 'propel.validator.UniqueValidator', '', 'NAWIID');

		$tMap->addValidator('TEST_DT', 'required', 'propel.validator.RequiredValidator', '', 'TEST_DT');

		$tMap->addValidator('TEST_END', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TEST_END');

		$tMap->addValidator('TEST_END', 'required', 'propel.validator.RequiredValidator', '', 'TEST_END');

		$tMap->addValidator('TEST_START', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TEST_START');

		$tMap->addValidator('TEST_START', 'required', 'propel.validator.RequiredValidator', '', 'TEST_START');

		$tMap->addValidator('TEST_TZ', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'TEST_TZ');

		$tMap->addValidator('TEST_TZ', 'required', 'propel.validator.RequiredValidator', '', 'TEST_TZ');

	} // doBuild()

} // NAWIMapBuilder
