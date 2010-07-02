<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SIMILITUDE_LAW' table to 'NEEScentral' DatabaseMap object.
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
class SimilitudeLawMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SimilitudeLawMapBuilder';

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

		$tMap = $this->dbMap->addTable('SIMILITUDE_LAW');
		$tMap->setPhpName('SimilitudeLaw');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SIMILITUDE_LAW_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMPUTE_EQUATION', 'ComputeEquation', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DEPENDENCE', 'Dependence', 'string', CreoleTypes::VARCHAR, false, 48);

		$tMap->addColumn('DESCRIPTION', 'Description', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addColumn('DISPLAY_EQUATION', 'DisplayEquation', 'string', CreoleTypes::VARCHAR, false, 512);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addForeignKey('SIMILITUDE_LAW_GROUP_ID', 'SimilitudeLawGroupId', 'double', CreoleTypes::NUMERIC, 'SIMILITUDE_LAW_GROUP', 'ID', false, 22);

		$tMap->addColumn('SYMBOL', 'Symbol', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addColumn('SYSTEM_NAME', 'SystemName', 'string', CreoleTypes::VARCHAR, false, 128);

		$tMap->addColumn('UNIT_DESCRIPTION', 'UnitDescription', 'string', CreoleTypes::VARCHAR, false, 256);

		$tMap->addValidator('COMPUTE_EQUATION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COMPUTE_EQUATION');

		$tMap->addValidator('COMPUTE_EQUATION', 'required', 'propel.validator.RequiredValidator', '', 'COMPUTE_EQUATION');

		$tMap->addValidator('DEPENDENCE', 'maxLength', 'propel.validator.MaxLengthValidator', '48', 'DEPENDENCE');

		$tMap->addValidator('DEPENDENCE', 'required', 'propel.validator.RequiredValidator', '', 'DEPENDENCE');

		$tMap->addValidator('DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'DESCRIPTION');

		$tMap->addValidator('DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'DESCRIPTION');

		$tMap->addValidator('DISPLAY_EQUATION', 'maxLength', 'propel.validator.MaxLengthValidator', '512', 'DISPLAY_EQUATION');

		$tMap->addValidator('DISPLAY_EQUATION', 'required', 'propel.validator.RequiredValidator', '', 'DISPLAY_EQUATION');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'NAME');

		$tMap->addValidator('NAME', 'required', 'propel.validator.RequiredValidator', '', 'NAME');

		$tMap->addValidator('SIMILITUDE_LAW_GROUP_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SIMILITUDE_LAW_GROUP_ID');

		$tMap->addValidator('SIMILITUDE_LAW_GROUP_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SIMILITUDE_LAW_GROUP_ID');

		$tMap->addValidator('SIMILITUDE_LAW_GROUP_ID', 'required', 'propel.validator.RequiredValidator', '', 'SIMILITUDE_LAW_GROUP_ID');

		$tMap->addValidator('SYMBOL', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'SYMBOL');

		$tMap->addValidator('SYMBOL', 'required', 'propel.validator.RequiredValidator', '', 'SYMBOL');

		$tMap->addValidator('SYSTEM_NAME', 'maxLength', 'propel.validator.MaxLengthValidator', '128', 'SYSTEM_NAME');

		$tMap->addValidator('SYSTEM_NAME', 'required', 'propel.validator.RequiredValidator', '', 'SYSTEM_NAME');

		$tMap->addValidator('UNIT_DESCRIPTION', 'maxLength', 'propel.validator.MaxLengthValidator', '256', 'UNIT_DESCRIPTION');

		$tMap->addValidator('UNIT_DESCRIPTION', 'required', 'propel.validator.RequiredValidator', '', 'UNIT_DESCRIPTION');

	} // doBuild()

} // SimilitudeLawMapBuilder
