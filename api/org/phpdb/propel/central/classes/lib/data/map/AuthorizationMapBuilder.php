<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'AUTHORIZATION' table to 'NEEScentral' DatabaseMap object.
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
class AuthorizationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.AuthorizationMapBuilder';

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

		$tMap = $this->dbMap->addTable('AUTHORIZATION');
		$tMap->setPhpName('Authorization');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('AUTHORIZATION_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ENTITY_ID', 'EntityId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('ENTITY_TYPE_ID', 'EntityTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', false, 22);

		$tMap->addColumn('PERMISSIONS', 'PermissionsStr', 'string', CreoleTypes::VARCHAR, false, 112);

		$tMap->addForeignKey('PERSON_ID', 'Uid', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addValidator('ENTITY_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_ID', 'required', 'propel.validator.RequiredValidator', '', 'ENTITY_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ENTITY_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ENTITY_TYPE_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('PERMISSIONS', 'maxLength', 'propel.validator.MaxLengthValidator', '112', 'PERMISSIONS');

		$tMap->addValidator('PERMISSIONS', 'required', 'propel.validator.RequiredValidator', '', 'PERMISSIONS');

		$tMap->addValidator('PERSON_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PERSON_ID');

		$tMap->addValidator('PERSON_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PERSON_ID');

		$tMap->addValidator('PERSON_ID', 'required', 'propel.validator.RequiredValidator', '', 'PERSON_ID');

	} // doBuild()

} // AuthorizationMapBuilder
