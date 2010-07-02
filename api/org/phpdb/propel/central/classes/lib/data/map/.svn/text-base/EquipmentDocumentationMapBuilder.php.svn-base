<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT_DOCUMENTATION' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentDocumentationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentDocumentationMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT_DOCUMENTATION');
		$tMap->setPhpName('EquipmentDocumentation');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('QPMNT_DCMNTTN_SEQ');

		$tMap->addPrimaryKey('EQUIPMENT_DOC_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('DOCUMENT_FORMAT_ID', 'DocumentFormatId', 'double', CreoleTypes::NUMERIC, 'DOCUMENT_FORMAT', 'DOCUMENT_FORMAT_ID', false, 22);

		$tMap->addForeignKey('DOCUMENT_TYPE_ID', 'DocumentTypeId', 'double', CreoleTypes::NUMERIC, 'DOCUMENT_TYPE', 'DOCUMENT_TYPE_ID', false, 22);

		$tMap->addForeignKey('DOCUMENTATION_FILE_ID', 'DocumentationFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addForeignKey('EQUIPMENT_ID', 'EquipmentId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT', 'EQUIPMENT_ID', false, 22);

		$tMap->addColumn('LAST_MODIFIED', 'LastModified', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PAGE_COUNT', 'PageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DOCUMENTATION_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCUMENTATION_FILE_ID');

		$tMap->addValidator('DOCUMENTATION_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCUMENTATION_FILE_ID');

		$tMap->addValidator('DOCUMENTATION_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOCUMENTATION_FILE_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('EQUIPMENT_DOC_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_DOC_ID');

		$tMap->addValidator('EQUIPMENT_DOC_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_DOC_ID');

		$tMap->addValidator('EQUIPMENT_DOC_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_DOC_ID');

		$tMap->addValidator('EQUIPMENT_DOC_ID', 'unique', 'propel.validator.UniqueValidator', '', 'EQUIPMENT_DOC_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('EQUIPMENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_ID');

		$tMap->addValidator('LAST_MODIFIED', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LAST_MODIFIED');

		$tMap->addValidator('LAST_MODIFIED', 'required', 'propel.validator.RequiredValidator', '', 'LAST_MODIFIED');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PAGE_COUNT');

		$tMap->addValidator('PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'PAGE_COUNT');

	} // doBuild()

} // EquipmentDocumentationMapBuilder
