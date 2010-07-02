<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'USER_REQ' table to 'NEEScentral' DatabaseMap object.
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
class UserReqMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.UserReqMapBuilder';

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

		$tMap = $this->dbMap->addTable('USER_REQ');
		$tMap->setPhpName('UserReq');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('USER_REQ_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('PASSWD', 'Password', 'string', CreoleTypes::VARCHAR, true, 200);

		$tMap->addColumn('EMAIL', 'Email', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('CATEGORY', 'Category', 'string', CreoleTypes::VARCHAR, false, 50);

		$tMap->addColumn('FIRST_NAME', 'FirstName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('LAST_NAME', 'LastName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('PHONE', 'Phone', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addColumn('FAX', 'Fax', 'string', CreoleTypes::VARCHAR, false, 20);

		$tMap->addColumn('ADDRESS', 'Address', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('COMMENTS', 'Comment', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('ORGID', 'OrganizationId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ORG_ROLE_ID', 'OrgRoleId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('EE_ORGANIZATION', 'EEOrganization', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('PERSONAL_REFERENCE', 'PersonalReference', 'string', CreoleTypes::VARCHAR, true, 100);

	} // doBuild()

} // UserReqMapBuilder
