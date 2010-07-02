<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SESSIONS' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.curation.map
 */
class NCSessionsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCSessionsMapBuilder';

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

		$tMap = $this->dbMap->addTable('SESSIONS');
		$tMap->setPhpName('NCSessions');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SESSIONS_SEQ');

		$tMap->addPrimaryKey('SESSION_ID', 'SessionId', 'string', CreoleTypes::VARCHAR, true, 128);

		$tMap->addColumn('ACCESS_TIME', 'AccessTime', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('DATA', 'Data', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('IP_ADDRESS', 'IpAddress', 'string', CreoleTypes::VARCHAR, false, 60);

		$tMap->addValidator('ACCESS_TIME', 'required', 'propel.validator.RequiredValidator', '', 'ACCESS_TIME');

		$tMap->addValidator('DATA', 'required', 'propel.validator.RequiredValidator', '', 'DATA');

		$tMap->addValidator('IP_ADDRESS', 'maxLength', 'propel.validator.MaxLengthValidator', '60', 'IP_ADDRESS');

		$tMap->addValidator('IP_ADDRESS', 'required', 'propel.validator.RequiredValidator', '', 'IP_ADDRESS');

		$tMap->addValidator('SESSION_ID', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'SESSION_ID');

		$tMap->addValidator('SESSION_ID', 'required', 'propel.validator.RequiredValidator', '', 'SESSION_ID');

		$tMap->addValidator('SESSION_ID', 'unique', 'propel.validator.UniqueValidator', '', 'SESSION_ID');

	} // doBuild()

} // NCSessionsMapBuilder
