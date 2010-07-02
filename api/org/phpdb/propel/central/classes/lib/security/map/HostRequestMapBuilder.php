<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'HOST_REQ' table to 'NEEScentral' DatabaseMap object.
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
class HostRequestMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.security.map.HostRequestMapBuilder';

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

		$tMap = $this->dbMap->addTable('HOST_REQ');
		$tMap->setPhpName('HostRequest');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('HOST_REQ_SEQ');

		$tMap->addPrimaryKey('REQID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMMENTS', 'Comments', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('EMAIL', 'Email', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('FIRST_NAME', 'FirstName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('HOSTNAME', 'Hostname', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('LAST_NAME', 'LastName', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('USERNAME', 'Username', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addValidator('COMMENTS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COMMENTS');

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'EMAIL');

		$tMap->addValidator('EMAIL', 'required', 'propel.validator.RequiredValidator', '', 'EMAIL');

		$tMap->addValidator('FIRST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'FIRST_NAME');

		$tMap->addValidator('FIRST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'FIRST_NAME');

		$tMap->addValidator('HOSTNAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'HOSTNAME');

		$tMap->addValidator('HOSTNAME', 'required', 'propel.validator.RequiredValidator', '', 'HOSTNAME');

		$tMap->addValidator('LAST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'LAST_NAME');

		$tMap->addValidator('LAST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'LAST_NAME');

		$tMap->addValidator('REQID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REQID');

		$tMap->addValidator('REQID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REQID');

		$tMap->addValidator('REQID', 'required', 'propel.validator.RequiredValidator', '', 'REQID');

		$tMap->addValidator('REQID', 'unique', 'propel.validator.UniqueValidator', '', 'REQID');

		$tMap->addValidator('USERNAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'USERNAME');

		$tMap->addValidator('USERNAME', 'required', 'propel.validator.RequiredValidator', '', 'USERNAME');

	} // doBuild()

} // HostRequestMapBuilder
