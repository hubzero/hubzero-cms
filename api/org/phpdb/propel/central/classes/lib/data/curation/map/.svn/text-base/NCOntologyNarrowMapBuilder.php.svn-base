<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ONTOLOGY_NARROW' table to 'NEEScentral' DatabaseMap object.
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
class NCOntologyNarrowMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCOntologyNarrowMapBuilder';

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

		$tMap = $this->dbMap->addTable('ONTOLOGY_NARROW');
		$tMap->setPhpName('NCOntologyNarrow');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ONTOLOGY_NARROW_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('NARROWER_TERM', 'NarrowerTerm', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('TERM_ID', 'TermId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NARROWER_TERM', 'maxValue', 'propel.validator.MaxValueValidator', '', 'NARROWER_TERM');

		$tMap->addValidator('NARROWER_TERM', 'notMatch', 'propel.validator.NotMatchValidator', '', 'NARROWER_TERM');

		$tMap->addValidator('NARROWER_TERM', 'required', 'propel.validator.RequiredValidator', '', 'NARROWER_TERM');

		$tMap->addValidator('TERM_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TERM_ID');

		$tMap->addValidator('TERM_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TERM_ID');

		$tMap->addValidator('TERM_ID', 'required', 'propel.validator.RequiredValidator', '', 'TERM_ID');

	} // doBuild()

} // NCOntologyNarrowMapBuilder
