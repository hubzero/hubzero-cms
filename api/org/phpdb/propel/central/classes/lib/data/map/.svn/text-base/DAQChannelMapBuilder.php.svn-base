<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DAQCHANNEL' table to 'NEEScentral' DatabaseMap object.
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
class DAQChannelMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DAQChannelMapBuilder';

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

		$tMap = $this->dbMap->addTable('DAQCHANNEL');
		$tMap->setPhpName('DAQChannel');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DAQCHANNEL_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ADCRANGE', 'ADCRange', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('ADCRESOLUTION', 'ADCResolution', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('CHANNEL_ORDER', 'ChannelOrder', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('DAQCONFIG_ID', 'DAQConfigId', 'double', CreoleTypes::NUMERIC, 'DAQCONFIG', 'ID', true, 22);

		$tMap->addForeignKey('DATA_FILE_ID', 'DataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('EXCITATION', 'Excitation', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('GAIN', 'Gain', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('SENSOR_ID', 'SensorId', 'double', CreoleTypes::NUMERIC, 'SENSOR', 'SENSOR_ID', false, 22);

		$tMap->addForeignKey('SENSOR_LOCATION_ID', 'SensorLocationId', 'double', CreoleTypes::NUMERIC, 'LOCATION', 'ID', true, 22);

		$tMap->addValidator('ADCRANGE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADCRANGE');

		$tMap->addValidator('ADCRANGE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADCRANGE');

		$tMap->addValidator('ADCRANGE', 'required', 'propel.validator.RequiredValidator', '', 'ADCRANGE');

		$tMap->addValidator('ADCRESOLUTION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADCRESOLUTION');

		$tMap->addValidator('ADCRESOLUTION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADCRESOLUTION');

		$tMap->addValidator('ADCRESOLUTION', 'required', 'propel.validator.RequiredValidator', '', 'ADCRESOLUTION');

		$tMap->addValidator('CHANNEL_ORDER', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CHANNEL_ORDER');

		$tMap->addValidator('CHANNEL_ORDER', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CHANNEL_ORDER');

		$tMap->addValidator('CHANNEL_ORDER', 'required', 'propel.validator.RequiredValidator', '', 'CHANNEL_ORDER');

		$tMap->addValidator('DAQCONFIG_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DAQCONFIG_ID');

		$tMap->addValidator('DAQCONFIG_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DAQCONFIG_ID');

		$tMap->addValidator('DAQCONFIG_ID', 'required', 'propel.validator.RequiredValidator', '', 'DAQCONFIG_ID');

		$tMap->addValidator('DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EXCITATION', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXCITATION');

		$tMap->addValidator('EXCITATION', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXCITATION');

		$tMap->addValidator('EXCITATION', 'required', 'propel.validator.RequiredValidator', '', 'EXCITATION');

		$tMap->addValidator('GAIN', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GAIN');

		$tMap->addValidator('GAIN', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GAIN');

		$tMap->addValidator('GAIN', 'required', 'propel.validator.RequiredValidator', '', 'GAIN');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SENSOR_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_LOCATION_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_LOCATION_ID');

		$tMap->addValidator('SENSOR_LOCATION_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_LOCATION_ID');

		$tMap->addValidator('SENSOR_LOCATION_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_LOCATION_ID');

	} // doBuild()

} // DAQChannelMapBuilder
