<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ENTITY_ACTIVITY_LOG' table to 'NEEScentral' DatabaseMap object.
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
class EntityActivityLogMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EntityActivityLogMapBuilder';

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

		$tMap = $this->dbMap->addTable('ENTITY_ACTIVITY_LOG');
		$tMap->setPhpName('EntityActivityLog');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignKey('ENTITY_TYPE_ID', 'EntityTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', true, 22);

		$tMap->addColumn('ENTITY_ID', 'EntityId', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('VIEW_COUNT', 'ViewCount', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DOWNLOAD_COUNT', 'DownloadCount', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addValidator('ENTITY_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_ID', 'required', 'propel.validator.RequiredValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ENTITY_TYPE_ID');

	} // doBuild()

} // EntityActivityLogMapBuilder
