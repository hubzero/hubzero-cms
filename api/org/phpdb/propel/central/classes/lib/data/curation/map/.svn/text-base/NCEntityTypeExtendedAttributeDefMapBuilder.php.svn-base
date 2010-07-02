<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ENTITY_TYPE_EXTENDED_ATTR_DEF' table to 'NEEScentral' DatabaseMap object.
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
class NCEntityTypeExtendedAttributeDefMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCEntityTypeExtendedAttributeDefMapBuilder';

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

		$tMap = $this->dbMap->addTable('ENTITY_TYPE_EXTENDED_ATTR_DEF');
		$tMap->setPhpName('NCEntityTypeExtendedAttributeDef');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('NTTY_TYP_XTNDD_TTR_DF_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ATTRIBUTE_NAME', 'AttributeName', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('MODIFIED_BY', 'ModifiedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('MODIFIED_DATE', 'ModifiedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('OBJECT_TYPE', 'ObjectType', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addValidator('ATTRIBUTE_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'ATTRIBUTE_NAME');

		$tMap->addValidator('ATTRIBUTE_NAME', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_NAME');

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MODIFIED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_BY', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('MODIFIED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('OBJECT_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'OBJECT_TYPE');

		$tMap->addValidator('OBJECT_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_TYPE');

	} // doBuild()

} // NCEntityTypeExtendedAttributeDefMapBuilder
