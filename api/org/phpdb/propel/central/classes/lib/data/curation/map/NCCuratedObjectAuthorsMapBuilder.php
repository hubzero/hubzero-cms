<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_OBJECT_AUTHORS' table to 'NEEScentral' DatabaseMap object.
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
class NCCuratedObjectAuthorsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedObjectAuthorsMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATED_OBJECT_AUTHORS');
		$tMap->setPhpName('NCCuratedObjectAuthors');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CRTD_BJCT_THRS_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('AUTHOR_TYPE', 'AuthorType', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('FIRST_NAME', 'FirstName', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('LAST_NAME', 'LastName', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('MIDDLE_NAME', 'MiddleName', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addForeignKey('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, 'CURATED_OBJECTS', 'OBJECT_ID', false, 22);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, false, 1016);

		$tMap->addValidator('AUTHOR_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'AUTHOR_TYPE');

		$tMap->addValidator('AUTHOR_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'AUTHOR_TYPE');

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('FIRST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'FIRST_NAME');

		$tMap->addValidator('FIRST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'FIRST_NAME');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('LAST_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'LAST_NAME');

		$tMap->addValidator('LAST_NAME', 'required', 'propel.validator.RequiredValidator', '', 'LAST_NAME');

		$tMap->addValidator('MIDDLE_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'MIDDLE_NAME');

		$tMap->addValidator('MIDDLE_NAME', 'required', 'propel.validator.RequiredValidator', '', 'MIDDLE_NAME');

		$tMap->addValidator('OBJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_ID');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1016', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

	} // doBuild()

} // NCCuratedObjectAuthorsMapBuilder
