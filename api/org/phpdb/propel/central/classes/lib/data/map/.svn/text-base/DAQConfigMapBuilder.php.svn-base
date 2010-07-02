<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DAQCONFIG' table to 'NEEScentral' DatabaseMap object.
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
class DAQConfigMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DAQConfigMapBuilder';

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

		$tMap = $this->dbMap->addTable('DAQCONFIG');
		$tMap->setPhpName('DAQConfig');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DAQCONFIG_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('CONFIG_DATA_FILE_ID', 'ConfigDataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addForeignKey('OUTPUT_DATA_FILE_ID', 'OutputDataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addForeignKey('TRIAL_ID', 'TrialId', 'double', CreoleTypes::NUMERIC, 'TRIAL', 'TRIALID', false, 22);

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('CONFIG_DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'CONFIG_DATA_FILE_ID');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('OUTPUT_DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OUTPUT_DATA_FILE_ID');

		$tMap->addValidator('OUTPUT_DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OUTPUT_DATA_FILE_ID');

		$tMap->addValidator('OUTPUT_DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'OUTPUT_DATA_FILE_ID');

		$tMap->addValidator('TRIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRIAL_ID');

		$tMap->addValidator('TRIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRIAL_ID');

	} // doBuild()

} // DAQConfigMapBuilder
