<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_PROJECT' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.tsunami.map
 */
class TsunamiProjectMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiProjectMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_PROJECT');
		$tMap->setPhpName('TsunamiProject');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSUNAMI_PROJECT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_PROJECT_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CO_PI', 'CoPi', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CO_PI_INSTITUTION', 'CoPiInstitution', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('COLLABORATORS', 'Collaborators', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CONTACT_EMAIL', 'ContactEmail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CONTACT_NAME', 'ContactName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('NSF_TITLE', 'NsfTitle', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('PI', 'Pi', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('PI_INSTITUTION', 'PiInstitution', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('PUBLIC_DATA', 'PublicData', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SHORT_TITLE', 'ShortTitle', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('SYSADMIN_EMAIL', 'SysadminEmail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SYSADMIN_NAME', 'SysadminName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('VIEWABLE', 'View', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addValidator('COLLABORATORS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COLLABORATORS');

		$tMap->addValidator('COLLABORATORS', 'required', 'propel.validator.RequiredValidator', '', 'COLLABORATORS');

		$tMap->addValidator('CONTACT_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_EMAIL');

		$tMap->addValidator('CONTACT_EMAIL', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_EMAIL');

		$tMap->addValidator('CONTACT_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_NAME');

		$tMap->addValidator('CONTACT_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CONTACT_NAME');

		$tMap->addValidator('CO_PI', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CO_PI');

		$tMap->addValidator('CO_PI', 'required', 'propel.validator.RequiredValidator', '', 'CO_PI');

		$tMap->addValidator('CO_PI_INSTITUTION', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'CO_PI_INSTITUTION');

		$tMap->addValidator('CO_PI_INSTITUTION', 'required', 'propel.validator.RequiredValidator', '', 'CO_PI_INSTITUTION');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('NSF_TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NSF_TITLE');

		$tMap->addValidator('NSF_TITLE', 'required', 'propel.validator.RequiredValidator', '', 'NSF_TITLE');

		$tMap->addValidator('PI', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'PI');

		$tMap->addValidator('PI', 'required', 'propel.validator.RequiredValidator', '', 'PI');

		$tMap->addValidator('PI_INSTITUTION', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'PI_INSTITUTION');

		$tMap->addValidator('PI_INSTITUTION', 'required', 'propel.validator.RequiredValidator', '', 'PI_INSTITUTION');

		$tMap->addValidator('PUBLIC_DATA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PUBLIC_DATA');

		$tMap->addValidator('PUBLIC_DATA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PUBLIC_DATA');

		$tMap->addValidator('PUBLIC_DATA', 'required', 'propel.validator.RequiredValidator', '', 'PUBLIC_DATA');

		$tMap->addValidator('SHORT_TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'SHORT_TITLE');

		$tMap->addValidator('SHORT_TITLE', 'required', 'propel.validator.RequiredValidator', '', 'SHORT_TITLE');

		$tMap->addValidator('STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'STATUS');

		$tMap->addValidator('STATUS', 'required', 'propel.validator.RequiredValidator', '', 'STATUS');

		$tMap->addValidator('SYSADMIN_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_EMAIL');

		$tMap->addValidator('SYSADMIN_EMAIL', 'required', 'propel.validator.RequiredValidator', '', 'SYSADMIN_EMAIL');

		$tMap->addValidator('SYSADMIN_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_NAME');

		$tMap->addValidator('SYSADMIN_NAME', 'required', 'propel.validator.RequiredValidator', '', 'SYSADMIN_NAME');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('VIEWABLE', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'VIEWABLE');

		$tMap->addValidator('VIEWABLE', 'required', 'propel.validator.RequiredValidator', '', 'VIEWABLE');

	} // doBuild()

} // TsunamiProjectMapBuilder
