<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DOC_LIB' table to 'NEEScentral' DatabaseMap object.
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
class DocLibMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.DocLibMapBuilder';

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

		$tMap = $this->dbMap->addTable('DOC_LIB');
		$tMap->setPhpName('DocLib');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('DOC_LIB_SEQ');

		$tMap->addPrimaryKey('DOCID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('AUTHOR_EMAILS', 'AuthorEmails', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('AUTHORS', 'Authors', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('HOW_TO_CITE', 'HowToCite', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('PARENT', 'Parent', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('AUTHORS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHORS');

		$tMap->addValidator('AUTHORS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHORS');

		$tMap->addValidator('AUTHOR_EMAILS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'AUTHOR_EMAILS');

		$tMap->addValidator('AUTHOR_EMAILS', 'required', 'propel.validator.RequiredValidator', '', 'AUTHOR_EMAILS');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DOCID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'DOCID');

		$tMap->addValidator('DOCID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'DOCID');

		$tMap->addValidator('DOCID', 'required', 'propel.validator.RequiredValidator', '', 'DOCID');

		$tMap->addValidator('DOCID', 'unique', 'propel.validator.UniqueValidator', '', 'DOCID');

		$tMap->addValidator('HOW_TO_CITE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'HOW_TO_CITE');

		$tMap->addValidator('HOW_TO_CITE', 'required', 'propel.validator.RequiredValidator', '', 'HOW_TO_CITE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PARENT', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'PARENT');

		$tMap->addValidator('PARENT', 'required', 'propel.validator.RequiredValidator', '', 'PARENT');

		$tMap->addValidator('TITLE', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TITLE');

		$tMap->addValidator('TITLE', 'required', 'propel.validator.RequiredValidator', '', 'TITLE');

	} // doBuild()

} // DocLibMapBuilder
