<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_OBJECTS' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.curation.map
 */
class NCCuratedObjectsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedObjectsMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATED_OBJECTS');
		$tMap->setPhpName('NCCuratedObjects');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CURATED_OBJECTS_SEQ');

		$tMap->addPrimaryKey('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CONFORMANCE_LEVEL', 'ConformanceLevel', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('CURATION_STATE', 'CurationState', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('INITIAL_CURATION_DATE', 'InitialCurationDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('LINK', 'Link', 'string', CreoleTypes::VARCHAR, false, 1016);

		$tMap->addColumn('MODIFIED_BY', 'ModifiedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('MODIFIED_DATE', 'ModifiedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('OBJECT_CREATION_DATE', 'ObjectCreationDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('OBJECT_STATUS', 'ObjectStatus', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('OBJECT_TYPE', 'ObjectType', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('OBJECT_VISIBILITY', 'ObjectVisibility', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('SHORT_TITLE', 'ShortTitle', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, false, 1016);

		$tMap->addColumn('VERSION', 'Version', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addValidator('CONFORMANCE_LEVEL', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'CONFORMANCE_LEVEL');

		$tMap->addValidator('CONFORMANCE_LEVEL', 'required', 'propel.validator.RequiredValidator', '', 'CONFORMANCE_LEVEL');

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CURATION_STATE', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'CURATION_STATE');

		$tMap->addValidator('CURATION_STATE', 'required', 'propel.validator.RequiredValidator', '', 'CURATION_STATE');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('INITIAL_CURATION_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INITIAL_CURATION_DATE');

		$tMap->addValidator('INITIAL_CURATION_DATE', 'required', 'propel.validator.RequiredValidator', '', 'INITIAL_CURATION_DATE');

		$tMap->addValidator('LINK', 'maxLength', 'propel.validator.MaxLengthValidator', '1016', 'LINK');

		$tMap->addValidator('LINK', 'required', 'propel.validator.RequiredValidator', '', 'LINK');

		$tMap->addValidator('MODIFIED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_BY', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('MODIFIED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('OBJECT_CREATION_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_CREATION_DATE');

		$tMap->addValidator('OBJECT_CREATION_DATE', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_CREATION_DATE');

		$tMap->addValidator('OBJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'unique', 'propel.validator.UniqueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'OBJECT_STATUS');

		$tMap->addValidator('OBJECT_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_STATUS');

		$tMap->addValidator('OBJECT_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'OBJECT_TYPE');

		$tMap->addValidator('OBJECT_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_TYPE');

		$tMap->addValidator('OBJECT_VISIBILITY', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'OBJECT_VISIBILITY');

		$tMap->addValidator('OBJECT_VISIBILITY', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_VISIBILITY');

		$tMap->addValidator('SHORT_TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'SHORT_TITLE');

		$tMap->addValidator('SHORT_TITLE', 'required', 'propel.validator.RequiredValidator', '', 'SHORT_TITLE');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1016', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

		$tMap->addValidator('VERSION', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'VERSION');

		$tMap->addValidator('VERSION', 'required', 'propel.validator.RequiredValidator', '', 'VERSION');

	} // doBuild()

} // NCCuratedObjectsMapBuilder
