<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ENTITY_TYPE' table to 'NEEScentral' DatabaseMap object.
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
class EntityTypeMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.EntityTypeMapBuilder';

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

		$tMap = $this->dbMap->addTable('ENTITY_TYPE');
		$tMap->setPhpName('EntityType');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('ENTITY_TYPE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('CLASS_NAME', 'ClassName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('N_TABLE_NAME', 'DatabaseTableName', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('TABLE_ID_COLUMN', 'TableIdColumn', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addValidator('CLASS_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'CLASS_NAME');

		$tMap->addValidator('CLASS_NAME', 'required', 'propel.validator.RequiredValidator', '', 'CLASS_NAME');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('N_TABLE_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'N_TABLE_NAME');

		$tMap->addValidator('N_TABLE_NAME', 'required', 'propel.validator.RequiredValidator', '', 'N_TABLE_NAME');

		$tMap->addValidator('TABLE_ID_COLUMN', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'TABLE_ID_COLUMN');

		$tMap->addValidator('TABLE_ID_COLUMN', 'required', 'propel.validator.RequiredValidator', '', 'TABLE_ID_COLUMN');

	} // doBuild()

} // EntityTypeMapBuilder
