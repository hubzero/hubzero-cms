<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TSUNAMI_BIOLOGICAL_DATA' table to 'NEEScentral' DatabaseMap object.
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
class TsunamiBiologicalDataMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.tsunami.map.TsunamiBiologicalDataMapBuilder';

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

		$tMap = $this->dbMap->addTable('TSUNAMI_BIOLOGICAL_DATA');
		$tMap->setPhpName('TsunamiBiologicalData');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('TSNM_BLGCL_DT_SEQ');

		$tMap->addPrimaryKey('TSUNAMI_BIOLOGICAL_DATA_ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('FAUNA', 'Fauna', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('FLORA', 'Flora', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addColumn('MARINE_BIOLOGY', 'MarineBiology', 'double', CreoleTypes::NUMERIC, false, 22);

		$tMap->addForeignKey('TSUNAMI_DOC_LIB_ID', 'TsunamiDocLibId', 'double', CreoleTypes::NUMERIC, 'TSUNAMI_DOC_LIB', 'TSUNAMI_DOC_LIB_ID', false, 22);

		$tMap->addValidator('FAUNA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FAUNA');

		$tMap->addValidator('FAUNA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FAUNA');

		$tMap->addValidator('FAUNA', 'required', 'propel.validator.RequiredValidator', '', 'FAUNA');

		$tMap->addValidator('FLORA', 'maxValue', 'propel.validator.MaxValueValidator', '', 'FLORA');

		$tMap->addValidator('FLORA', 'notMatch', 'propel.validator.NotMatchValidator', '', 'FLORA');

		$tMap->addValidator('FLORA', 'required', 'propel.validator.RequiredValidator', '', 'FLORA');

		$tMap->addValidator('MARINE_BIOLOGY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MARINE_BIOLOGY');

		$tMap->addValidator('MARINE_BIOLOGY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MARINE_BIOLOGY');

		$tMap->addValidator('MARINE_BIOLOGY', 'required', 'propel.validator.RequiredValidator', '', 'MARINE_BIOLOGY');

		$tMap->addValidator('TSUNAMI_BIOLOGICAL_DATA_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_BIOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_BIOLOGICAL_DATA_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_BIOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_BIOLOGICAL_DATA_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_BIOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_BIOLOGICAL_DATA_ID', 'unique', 'propel.validator.UniqueValidator', '', 'TSUNAMI_BIOLOGICAL_DATA_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TSUNAMI_DOC_LIB_ID');

		$tMap->addValidator('TSUNAMI_DOC_LIB_ID', 'required', 'propel.validator.RequiredValidator', '', 'TSUNAMI_DOC_LIB_ID');

	} // doBuild()

} // TsunamiBiologicalDataMapBuilder
