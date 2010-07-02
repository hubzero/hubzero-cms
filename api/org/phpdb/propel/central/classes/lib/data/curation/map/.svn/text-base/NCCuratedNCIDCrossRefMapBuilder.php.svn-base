<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CURATEDNCIDCROSS_REF' table to 'NEEScentral' DatabaseMap object.
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
class NCCuratedNCIDCrossRefMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.curation.map.NCCuratedNCIDCrossRefMapBuilder';

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

		$tMap = $this->dbMap->addTable('CURATEDNCIDCROSS_REF');
		$tMap->setPhpName('NCCuratedNCIDCrossRef');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('CURATEDNCIDCROSS_REF_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CREATED_BY', 'CreatedBy', 'string', CreoleTypes::VARCHAR, false, 80);

		$tMap->addColumn('CREATED_DATE', 'CreatedDate', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('CURATED_ENTITYID', 'CuratedEntityId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NEESCENTRAL_OBJECTID', 'NEEScentralObjectId', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('NEESCENTRAL_TABLE_SOURCE', 'NEEScentralTableSource', 'string', CreoleTypes::VARCHAR, false, 120);

		$tMap->addValidator('CREATED_BY', 'maxLength', 'propel.validator.MaxLengthValidator', '80', 'CREATED_BY');

		$tMap->addValidator('CREATED_BY', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_BY');

		$tMap->addValidator('CREATED_DATE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CREATED_DATE', 'required', 'propel.validator.RequiredValidator', '', 'CREATED_DATE');

		$tMap->addValidator('CURATED_ENTITYID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'CURATED_ENTITYID');

		$tMap->addValidator('CURATED_ENTITYID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'CURATED_ENTITYID');

		$tMap->addValidator('CURATED_ENTITYID', 'required', 'propel.validator.RequiredValidator', '', 'CURATED_ENTITYID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NEESCENTRAL_OBJECTID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'NEESCENTRAL_OBJECTID');

		$tMap->addValidator('NEESCENTRAL_OBJECTID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'NEESCENTRAL_OBJECTID');

		$tMap->addValidator('NEESCENTRAL_OBJECTID', 'required', 'propel.validator.RequiredValidator', '', 'NEESCENTRAL_OBJECTID');

		$tMap->addValidator('NEESCENTRAL_TABLE_SOURCE', 'maxLength', 'propel.validator.MaxLengthValidator', '120', 'NEESCENTRAL_TABLE_SOURCE');

		$tMap->addValidator('NEESCENTRAL_TABLE_SOURCE', 'required', 'propel.validator.RequiredValidator', '', 'NEESCENTRAL_TABLE_SOURCE');

	} // doBuild()

} // NCCuratedNCIDCrossRefMapBuilder
