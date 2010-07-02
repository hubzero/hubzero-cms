<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SENSOR_SENSOR_MANIFEST' table to 'NEEScentral' DatabaseMap object.
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
class SensorSensorManifestMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SensorSensorManifestMapBuilder';

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

		$tMap = $this->dbMap->addTable('SENSOR_SENSOR_MANIFEST');
		$tMap->setPhpName('SensorSensorManifest');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SNSR_SNSR_MNFST_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('SENSOR_ID', 'SensorId', 'double', CreoleTypes::NUMERIC, 'SENSOR', 'SENSOR_ID', false, 22);

		$tMap->addForeignKey('MANIFEST_ID', 'SensorManifestId', 'double', CreoleTypes::NUMERIC, 'SENSOR_MANIFEST', 'ID', false, 22);

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MANIFEST_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MANIFEST_ID');

		$tMap->addValidator('MANIFEST_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MANIFEST_ID');

		$tMap->addValidator('MANIFEST_ID', 'required', 'propel.validator.RequiredValidator', '', 'MANIFEST_ID');

		$tMap->addValidator('SENSOR_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SENSOR_ID');

		$tMap->addValidator('SENSOR_ID', 'required', 'propel.validator.RequiredValidator', '', 'SENSOR_ID');

	} // doBuild()

} // SensorSensorManifestMapBuilder
