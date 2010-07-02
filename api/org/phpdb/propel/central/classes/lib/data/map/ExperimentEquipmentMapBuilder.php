<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EXPERIMENT_EQUIPMENT' table to 'NEEScentral' DatabaseMap object.
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
class ExperimentEquipmentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ExperimentEquipmentMapBuilder';

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

		$tMap = $this->dbMap->addTable('EXPERIMENT_EQUIPMENT');
		$tMap->setPhpName('ExperimentEquipment');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EXPERIMENT_EQUIPMENT_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMMENTS', 'Comment', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addForeignKey('EXPERIMENT_ID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EXPERIMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPERIMENT_ID');

		$tMap->addValidator('EXPERIMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPERIMENT_ID');

		$tMap->addValidator('EXPERIMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EXPERIMENT_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

	} // doBuild()

} // ExperimentEquipmentMapBuilder
