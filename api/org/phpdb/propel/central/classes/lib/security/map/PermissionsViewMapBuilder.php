<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PERMISSION' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.security.map
 */
class PermissionsViewMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.security.map.PermissionsViewMapBuilder';

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

		$tMap = $this->dbMap->addTable('PERMISSION');
		$tMap->setPhpName('PermissionsView');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('PERSON_ID', 'PersonId', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('ENTITY_TYPE_ID', 'EntityTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ENTITY_ID', 'EntityId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CAN_VIEW', 'CanView', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CAN_CREATE', 'CanCreate', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CAN_EDIT', 'CanEdit', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CAN_DELETE', 'CanDelete', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CAN_GRANT', 'CanGrant', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('IS_SUPER_ROLE', 'SuperRole', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PERMISSIONS', 'PermissionsStr', 'string', CreoleTypes::VARCHAR, false, 112);

	} // doBuild()

} // PermissionsViewMapBuilder
