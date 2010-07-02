<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATED_OBJECT_ASSOCIATIONS' table to 'NEEScentral' DatabaseMap object.
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
class NCCuratedObjectAssociationsMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedObjectAssociationsMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATED_OBJECT_ASSOCIATIONS');
		$tMap->setPhpName('NCCuratedObjectAssociations');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CRTD_BJCT_SSCTNS_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('INVERSE_OF', 'InverseOf', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('REFERENCE_FOR_OBJECT', 'ReferenceForObject', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('REFERENCE_TO_OBJECT', 'ReferenceToObject', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('VERB', 'Verb', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INVERSE_OF', 'maxValue', 'propel.validator.MaxValueValidator', '', 'INVERSE_OF');

		$tMap->addValidator('INVERSE_OF', 'notMatch', 'propel.validator.NotMatchValidator', '', 'INVERSE_OF');

		$tMap->addValidator('INVERSE_OF', 'required', 'propel.validator.RequiredValidator', '', 'INVERSE_OF');

		$tMap->addValidator('REFERENCE_FOR_OBJECT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REFERENCE_FOR_OBJECT');

		$tMap->addValidator('REFERENCE_FOR_OBJECT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REFERENCE_FOR_OBJECT');

		$tMap->addValidator('REFERENCE_FOR_OBJECT', 'required', 'propel.validator.RequiredValidator', '', 'REFERENCE_FOR_OBJECT');

		$tMap->addValidator('REFERENCE_TO_OBJECT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'REFERENCE_TO_OBJECT');

		$tMap->addValidator('REFERENCE_TO_OBJECT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'REFERENCE_TO_OBJECT');

		$tMap->addValidator('REFERENCE_TO_OBJECT', 'required', 'propel.validator.RequiredValidator', '', 'REFERENCE_TO_OBJECT');

		$tMap->addValidator('VERB', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'VERB');

		$tMap->addValidator('VERB', 'required', 'propel.validator.RequiredValidator', '', 'VERB');

	} // doBuild()

} // NCCuratedObjectAssociationsMapBuilder
