<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'LOCATION' table to 'NEEScentral' DatabaseMap object.
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
class LocationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.LocationMapBuilder';

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

		$tMap = $this->dbMap->addTable('LOCATION');
		$tMap->setPhpName('Location');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('LOCATION_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMMENTS', 'Comment', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('COORDINATE_SPACE_ID', 'CoordinateSpaceId', 'double', CreoleTypes::NUMERIC, 'COORDINATE_SPACE', 'ID', false, 22);

		$tMap->addColumn('I', 'I', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('I_UNIT', 'IUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('J', 'J', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('J_UNIT', 'JUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('K', 'K', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('K_UNIT', 'KUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('LABEL', 'Label', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('LOCATION_TYPE_ID', 'LocationTypeId', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addForeignKey('PLAN_ID', 'PlanId', 'double', CreoleTypes::NUMERIC, 'LOCATION_PLAN', 'ID', false, 22);

		$tMap->addForeignKey('SENSOR_TYPE_ID', 'SensorTypeId', 'double', CreoleTypes::NUMERIC, 'SENSOR_TYPE', 'ID', false, 22);

		$tMap->addForeignKey('SOURCE_TYPE_ID', 'SourceTypeId', 'double', CreoleTypes::NUMERIC, 'SOURCE_TYPE', 'ID', false, 22);

		$tMap->addColumn('X', 'X', 'double', CreoleTypes::FLOAT, true, 22);

		$tMap->addForeignKey('X_UNIT', 'XUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('Y', 'Y', 'double', CreoleTypes::FLOAT, true, 22);

		$tMap->addForeignKey('Y_UNIT', 'YUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('Z', 'Z', 'double', CreoleTypes::FLOAT, true, 22);

		$tMap->addForeignKey('Z_UNIT', 'ZUnit', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addValidator('COMMENTS', 'maxLength', 'propel.validator.MaxLengthValidator', '255', 'Comments must be no more than 255 characters in length.');

		$tMap->addValidator('COORDINATE_SPACE_ID', 'required', 'propel.validator.RequiredValidator', '', 'Locations must be associated with a coordinate space.');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'Each location must have an ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'Each location must have a unique ID');

		$tMap->addValidator('I_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'I must be associated with a unit.');

		$tMap->addValidator('J_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'J must be associated with a unit.');

		$tMap->addValidator('K_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'K must be associated with a unit.');

		$tMap->addValidator('LABEL', 'maxLength', 'propel.validator.MaxLengthValidator', '100', 'Labels must be no more than 100 characters in length.');

		$tMap->addValidator('LABEL', 'required', 'propel.validator.RequiredValidator', '', 'Labels are required.');

		$tMap->addValidator('LOCATION_TYPE_ID', 'required', 'propel.validator.RequiredValidator', '', 'Locations must be associated with a Location Type');

		$tMap->addValidator('PLAN_ID', 'required', 'propel.validator.RequiredValidator', '', 'Locations must be associated with a Location Plan');

		$tMap->addValidator('X', 'required', 'propel.validator.RequiredValidator', '', 'X is required.');

		$tMap->addValidator('X_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'X must be associated with a Unit');

		$tMap->addValidator('Y', 'required', 'propel.validator.RequiredValidator', '', 'Y is required');

		$tMap->addValidator('Y_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'Y must be associated with a unit.');

		$tMap->addValidator('Z', 'required', 'propel.validator.RequiredValidator', '', 'Z is required');

		$tMap->addValidator('Z_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'Z must be associated with a unit.');

	} // doBuild()

} // LocationMapBuilder
