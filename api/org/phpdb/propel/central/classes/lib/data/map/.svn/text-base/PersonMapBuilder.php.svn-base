<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PERSON' table to 'NEEScentral' DatabaseMap object.
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
class PersonMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.PersonMapBuilder';

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

		$tMap = $this->dbMap->addTable('PERSON');
		$tMap->setPhpName('Person');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('PERSON_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ADDRESS', 'Address', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('ADMIN_STATUS', 'AdminStatus', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('CATEGORY', 'Category', 'string', CreoleTypes::VARCHAR, false, 104);

		$tMap->addColumn('COMMENTS', 'Comment', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('E_MAIL', 'EMail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('FAX', 'Fax', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('FIRST_NAME', 'FirstName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('LAST_NAME', 'LastName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('PHONE', 'Phone', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addColumn('USER_NAME', 'UserName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addValidator('ADDRESS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'ADDRESS');

		$tMap->addValidator('ADDRESS', 'required', 'propel.validator.RequiredValidator', '', 'ADDRESS');

		$tMap->addValidator('ADMIN_STATUS', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADMIN_STATUS');

		$tMap->addValidator('ADMIN_STATUS', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADMIN_STATUS');

		$tMap->addValidator('ADMIN_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'ADMIN_STATUS');

		$tMap->addValidator('CATEGORY', 'maxLength', 'propel.validator.MaxLengthValidator', '104', 'CATEGORY');

		$tMap->addValidator('CATEGORY', 'required', 'propel.validator.RequiredValidator', '', 'CATEGORY');

		$tMap->addValidator('COMMENTS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COMMENTS');

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('E_MAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'E_MAIL');

		$tMap->addValidator('E_MAIL', 'required', 'propel.validator.RequiredValidator', '', 'E_MAIL');

		$tMap->addValidator('FAX', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'FAX');

		$tMap->addValidator('FAX', 'required', 'propel.validator.RequiredValidator', '', 'FAX');

		$tMap->addValidator('FIRST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'FIRST_NAME');

		$tMap->addValidator('FIRST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'FIRST_NAME');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('LAST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'LAST_NAME');

		$tMap->addValidator('LAST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'LAST_NAME');

		$tMap->addValidator('PHONE', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'PHONE');

		$tMap->addValidator('PHONE', 'required', 'propel.validator.RequiredValidator', '', 'PHONE');

		$tMap->addValidator('USER_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'USER_NAME');

		$tMap->addValidator('USER_NAME', 'required', 'propel.validator.RequiredValidator', '', 'USER_NAME');

	} // doBuild()

} // PersonMapBuilder
