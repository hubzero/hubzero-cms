<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_EXTENDED_ATTRIBUTES' table to 'NEEScentral' DatabaseMap object.
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
class NCCuratedExtendedAttributesMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedExtendedAttributesMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATED_EXTENDED_ATTRIBUTES');
		$tMap->setPhpName('NCCuratedExtendedAttributes');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CRTD_XTNDD_TTRBTS_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ATTRIBUTE_ID', 'AttributeId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('ATTRIBUTE_NAME', 'AttributeName', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('ATTRIBUTE_VALUE', 'AttributeValue', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, 'CURATED_OBJECTS', 'OBJECT_ID', false, 22);

		$tMap->addValidator('ATTRIBUTE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_ID', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_ID');

		$tMap->addValidator('ATTRIBUTE_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'ATTRIBUTE_NAME');

		$tMap->addValidator('ATTRIBUTE_NAME', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_NAME');

		$tMap->addValidator('ATTRIBUTE_VALUE', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'ATTRIBUTE_VALUE');

		$tMap->addValidator('ATTRIBUTE_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'ATTRIBUTE_VALUE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('OBJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_ID');

	} // doBuild()

} // NCCuratedExtendedAttributesMapBuilder
