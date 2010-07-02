<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'PROJECT' table to 'NEEScentral' DatabaseMap object.
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
class ProjectMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ProjectMapBuilder';

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

		$tMap = $this->dbMap->addTable('PROJECT');
		$tMap->setPhpName('Project');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('PROJECT_SEQ');

		$tMap->addPrimaryKey('PROJID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CONTACT_EMAIL', 'ContactEmail', 'string', CreoleTypes::VARCHAR, true, 1020);

		$tMap->addColumn('CONTACT_NAME', 'ContactName', 'string', CreoleTypes::VARCHAR, true, 1020);

		$tMap->addColumn('CURATION_STATUS', 'CurationStatus', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('END_DATE', 'EndDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('FUNDORG', 'Fundorg', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('FUNDORGPROJID', 'FundorgProjId', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('NEES', 'NEES', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NSFTITLE', 'NSFTitle', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('NICKNAME', 'Nickname', 'string', CreoleTypes::VARCHAR, true, 400);

		$tMap->addColumn('SHORT_TITLE', 'ShortTitle', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('START_DATE', 'StartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, false, 44);

		$tMap->addColumn('SYSADMIN_EMAIL', 'SysadminEmail', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SYSADMIN_NAME', 'SysadminName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, true, 1020);

		$tMap->addColumn('VIEWABLE', 'View', 'string', CreoleTypes::VARCHAR, false, 28);

		$tMap->addForeignKey('CREATOR_ID', 'CreatorId', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addForeignKey('SUPER_PROJECT_ID', 'SuperProjectId', 'double', CreoleTypes::NUMERIC, 'PROJECT', 'PROJID', false, 22);

		$tMap->addColumn('PROJECT_TYPE_ID', 'ProjectTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('CONTACT_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_EMAIL');

		$tMap->addValidator('CONTACT_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CONTACT_NAME');

		$tMap->addValidator('CURATION_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CURATION_STATUS');

		$tMap->addValidator('FUNDORG', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'FUNDORG');

		$tMap->addValidator('FUNDORGPROJID', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'FUNDORGPROJID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('NICKNAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'NICKNAME');

		$tMap->addValidator('NSFTITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NSFTITLE');

		$tMap->addValidator('PROJID', 'unique', 'propel.validator.UniqueValidator', '', 'PROJID');

		$tMap->addValidator('SHORT_TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'SHORT_TITLE');

		$tMap->addValidator('STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '44', 'STATUS');

		$tMap->addValidator('SYSADMIN_EMAIL', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_EMAIL');

		$tMap->addValidator('SYSADMIN_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'SYSADMIN_NAME');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('VIEWABLE', 'maxLength', 'propel.validator.MaxLengthValidator', '28', 'VIEWABLE');

	} // doBuild()

} // ProjectMapBuilder
