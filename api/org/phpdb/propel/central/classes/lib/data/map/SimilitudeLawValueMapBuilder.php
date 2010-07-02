<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SIMILITUDE_LAW_VALUE' table to 'NEEScentral' DatabaseMap object.
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
class SimilitudeLawValueMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.data.map.SimilitudeLawValueMapBuilder';

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

		$tMap = $this->dbMap->addTable('SIMILITUDE_LAW_VALUE');
		$tMap->setPhpName('SimilitudeLawValue');

		$tMap->setUseIdGenerator(true);

		$tMap->setPrimaryKeyMethodInfo('SIMILITUDE_LAW_VALUE_SEQ');

		$tMap->addPrimaryKey('ID', 'Id', 'double', CreoleTypes::NUMERIC, true, 22);

		$tMap->addColumn('COMMENTS', 'Comments', 'string', CreoleTypes::VARCHAR, false, 1020);

		$tMap->addForeignKey('EXPID', 'ExperimentId', 'double', CreoleTypes::NUMERIC, 'EXPERIMENT', 'EXPID', false, 22);

		$tMap->addForeignKey('SIMILITUDE_LAW_ID', 'SimilitudeLawId', 'double', CreoleTypes::NUMERIC, 'SIMILITUDE_LAW', 'ID', false, 22);

		$tMap->addColumn('VALUE', 'Value', 'double', CreoleTypes::FLOAT, false, 22);

		$tMap->addValidator('COMMENTS', 'maxLength', 'propel.validator.MaxLengthValidator', '1020', 'COMMENTS');

		$tMap->addValidator('COMMENTS', 'required', 'propel.validator.RequiredValidator', '', 'COMMENTS');

		$tMap->addValidator('EXPID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'EXPID');

		$tMap->addValidator('EXPID', 'required', 'propel.validator.RequiredValidator', '', 'EXPID');

		$tMap->addValidator('ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'ID');

		$tMap->addValidator('ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'ID');

		$tMap->addValidator('ID', 'required', 'propel.validator.RequiredValidator', '', 'ID');

		$tMap->addValidator('ID', 'unique', 'propel.validator.UniqueValidator', '', 'ID');

		$tMap->addValidator('SIMILITUDE_LAW_ID', 'maxValue', 'propel.validator.MaxValueValidator', '', 'SIMILITUDE_LAW_ID');

		$tMap->addValidator('SIMILITUDE_LAW_ID', 'notMatch', 'propel.validator.NotMatchValidator', '', 'SIMILITUDE_LAW_ID');

		$tMap->addValidator('SIMILITUDE_LAW_ID', 'required', 'propel.validator.RequiredValidator', '', 'SIMILITUDE_LAW_ID');

		$tMap->addValidator('VALUE', 'maxValue', 'propel.validator.MaxValueValidator', '', 'VALUE');

		$tMap->addValidator('VALUE', 'notMatch', 'propel.validator.NotMatchValidator', '', 'VALUE');

		$tMap->addValidator('VALUE', 'required', 'propel.validator.RequiredValidator', '', 'VALUE');

	} // doBuild()

} // SimilitudeLawValueMapBuilder
