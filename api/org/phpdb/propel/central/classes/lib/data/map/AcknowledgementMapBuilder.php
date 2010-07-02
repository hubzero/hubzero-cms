<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ACKNOWLEDGEMENT' table to 'NEEScentral' DatabaseMap object.
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
class AcknowledgementMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.AcknowledgementMapBuilder';

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

		$tMap = $this->dbMap->addTable('ACKNOWLEDGEMENT');
		$tMap->setPhpName('Acknowledgement');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ACKNOWLEDGEMENT_SEQ');

		$tMap->addPrimaryKey('ACKID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addColumn('HOW_TO_CITE', 'HowToCite', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('PROJID', 'ProjectId', 'double', CreoleTypes::NUMERIC, 'PROJECT', 'PROJID', false, 22);

		$tMap->addColumn('SPONSOR', 'Sponsor', 'string', CreoleTypes::VARCHAR, false, 4000);

		$tMap->addForeignKey('TRIALID', 'TrialId', 'double', CreoleTypes::NUMERIC, 'TRIAL', 'TRIALID', false, 22);

		$tMap->addValidator('ACKID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ACKID');

		$tMap->addValidator('ACKID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ACKID');

		$tMap->addValidator('ACKID', 'required', 'propel.validator.RequiredValidator', '', 'ACKID');

		$tMap->addValidator('ACKID', 'unique', 'propel.validator.UniqueValidator', '', 'ACKID');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('HOW_TO_CITE', 'required', 'propel.validator.RequiredValidator', '', 'HOW_TO_CITE');

		$tMap->addValidator('PROJID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PROJID');

		$tMap->addValidator('PROJID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PROJID');

		$tMap->addValidator('PROJID', 'required', 'propel.validator.RequiredValidator', '', 'PROJID');

		$tMap->addValidator('SPONSOR', 'maxLength', 'propel.validator.MaxLengthValidator', '4000', 'SPONSOR');

		$tMap->addValidator('SPONSOR', 'required', 'propel.validator.RequiredValidator', '', 'SPONSOR');

		$tMap->addValidator('TRIALID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIALID');

		$tMap->addValidator('TRIALID', 'required', 'propel.validator.RequiredValidator', '', 'TRIALID');

	} // doBuild()

} // AcknowledgementMapBuilder
