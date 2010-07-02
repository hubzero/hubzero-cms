<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DOCUMENT_FORMAT' table to 'NEEScentral' DatabaseMap object.
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
class DocumentFormatMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DocumentFormatMapBuilder';

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

		$tMap = $this->dbMap->addTable('DOCUMENT_FORMAT');
		$tMap->setPhpName('DocumentFormat');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DOCUMENT_FORMAT_SEQ');

		$tMap->addPrimaryKey('DOCUMENT_FORMAT_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DEFAULT_EXTENSION', 'DefaultExtension', 'string', CreoleTypes::VARCHAR, false, 320);

		$tMap->addColumn('FORMAT', 'Format', 'string', CreoleTypes::VARCHAR, false, 64);

		$tMap->addColumn('MIME_TYPE', 'MimeType', 'string', CreoleTypes::VARCHAR, false, 320);

		$tMap->addValidator('DEFAULT_EXTENSION', 'maxLength', 'propel.validator.MaxLengthValidator', '320', 'DEFAULT_EXTENSION');

		$tMap->addValidator('DEFAULT_EXTENSION', 'required', 'propel.validator.RequiredValidator', '', 'DEFAULT_EXTENSION');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'required', 'propel.validator.RequiredValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('DOCUMENT_FORMAT_ID', 'unique', 'propel.validator.UniqueValidator', '', 'DOCUMENT_FORMAT_ID');

		$tMap->addValidator('FORMAT', 'maxLength', 'propel.validator.MaxLengthValidator', '64', 'FORMAT');

		$tMap->addValidator('FORMAT', 'required', 'propel.validator.RequiredValidator', '', 'FORMAT');

		$tMap->addValidator('MIME_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '320', 'MIME_TYPE');

		$tMap->addValidator('MIME_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'MIME_TYPE');

	} // doBuild()

} // DocumentFormatMapBuilder
