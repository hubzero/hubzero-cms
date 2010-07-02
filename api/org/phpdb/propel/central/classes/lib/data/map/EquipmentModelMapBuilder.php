<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EQUIPMENT_MODEL' table to 'NEEScentral' DatabaseMap object.
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
class EquipmentModelMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EquipmentModelMapBuilder';

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

		$tMap = $this->dbMap->addTable('EQUIPMENT_MODEL');
		$tMap->setPhpName('EquipmentModel');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('EQUIPMENT_MODEL_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('ADDITIONAL_SPEC_FILE_ID', 'AdditionalSpecFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('ADDITIONAL_SPEC_PAGE_COUNT', 'AdditionalSpecPageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('DESIGN_CONSIDERATION_FILE_ID', 'DesignConsiderationFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('DESIGN_PAGE_COUNT', 'DesignConsiderationPageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('EQUIPMENT_CLASS_ID', 'EquipmentClassId', 'double', CreoleTypes::NUMERIC, 'EQUIPMENT_CLASS', 'EQUIPMENT_CLASS_ID', false, 22);

		$tMap->addForeignKey('INTERFACE_DOC_FILE_ID', 'InterfaceDocFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('INTERFACE_DOC_PAGE_COUNT', 'InterfaceDocPageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MANUFACTURER', 'Manufacturer', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addForeignKey('MANUFACTURER_DOC_FILE_ID', 'ManufacturerDocFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('MANUFACTURER_DOC_PAGE_COUNT', 'ManufacturerDocPageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MODEL_NUMBER', 'ModelNumber', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addForeignKey('SUBCOMPONENTS_DOC_FILE_ID', 'SubcomponentsDocFileId', 'double', CreoleTypes::NUMERIC, 'DATA_FILE', 'ID', false, 22);

		$tMap->addColumn('SUBCOMPONENTS_DOC_PAGE_COUNT', 'SubcomponentsDocPageCount', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('SUPPLIER', 'Supplier', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addValidator('ADDITIONAL_SPEC_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADDITIONAL_SPEC_FILE_ID');

		$tMap->addValidator('ADDITIONAL_SPEC_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADDITIONAL_SPEC_FILE_ID');

		$tMap->addValidator('ADDITIONAL_SPEC_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ADDITIONAL_SPEC_FILE_ID');

		$tMap->addValidator('ADDITIONAL_SPEC_PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ADDITIONAL_SPEC_PAGE_COUNT');

		$tMap->addValidator('ADDITIONAL_SPEC_PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ADDITIONAL_SPEC_PAGE_COUNT');

		$tMap->addValidator('ADDITIONAL_SPEC_PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'ADDITIONAL_SPEC_PAGE_COUNT');

		$tMap->addValidator('DESIGN_CONSIDERATION_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DESIGN_CONSIDERATION_FILE_ID');

		$tMap->addValidator('DESIGN_CONSIDERATION_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DESIGN_CONSIDERATION_FILE_ID');

		$tMap->addValidator('DESIGN_CONSIDERATION_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DESIGN_CONSIDERATION_FILE_ID');

		$tMap->addValidator('DESIGN_PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DESIGN_PAGE_COUNT');

		$tMap->addValidator('DESIGN_PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DESIGN_PAGE_COUNT');

		$tMap->addValidator('DESIGN_PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'DESIGN_PAGE_COUNT');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('EQUIPMENT_CLASS_ID', 'required', 'propel.validator.RequiredValidator', '', 'EQUIPMENT_CLASS_ID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INTERFACE_DOC_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INTERFACE_DOC_FILE_ID');

		$tMap->addValidator('INTERFACE_DOC_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INTERFACE_DOC_FILE_ID');

		$tMap->addValidator('INTERFACE_DOC_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'INTERFACE_DOC_FILE_ID');

		$tMap->addValidator('INTERFACE_DOC_PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INTERFACE_DOC_PAGE_COUNT');

		$tMap->addValidator('INTERFACE_DOC_PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INTERFACE_DOC_PAGE_COUNT');

		$tMap->addValidator('INTERFACE_DOC_PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'INTERFACE_DOC_PAGE_COUNT');

		$tMap->addValidator('MANUFACTURER', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'MANUFACTURER');

		$tMap->addValidator('MANUFACTURER', 'required', 'propel.validator.RequiredValidator', '', 'MANUFACTURER');

		$tMap->addValidator('MANUFACTURER_DOC_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MANUFACTURER_DOC_FILE_ID');

		$tMap->addValidator('MANUFACTURER_DOC_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MANUFACTURER_DOC_FILE_ID');

		$tMap->addValidator('MANUFACTURER_DOC_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'MANUFACTURER_DOC_FILE_ID');

		$tMap->addValidator('MANUFACTURER_DOC_PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MANUFACTURER_DOC_PAGE_COUNT');

		$tMap->addValidator('MANUFACTURER_DOC_PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MANUFACTURER_DOC_PAGE_COUNT');

		$tMap->addValidator('MANUFACTURER_DOC_PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'MANUFACTURER_DOC_PAGE_COUNT');

		$tMap->addValidator('MODEL_NUMBER', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'MODEL_NUMBER');

		$tMap->addValidator('MODEL_NUMBER', 'required', 'propel.validator.RequiredValidator', '', 'MODEL_NUMBER');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SUBCOMPONENTS_DOC_FILE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SUBCOMPONENTS_DOC_FILE_ID');

		$tMap->addValidator('SUBCOMPONENTS_DOC_FILE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SUBCOMPONENTS_DOC_FILE_ID');

		$tMap->addValidator('SUBCOMPONENTS_DOC_FILE_ID', 'required', 'propel.validator.RequiredValidator', '', 'SUBCOMPONENTS_DOC_FILE_ID');

		$tMap->addValidator('SUBCOMPONENTS_DOC_PAGE_COUNT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SUBCOMPONENTS_DOC_PAGE_COUNT');

		$tMap->addValidator('SUBCOMPONENTS_DOC_PAGE_COUNT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SUBCOMPONENTS_DOC_PAGE_COUNT');

		$tMap->addValidator('SUBCOMPONENTS_DOC_PAGE_COUNT', 'required', 'propel.validator.RequiredValidator', '', 'SUBCOMPONENTS_DOC_PAGE_COUNT');

		$tMap->addValidator('SUPPLIER', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'SUPPLIER');

		$tMap->addValidator('SUPPLIER', 'required', 'propel.validator.RequiredValidator', '', 'SUPPLIER');

	} // doBuild()

} // EquipmentModelMapBuilder
