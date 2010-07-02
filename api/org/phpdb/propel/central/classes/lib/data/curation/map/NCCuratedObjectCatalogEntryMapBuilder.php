<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_OBJECT_CATALOG_ENTRY' table to 'NEEScentral' DatabaseMap object.
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
class NCCuratedObjectCatalogEntryMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedObjectCatalogEntryMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATED_OBJECT_CATALOG_ENTRY');
		$tMap->setPhpName('NCCuratedObjectCatalogEntry');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CRTD_BJCT_CTLG_NTRY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addForeignKey('OBJECT_ID', 'ObjectId', 'double', CreoleTypes::NUMERIC, 'CURATED_OBJECTS', 'OBJECT_ID', false, 22);

		$tMap->addColumn('ONTOLOGY_TERM', 'OntologyTerm', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('RELEVANCE_LEVEL', 'RelevanceLevel', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('OBJECT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'OBJECT_ID');

		$tMap->addValidator('OBJECT_ID', 'required', 'propel.validator.RequiredValidator', '', 'OBJECT_ID');

		$tMap->addValidator('ONTOLOGY_TERM', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'ONTOLOGY_TERM');

		$tMap->addValidator('ONTOLOGY_TERM', 'required', 'propel.validator.RequiredValidator', '', 'ONTOLOGY_TERM');

		$tMap->addValidator('RELEVANCE_LEVEL', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'RELEVANCE_LEVEL');

		$tMap->addValidator('RELEVANCE_LEVEL', 'required', 'propel.validator.RequiredValidator', '', 'RELEVANCE_LEVEL');

	} // doBuild()

} // NCCuratedObjectCatalogEntryMapBuilder
