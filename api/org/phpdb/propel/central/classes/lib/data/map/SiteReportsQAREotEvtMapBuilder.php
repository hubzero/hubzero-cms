<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SITEREPORTS_QAR_EOT_EVT' table to 'NEEScentral' DatabaseMap object.
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
class SiteReportsQAREotEvtMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SiteReportsQAREotEvtMapBuilder';

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

		$tMap = $this->dbMap->addTable('SITEREPORTS_QAR_EOT_EVT');
		$tMap->setPhpName('SiteReportsQAREotEvt');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SITEREPORTS_QAR_EOT_EVT_SEQ');

		$tMap->addPrimaryKey('ID', 'ID', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('QAR_ID', 'QAR_ID', 'double', CreoleTypes::NUMERIC, 'SITEREPORTS_QAR', 'ID', false, 22);

		$tMap->addColumn('EVENT_TYPE', 'EVENT_TYPE', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('ACTIVITY', 'ACTIVITY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('ACTIVITY_OBJECTIVES', 'ACTIVITY_OBJECTIVES', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('OBJECTIVE_MET', 'OBJECTIVE_MET', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addColumn('PARTICIPANT_CAT1', 'PARTICIPANT_CAT1', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('NUM_OF_PARTICIPANTS1', 'NUM_OF_PARTICIPANTS1', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PARTICIPANT_DETAILS1', 'PARTICIPANT_DETAILS1', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PARTICIPANT_CAT2', 'PARTICIPANT_CAT2', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('NUM_OF_PARTICIPANTS2', 'NUM_OF_PARTICIPANTS2', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PARTICIPANT_DETAILS2', 'PARTICIPANT_DETAILS2', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PARTICIPANT_CAT3', 'PARTICIPANT_CAT3', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('NUM_OF_PARTICIPANTS3', 'NUM_OF_PARTICIPANTS3', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PARTICIPANT_DETAILS3', 'PARTICIPANT_DETAILS3', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PARTICIPANT_CAT4', 'PARTICIPANT_CAT4', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('NUM_OF_PARTICIPANTS4', 'NUM_OF_PARTICIPANTS4', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PARTICIPANT_DETAILS4', 'PARTICIPANT_DETAILS4', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('EVENT_NAR', 'EVENT_NAR', 'string', CreoleTypes::VARCHAR, false, null);

		$tMap->addColumn('CREATED_BY', 'CREATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('CREATED_ON', 'CREATED_ON', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('UPDATED_BY', 'UPDATED_BY', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('UPDATED_ON', 'UPDATED_ON', 'int', CreoleTypes::DATE, false, null);

	} // doBuild()

} // SiteReportsQAREotEvtMapBuilder
