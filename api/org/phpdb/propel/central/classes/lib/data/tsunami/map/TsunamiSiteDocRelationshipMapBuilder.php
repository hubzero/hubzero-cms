<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_SITE_DOC_RELATIONSHIP' table to 'NEEScentral' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.data.tsunami.map
 */
class TsunamiSiteDocRelationshipMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiSiteDocRelationshipMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_SITE_DOC_RELATIONSHIP');
		$tMap->setPhpName('TsunamiSiteDocRelationship');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_ST_DC_RLTNSHP_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addForeignKey('TSUNAMI_SITE_ID', 'TsunamiSiteId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_SITE', 'TSUNAMI_SITE_ID', false, 22);

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_SITE_ID');

		$tMap->addValidator('TSUNAMI_SITE_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_SITE_ID');

	} // doBuild()

} // TsunamiSiteDocRelationshipMapBuilder
