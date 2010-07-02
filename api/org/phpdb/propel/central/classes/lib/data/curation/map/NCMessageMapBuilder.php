<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MESSAGE' table to 'NEEScentral' DatabaseMap object.
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
class NCMessageMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCMessageMapBuilder';

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

		$tMap = $this->dbMap->addTable('MESSAGE');
		$tMap->setPhpName('NCMessage');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MESSAGE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1016);

		$tMap->addColumn('KEY_NAME', 'KeyName', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('KEY_VALUE', 'KeyValue', 'string', CreoleTypes::VARCHAR, false, 1016);

		$tMap->addColumn('LANGUAGE', 'Language', 'string', CreoleTypes::VARCHAR, false, 20);

		$tMap->addColumn('NAME_SPACE', 'NameSpace', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addColumn('SHORT_LANGUAGE', 'ShortLanguage', 'string', CreoleTypes::VARCHAR, false, 8);

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1016', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('KEY_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'KEY_NAME');

		$tMap->addValidator('KEY_NAME', 'required', 'propel.validator.RequiredValidator', '', 'KEY_NAME');

		$tMap->addValidator('KEY_VALUE', 'maxLength', 'propel.validator.MaxLengthValidator', '1016', 'KEY_VALUE');

		$tMap->addValidator('KEY_VALUE', 'required', 'propel.validator.RequiredValidator', '', 'KEY_VALUE');

		$tMap->addValidator('LANGUAGE', 'maxLength', 'propel.validator.MaxLengthValidator', '20', 'LANGUAGE');

		$tMap->addValidator('LANGUAGE', 'required', 'propel.validator.RequiredValidator', '', 'LANGUAGE');

		$tMap->addValidator('NAME_SPACE', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME_SPACE');

		$tMap->addValidator('NAME_SPACE', 'required', 'propel.validator.RequiredValidator', '', 'NAME_SPACE');

		$tMap->addValidator('SHORT_LANGUAGE', 'maxLength', 'propel.validator.MaxLengthValidator', '8', 'SHORT_LANGUAGE');

		$tMap->addValidator('SHORT_LANGUAGE', 'required', 'propel.validator.RequiredValidator', '', 'SHORT_LANGUAGE');

	} // doBuild()

} // NCMessageMapBuilder
