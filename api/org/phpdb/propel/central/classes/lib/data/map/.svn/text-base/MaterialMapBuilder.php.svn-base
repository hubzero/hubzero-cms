<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'MATERIAL' table to 'NEEScentral' DatabaseMap object.
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
class MaterialMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.MaterialMapBuilder';

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

		$tMap = $this->dbMap->addTable('MATERIAL');
		$tMap->setPhpName('Material');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('MATERIAL_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addForeignKey('MATERIAL_TYPE_ID', 'MaterialTypeId', 'double', CreoleTypes::NUMERIC, 'MATERIAL_TYPE', 'ID', false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 400);

		$tMap->addForeignKey('PROTOTYPE_MATERIAL_ID', 'PrototypeMaterialId', 'double', CreoleTypes::NUMERIC, 'MATERIAL', 'ID', false, 22);

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('MATERIAL_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'MATERIAL_TYPE_ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '400', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PROTOTYPE_MATERIAL_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PROTOTYPE_MATERIAL_ID');

		$tMap->addValidator('PROTOTYPE_MATERIAL_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PROTOTYPE_MATERIAL_ID');

		$tMap->addValidator('PROTOTYPE_MATERIAL_ID', 'required', 'propel.validator.RequiredValidator', '', 'PROTOTYPE_MATERIAL_ID');

	} // doBuild()

} // MaterialMapBuilder
