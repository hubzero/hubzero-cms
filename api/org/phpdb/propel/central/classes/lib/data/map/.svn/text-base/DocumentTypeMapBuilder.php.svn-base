<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DOCUMENT_TYPE' table to 'NEEScentral' DatabaseMap object.
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
class DocumentTypeMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DocumentTypeMapBuilder';

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

		$tMap = $this->dbMap->addTable('DOCUMENT_TYPE');
		$tMap->setPhpName('DocumentType');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DOCUMENT_TYPE_SEQ');

		$tMap->addPrimaryKey('DOCUMENT_TYPE_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addColumn('PREFERRED_FORMAT', 'PreferredFormat', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('DOCUMENT_TYPE_ID', 'unique', 'propel.validator.UniqueValidator', '', 'DOCUMENT_TYPE_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PREFERRED_FORMAT', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'PREFERRED_FORMAT');

		$tMap->addValidator('PREFERRED_FORMAT', 'required', 'propel.validator.RequiredValidator', '', 'PREFERRED_FORMAT');

	} // doBuild()

} // DocumentTypeMapBuilder
