<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CONTROLLER_CHANNEL' table to 'NEEScentral' DatabaseMap object.
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
class ControllerChannelMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ControllerChannelMapBuilder';

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

		$tMap = $this->dbMap->addTable('CONTROLLER_CHANNEL');
		$tMap->setPhpName('ControllerChannel');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CONTROLLER_CHANNEL_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('CONTROLLER_CONFIG_ID', 'ControllerConfigId', 'double', CreoleTypes::NUMERIC, 'CONTROLLER_CONFIG', 'ID', false, 22);

		$tMap->addForeignKey('DATA_FILE_ID', 'DataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('DIRECTION', 'Direction', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, true, 400);

		$tMap->addForeignKey('SOURCE_LOCATION_ID', 'SourceLocationId', 'double', CreoleTypes::NUMERIC, 'LOCATION', 'ID', true, 22);

		$tMap->addColumn('STATION', 'Station', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addValidator('CONTROLLER_CONFIG_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONTROLLER_CONFIG_ID');

		$tMap->addValidator('CONTROLLER_CONFIG_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONTROLLER_CONFIG_ID');

		$tMap->addValidator('CONTROLLER_CONFIG_ID', 'required', 'propel.validator.RequiredValidator', '', 'CONTROLLER_CONFIG_ID');

		$tMap->addValidator('DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DIRECTION', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'DIRECTION');

		$tMap->addValidator('DIRECTION', 'required', 'propel.validator.RequiredValidator', '', 'DIRECTION');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SOURCE_LOCATION_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SOURCE_LOCATION_ID');

		$tMap->addValidator('SOURCE_LOCATION_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SOURCE_LOCATION_ID');

		$tMap->addValidator('SOURCE_LOCATION_ID', 'required', 'propel.validator.RequiredValidator', '', 'SOURCE_LOCATION_ID');

		$tMap->addValidator('STATION', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'STATION');

		$tMap->addValidator('STATION', 'required', 'propel.validator.RequiredValidator', '', 'STATION');

	} // doBuild()

} // ControllerChannelMapBuilder
