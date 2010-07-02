<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ENTITY_TYPE_ASSOCIATION_DEF' table to 'NEEScentral' DatabaseMap object.
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
class NCEntityTypeAssociationDefMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCEntityTypeAssociationDefMapBuilder';

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

		$tMap = $this->dbMap->addTable('ENTITY_TYPE_ASSOCIATION_DEF');
		$tMap->setPhpName('NCEntityTypeAssociationDef');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('NTTY_TYP_SSCTN_DF_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ASSOCIATION_VERB', 'AssociationVerb', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('FOR_OBJECT_TYPE', 'ForObjectType', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addColumn('INVERSE_ASSOCIATION_VERB', 'InverseAssociationVerb', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addColumn('MODIFIED_BY', 'ModifiedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('MODIFIED_DATE', 'ModifiedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('TO_OBJECT_TYPE', 'ToObjectType', 'string', CreoleTypes::VARCHAR, false, 160);

		$tMap->addValidator('ASSOCIATION_VERB', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'ASSOCIATION_VERB');

		$tMap->addValidator('ASSOCIATION_VERB', 'required', 'propel.validator.RequiredValidator', '', 'ASSOCIATION_VERB');

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('FOR_OBJECT_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'FOR_OBJECT_TYPE');

		$tMap->addValidator('FOR_OBJECT_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'FOR_OBJECT_TYPE');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('INVERSE_ASSOCIATION_VERB', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'INVERSE_ASSOCIATION_VERB');

		$tMap->addValidator('INVERSE_ASSOCIATION_VERB', 'required', 'propel.validator.RequiredValidator', '', 'INVERSE_ASSOCIATION_VERB');

		$tMap->addValidator('MODIFIED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_BY', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_BY');

		$tMap->addValidator('MODIFIED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('MODIFIED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'MODIFIED_DATE');

		$tMap->addValidator('TO_OBJECT_TYPE', 'maxLength', 'propel.validator.MaxLengthValidator', '160', 'TO_OBJECT_TYPE');

		$tMap->addValidator('TO_OBJECT_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'TO_OBJECT_TYPE');

	} // doBuild()

} // NCEntityTypeAssociationDefMapBuilder
