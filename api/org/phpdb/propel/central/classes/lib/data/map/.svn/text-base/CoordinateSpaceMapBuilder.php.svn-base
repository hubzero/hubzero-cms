<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'COORDINATE_SPACE' table to 'NEEScentral' DatabaseMap object.
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
class CoordinateSpaceMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.CoordinateSpaceMapBuilder';

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

		$tMap = $this->dbMap->addTable('COORDINATE_SPACE');
		$tMap->setPhpName('CoordinateSpace');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('COORDINATE_SPACE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('ALTITUDE', 'Altitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('ALTITUDE_UNIT', 'AltitudeUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('DATE_CREATED', 'DateCreated', 'int', CreoleTypes::DATE, false, null);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::CLOB, false, null);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addColumn('LATITUDE', 'Latitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('LONGITUDE', 'Longitude', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 200);

		$tMap->addForeignKey('PARENT_ID', 'ParentId', 'double', CreoleTypes::NUMERIC, 'COORDINATE_SPACE', 'ID', false, 22);

		$tMap->addColumn('ROTATIONX', 'RotationX', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('ROTATIONXUNIT_ID', 'RotationXUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('ROTATIONY', 'RotationY', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('ROTATIONYUNIT_ID', 'RotationYUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('ROTATIONZ', 'RotationZ', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('ROTATIONZUNIT_ID', 'RotationZUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('SCALE', 'Scale', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('SYSTEM_ID', 'SystemId', 'double', CreoleTypes::NUMERIC, 'COORDINATE_SYSTEM', 'ID', false, 22);

		$tMap->addColumn('TRANSLATIONX', 'TranslationX', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('TRANSLATIONXUNIT_ID', 'TranslationXUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('TRANSLATIONY', 'TranslationY', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('TRANSLATIONYUNIT_ID', 'TranslationYUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addColumn('TRANSLATIONZ', 'TranslationZ', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addForeignKey('TRANSLATIONZUNIT_ID', 'TranslationZUnitId', 'double', CreoleTypes::NUMERIC, 'MEASUREMENT_UNIT', 'ID', false, 22);

		$tMap->addValidator('ALTITUDE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ALTITUDE');

		$tMap->addValidator('ALTITUDE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ALTITUDE');

		$tMap->addValidator('ALTITUDE', 'required', 'propel.validator.RequiredValidator', '', 'ALTITUDE');

		$tMap->addValidator('ALTITUDE_UNIT', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ALTITUDE_UNIT');

		$tMap->addValidator('ALTITUDE_UNIT', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ALTITUDE_UNIT');

		$tMap->addValidator('ALTITUDE_UNIT', 'required', 'propel.validator.RequiredValidator', '', 'ALTITUDE_UNIT');

		$tMap->addValidator('DATE_CREATED', 'required', 'propel.validator.RequiredValidator', '', 'DATE_CREATED');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('LATITUDE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LATITUDE');

		$tMap->addValidator('LATITUDE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LATITUDE');

		$tMap->addValidator('LATITUDE', 'required', 'propel.validator.RequiredValidator', '', 'LATITUDE');

		$tMap->addValidator('LONGITUDE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'LONGITUDE');

		$tMap->addValidator('LONGITUDE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'LONGITUDE');

		$tMap->addValidator('LONGITUDE', 'required', 'propel.validator.RequiredValidator', '', 'LONGITUDE');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '200', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('PARENT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'PARENT_ID');

		$tMap->addValidator('PARENT_ID', 'required', 'propel.validator.RequiredValidator', '', 'PARENT_ID');

		$tMap->addValidator('ROTATIONX', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONX');

		$tMap->addValidator('ROTATIONX', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONX');

		$tMap->addValidator('ROTATIONX', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONX');

		$tMap->addValidator('ROTATIONXUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONXUNIT_ID');

		$tMap->addValidator('ROTATIONXUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONXUNIT_ID');

		$tMap->addValidator('ROTATIONXUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONXUNIT_ID');

		$tMap->addValidator('ROTATIONY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONY');

		$tMap->addValidator('ROTATIONY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONY');

		$tMap->addValidator('ROTATIONY', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONY');

		$tMap->addValidator('ROTATIONYUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONYUNIT_ID');

		$tMap->addValidator('ROTATIONYUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONYUNIT_ID');

		$tMap->addValidator('ROTATIONYUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONYUNIT_ID');

		$tMap->addValidator('ROTATIONZ', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONZ');

		$tMap->addValidator('ROTATIONZ', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONZ');

		$tMap->addValidator('ROTATIONZ', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONZ');

		$tMap->addValidator('ROTATIONZUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ROTATIONZUNIT_ID');

		$tMap->addValidator('ROTATIONZUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ROTATIONZUNIT_ID');

		$tMap->addValidator('ROTATIONZUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'ROTATIONZUNIT_ID');

		$tMap->addValidator('SCALE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SCALE');

		$tMap->addValidator('SCALE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SCALE');

		$tMap->addValidator('SCALE', 'required', 'propel.validator.RequiredValidator', '', 'SCALE');

		$tMap->addValidator('SYSTEM_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SYSTEM_ID');

		$tMap->addValidator('SYSTEM_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SYSTEM_ID');

		$tMap->addValidator('SYSTEM_ID', 'required', 'propel.validator.RequiredValidator', '', 'SYSTEM_ID');

		$tMap->addValidator('TRANSLATIONX', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONX');

		$tMap->addValidator('TRANSLATIONX', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONX');

		$tMap->addValidator('TRANSLATIONX', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONX');

		$tMap->addValidator('TRANSLATIONXUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONXUNIT_ID');

		$tMap->addValidator('TRANSLATIONXUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONXUNIT_ID');

		$tMap->addValidator('TRANSLATIONXUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONXUNIT_ID');

		$tMap->addValidator('TRANSLATIONY', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONY');

		$tMap->addValidator('TRANSLATIONY', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONY');

		$tMap->addValidator('TRANSLATIONY', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONY');

		$tMap->addValidator('TRANSLATIONYUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONYUNIT_ID');

		$tMap->addValidator('TRANSLATIONYUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONYUNIT_ID');

		$tMap->addValidator('TRANSLATIONYUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONYUNIT_ID');

		$tMap->addValidator('TRANSLATIONZ', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONZ');

		$tMap->addValidator('TRANSLATIONZ', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONZ');

		$tMap->addValidator('TRANSLATIONZ', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONZ');

		$tMap->addValidator('TRANSLATIONZUNIT_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'TRANSLATIONZUNIT_ID');

		$tMap->addValidator('TRANSLATIONZUNIT_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'TRANSLATIONZUNIT_ID');

		$tMap->addValidator('TRANSLATIONZUNIT_ID', 'required', 'propel.validator.RequiredValidator', '', 'TRANSLATIONZUNIT_ID');

	} // doBuild()

} // CoordinateSpaceMapBuilder
