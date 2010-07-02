<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ONTOLOGY' table to 'NEEScentral' DatabaseMap object.
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
class NCOntologyMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCOntologyMapBuilder';

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

		$tMap = $this->dbMap->addTable('ONTOLOGY');
		$tMap->setPhpName('NCOntology');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ONTOLOGY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('BROADER_TERM', 'BroaderTerm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('COMMENTS', 'Comments', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addColumn('TERM', 'Term', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addValidator('BROADER_TERM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'BROADER_TERM');

		$tMap->addValidator('BROADER_TERM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'BROADER_TERM');

		$tMap->addValidator('BROADER_TERM', 'required', 'propel.validator.RequiredValidator', '', 'BROADER_TERM');

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('TERM', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'TERM');

		$tMap->addValidator('TERM', 'required', 'propel.validator.RequiredValidator', '', 'TERM');

	} // doBuild()

} // NCOntologyMapBuilder
