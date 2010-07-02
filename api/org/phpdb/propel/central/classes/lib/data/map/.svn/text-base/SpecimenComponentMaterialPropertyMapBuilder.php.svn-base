<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SPECCOMP_MATERIAL_PROPERTY' table to 'NEEScentral' DatabaseMap object.
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
class SpecimenComponentMaterialPropertyMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SpecimenComponentMaterialPropertyMapBuilder';

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

		$tMap = $this->dbMap->addTable('SPECCOMP_MATERIAL_PROPERTY');
		$tMap->setPhpName('SpecimenComponentMaterialProperty');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SPECCOMP_MATERIAL_PROPERTY_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('SPECIMEN_COMPONENT_MATERIAL_ID', 'SpecimenComponentMaterialId', 'double', CreoleTypes::NUMERIC, 'SPECCOMP_MATERIAL', 'ID', true, 22);

		$tMap->addForeignKey('MATERIAL_TYPE_PROPERTY_ID', 'MaterialTypePropertyId', 'double', CreoleTypes::NUMERIC, 'MATERIAL_TYPE_PROPERTY', 'ID', true, 22);

		$tMap->addForeignKey('MEASUREMENT_UNIT_ID', 'MeasurementUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('VALUE', 'Value', 'string', CreoleTypes::VARCHAR, false, 100);

	} // doBuild()

} // SpecimenComponentMaterialPropertyMapBuilder
