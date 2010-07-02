<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SENSOR_GROUPED_ATTRIBUTE_MAP' table to 'NEEScentral' DatabaseMap object.
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
class SensorGroupedAttributeMapMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SensorGroupedAttributeMapMapBuilder';

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

		$tMap = $this->dbMap->addTable('SENSOR_GROUPED_ATTRIBUTE_MAP');
		$tMap->setPhpName('SensorGroupedAttributeMap');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SNSR_GRPD_TTRBT_MP_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('G_ATTRIBUTE_ID', 'GroupAttributeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('GROUP_VALUE', 'GroupValue', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('GROUP_VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'GROUP_VALUE');

		$tMap->addValidator('GROUP_VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'GROUP_VALUE');

		$tMap->addValidator('GROUP_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'GROUP_VALUE');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('G_ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'G_ATTRIBUTE_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

	} // doBuild()

} // SensorGroupedAttributeMapMapBuilder
