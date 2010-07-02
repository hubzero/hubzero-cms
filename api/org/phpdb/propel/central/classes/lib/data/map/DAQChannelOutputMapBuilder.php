<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DAQCHANNEL_OUTPUT' table to 'NEEScentral' DatabaseMap object.
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
class DAQChannelOutputMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DAQChannelOutputMapBuilder';

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

		$tMap = $this->dbMap->addTable('DAQCHANNEL_OUTPUT');
		$tMap->setPhpName('DAQChannelOutput');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DAQCHANNEL_OUTPUT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('DAQCHANNEL_ID', 'DAQChannelId', 'double', CreoleTypes::NUMERIC, 'DAQCHANNEL', 'ID', false, 22);

		$tMap->addColumn('TIME', 'Time', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addValidator('DAQCHANNEL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAQCHANNEL_ID');

		$tMap->addValidator('DAQCHANNEL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAQCHANNEL_ID');

		$tMap->addValidator('DAQCHANNEL_ID', 'required', 'propel.validator.RequiredValidator', '', 'DAQCHANNEL_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('TIME', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TIME');

		$tMap->addValidator('TIME', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TIME');

		$tMap->addValidator('TIME', 'required', 'propel.validator.RequiredValidator', '', 'TIME');

	} // doBuild()

} // DAQChannelOutputMapBuilder
