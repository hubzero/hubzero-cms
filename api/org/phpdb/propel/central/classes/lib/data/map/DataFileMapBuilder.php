<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DATA_FILE' table to 'NEEScentral' DatabaseMap object.
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
class DataFileMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DataFileMapBuilder';

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

		$tMap = $this->dbMap->addTable('DATA_FILE');
		$tMap->setPhpName('DataFile');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DATA_FILE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('AUTHOR_EMAILS', 'AuthorEmails', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('AUTHORS', 'Authors', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('CHECKSUM', 'Checksum', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addColumn('CREATED', 'Created', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('CURATION_STATUS', 'CurationStatus', 'string', CreoleTypes::VARCHAR, false, 40);

		$tMap->addColumn('DELETED', 'Deleted', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('DIRECTORY', 'Directory', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FILESIZE', 'Filesize', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('HOW_TO_CITE', 'HowToCite', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('PAGE_COUNT', 'PageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('PATH', 'Path', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('VIEWABLE', 'View', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addForeignKey('THUMB_ID', 'ThumbId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addForeignKey('DOCUMENT_FORMAT_ID', 'DocumentFormatId', 'double', CreoleTypes::NUMERIC, 'DOCUMENT_FORMAT', 'DOCUMENT_FORMAT_ID', false, 22);

		$tMap->addColumn('OPENING_TOOL', 'OpeningTool', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addForeignKey('USAGE_TYPE_ID', 'UsageTypeId', 'double', CreoleTypes::NUMERIC, 'ENTITY_TYPE', 'ID', false, 22);

		$tMap->addForeignKey('CREATOR_ID', 'CreatorId', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('MODIFIED_BY_ID', 'ModifiedById', 'double', CreoleTypes::NUMERIC, 'PERSON', 'ID', false, 22);

		$tMap->addColumn('MODIFIED_DATE', 'ModifiedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('APP_ID', 'AppId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('AUTHORS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHORS');

		$tMap->addValidator('AUTHORS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHORS');

		$tMap->addValidator('AUTHOR_EMAILS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHOR_EMAILS');

		$tMap->addValidator('AUTHOR_EMAILS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHOR_EMAILS');

		$tMap->addValidator('CHECKSUM', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'CHECKSUM');

		$tMap->addValidator('CHECKSUM', 'required', 'propel.validator.RequiredValidator', '', 'CHECKSUM');

		$tMap->addValidator('CREATED', 'required', 'propel.validator.RequiredValidator', '', 'CREATED');

		$tMap->addValidator('CURATION_STATUS', 'maxLength', 'propel.validator.MaxLengthValidator', '40', 'CURATION_STATUS');

		$tMap->addValidator('CURATION_STATUS', 'required', 'propel.validator.RequiredValidator', '', 'CURATION_STATUS');

		$tMap->addValidator('DELETED', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DELETED');

		$tMap->addValidator('DELETED', 'required', 'propel.validator.RequiredValidator', '', 'DELETED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DIRECTORY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DIRECTORY');

		$tMap->addValidator('DIRECTORY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DIRECTORY');

		$tMap->addValidator('DIRECTORY', 'required', 'propel.validator.RequiredValidator', '', 'DIRECTORY');

		$tMap->addValidator('FILESIZE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FILESIZE');

		$tMap->addValidator('FILESIZE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FILESIZE');

		$tMap->addValidator('FILESIZE', 'required', 'propel.validator.RequiredValidator', '', 'FILESIZE');

		$tMap->addValidator('HOW_TO_CITE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'HOW_TO_CITE');

		$tMap->addValidator('HOW_TO_CITE', 'required', 'propel.validator.RequiredValidator', '', 'HOW_TO_CITE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PATH', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'PATH');

		$tMap->addValidator('PATH', 'required', 'propel.validator.RequiredValidator', '', 'PATH');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

		$tMap->addValidator('VIEWABLE', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'VIEWABLE');

		$tMap->addValidator('VIEWABLE', 'required', 'propel.validator.RequiredValidator', '', 'VIEWABLE');

	} // doBuild()

} // DataFileMapBuilder
