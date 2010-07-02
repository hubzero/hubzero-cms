<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'FACILITY_DATA_FILE' table to 'NEEScentral' DatabaseMap object.
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
class FacilityDataFileMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.FacilityDataFileMapBuilder';

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

		$tMap = $this->dbMap->addTable('FACILITY_DATA_FILE');
		$tMap->setPhpName('FacilityDataFile');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('FACILITY_DATA_FILE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('DATA_FILE_ID', 'DataFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addForeignKey('DOC_FORMAT_ID', 'DocFormatId', 'double', CreoleTypes::NUMERIC, 'DOCUMENT_FORMAT', 'DOCUMENT_FORMAT_ID', false, 22);

		$tMap->addForeignKey('DOC_TYPE_ID', 'DocTypeId', 'double', CreoleTypes::NUMERIC, 'DOCUMENT_TYPE', 'DOCUMENT_TYPE_ID', false, 22);

		$tMap->addForeignKey('FACILITY_ID', 'FacilityId', 'double', CreoleTypes::NUMERIC, 'ORGANIZATION', 'ORGID', false, 22);

		$tMap->addColumn('GROUPBY', 'GroupBy', 'string', CreoleTypes::VARCHAR, false, 320);

		$tMap->addColumn('INFO_TYPE', 'InfoType', 'string', CreoleTypes::VARCHAR, false, 320);

		$tMap->addColumn('SUB_INFO_TYPE', 'SubInfoType', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addValidator('DATA_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DATA_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DATA_FILE_ID');

		$tMap->addValidator('DOC_FORMAT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOC_FORMAT_ID');

		$tMap->addValidator('DOC_FORMAT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOC_FORMAT_ID');

		$tMap->addValidator('DOC_FORMAT_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOC_FORMAT_ID');

		$tMap->addValidator('DOC_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOC_TYPE_ID');

		$tMap->addValidator('DOC_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOC_TYPE_ID');

		$tMap->addValidator('DOC_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOC_TYPE_ID');

		$tMap->addValidator('FACILITY_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FACILITY_ID');

		$tMap->addValidator('FACILITY_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FACILITY_ID');

		$tMap->addValidator('FACILITY_ID', 'required', 'propel.validator.RequiredValidator', '', 'FACILITY_ID');

		$tMap->addValidator('GROUPBY', 'maxLength', 'propel.validator.MaxLengthValidator', '320', 'GROUPBY');

		$tMap->addValidator('GROUPBY', 'required', 'propel.validator.RequiredValidator', '', 'GROUPBY');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INFO_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '320', 'INFO_TYPE');

		$tMap->addValidator('INFO_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'INFO_TYPE');

		$tMap->addValidator('SUB_INFO_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'SUB_INFO_TYPE');

		$tMap->addValidator('SUB_INFO_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'SUB_INFO_TYPE');

	} // doBuild()

} // FacilityDataFileMapBuilder
