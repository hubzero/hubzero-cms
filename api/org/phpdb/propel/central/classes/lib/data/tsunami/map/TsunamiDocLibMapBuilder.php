<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_DOC_LIB' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiDocLibMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiDocLibMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_DOC_LIB');
		$tMap->setPhpName('TsunamiDocLib');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSUNAMI_DOC_LIB_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_DOC_LIB_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('AUTHOR_EMAILS', 'AuthorEmails', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('AUTHORS', 'Authors', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('DIRTY', 'Dirty', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FILE_LOCATION', 'FileLocation', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('FILE_SIZE', 'FileSize', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HOW_TO_CITE', 'HowToCite', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('SPECIFIC_LAT', 'SpecificLatitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('SPECIFIC_LON', 'SpecificLongitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('START_DATE', 'StartDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('TSUNAMI_PROJECT_ID', 'TsunamiProjectId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_PROJECT', 'TSUNAMI_PROJECT_ID', false, 22);

		$tMap->addColumn('TYPE_OF_MATERIAL', 'TypeOfMaterial', 'string', CreoleTypes::VARCHAR, false, 48);

		$tMap->addValidator('AUTHORS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHORS');

		$tMap->addValidator('AUTHORS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHORS');

		$tMap->addValidator('AUTHOR_EMAILS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHOR_EMAILS');

		$tMap->addValidator('AUTHOR_EMAILS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHOR_EMAILS');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DIRTY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DIRTY');

		$tMap->addValidator('DIRTY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DIRTY');

		$tMap->addValidator('DIRTY', 'required', 'propel.validator.RequiredValidator', '', 'DIRTY');

		$tMap->addValidator('FILE_LOCATION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'FILE_LOCATION');

		$tMap->addValidator('FILE_LOCATION', 'required', 'propel.validator.RequiredValidator', '', 'FILE_LOCATION');

		$tMap->addValidator('FILE_SIZE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FILE_SIZE');

		$tMap->addValidator('FILE_SIZE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FILE_SIZE');

		$tMap->addValidator('FILE_SIZE', 'required', 'propel.validator.RequiredValidator', '', 'FILE_SIZE');

		$tMap->addValidator('HOW_TO_CITE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'HOW_TO_CITE');

		$tMap->addValidator('HOW_TO_CITE', 'required', 'propel.validator.RequiredValidator', '', 'HOW_TO_CITE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SPECIFIC_LAT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SPECIFIC_LAT');

		$tMap->addValidator('SPECIFIC_LAT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SPECIFIC_LAT');

		$tMap->addValidator('SPECIFIC_LAT', 'required', 'propel.validator.RequiredValidator', '', 'SPECIFIC_LAT');

		$tMap->addValidator('SPECIFIC_LON', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SPECIFIC_LON');

		$tMap->addValidator('SPECIFIC_LON', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SPECIFIC_LON');

		$tMap->addValidator('SPECIFIC_LON', 'required', 'propel.validator.RequiredValidator', '', 'SPECIFIC_LON');

		$tMap->addValidator('START_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'START_DATE');

		$tMap->addValidator('START_DATE', 'required', 'propel.validator.RequiredValidator', '', 'START_DATE');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TSUNAMI_PROJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_PROJECT_ID');

		$tMap->addValidator('TYPE_OF_MATERIAL', 'maxLength', 'propel.validator.MaxLengthValidator', '48', 'TYPE_OF_MATERIAL');

		$tMap->addValidator('TYPE_OF_MATERIAL', 'required', 'propel.validator.RequiredValidator', '', 'TYPE_OF_MATERIAL');

	} // doBuild()

} // TsunamiDocLibMapBuilder
