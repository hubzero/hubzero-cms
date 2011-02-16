<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EXPERIMENT' table to 'NEEScentral' DatabaseMap object.
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
class ExperimentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.ExperimentMapBuilder';

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

		$tMap = $this->dbMap->addTable('EXPERIMENT');
		$tMap->setPhpName('Experiment');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EXPERIMENT_SEQ');

		$tMap->addPrimaryKey('EXPID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CURATION_STATUS', 'CurationStatus', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('END_DATE', 'EndDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('EXPERIMENT_DOMAIN_ID', 'ExperimentDomainId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT_DOMAIN', 'ID', false, 22);

		$tMap->addColumn('EXP_TYPE_ID', 'ExperimentTypeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('OBJECTIVE', 'Objective', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('PROJID', 'ProjectId', 'double', CreoleTypes::NUMERIC, 'PROJECT', 'PROJID', false, 22);

		$tMap->addColumn('START_DATE', 'StartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('STATUS', 'Status', 'string', CreoleTypes::VARCHAR, false, 48);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, true, 1020);

		$tMap->addColumn('VIEWABLE', 'View', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addForeignKey('CREATOR_ID', 'CreatorId', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('MODIFIED_BY_ID', 'ModifiedById', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('MODIFIED_DATE', 'ModifiedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('APP_ID', 'AppId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('CURATION_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CURATION_STATUS');

		$tMap->addValidator('CURATION_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'CURATION_STATUS');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('END_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'END_DATE');

		$tMap->addValidator('END_DATE', 'required', 'propel.validator.RequiredValidator', '', 'END_DATE');

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('EXPERIMENT_DOMAIN_ID', 'required', 'propel.validator.RequiredValidator', '', 'EXPERIMENT_DOMAIN_ID');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'unique', 'propel.validator.UniqueValidator', '', 'EXPID');

		$tMap->addValidator('EXP_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXP_TYPE_ID');

		$tMap->addValidator('EXP_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXP_TYPE_ID');

		$tMap->addValidator('EXP_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'EXP_TYPE_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('OBJECTIVE', 'required', 'propel.validator.RequiredValidator', '', 'OBJECTIVE');

		$tMap->addValidator('PROJID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PROJID');

		$tMap->addValidator('PROJID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PROJID');

		$tMap->addValidator('PROJID', 'required', 'propel.validator.RequiredValidator', '', 'PROJID');

		$tMap->addValidator('START_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'START_DATE');

		$tMap->addValidator('START_DATE', 'required', 'propel.validator.RequiredValidator', '', 'START_DATE');

		$tMap->addValidator('STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '48', 'STATUS');

		$tMap->addValidator('STATUS', 'required', 'propel.validator.RequiredValidator', '', 'STATUS');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

		$tMap->addValidator('VIEWABLE', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'VIEWABLE');

		$tMap->addValidator('VIEWABLE', 'required', 'propel.validator.RequiredValidator', '', 'VIEWABLE');

	} // doBuild()

} // ExperimentMapBuilder
